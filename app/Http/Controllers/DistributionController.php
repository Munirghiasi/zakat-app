<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Recipient;
use App\Models\ZakatCalculation;
use App\Models\ZakatYear;
use App\Services\ZakatCalculationService;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DistributionController extends Controller
{
    use Auditable;
    
    protected $zakatService;
    
    public function __construct(ZakatCalculationService $zakatService)
    {
        $this->zakatService = $zakatService;
    }
    
    public function index()
    {
        $user = Auth::user();
        $currentYear = ZakatYear::where('user_id', $user->id)->where('year', date('Y'))->first();
        
        if (!$currentYear) {
            return redirect()->route('dashboard')->with('error', 'Please create a Zakat year first.');
        }
        
        $distributions = Distribution::where('user_id', $user->id)
            ->where('zakat_year_id', $currentYear->id)
            ->with('recipient')
            ->orderBy('distribution_date', 'desc')
            ->get();
        
        $calculation = ZakatCalculation::where('user_id', $user->id)
            ->where('zakat_year_id', $currentYear->id)
            ->first();
        
        return view('distributions.index', compact('distributions', 'currentYear', 'calculation'));
    }
    
    public function create()
    {
        $user = Auth::user();
        $currentYear = ZakatYear::where('user_id', $user->id)->where('year', date('Y'))->first();
        
        if (!$currentYear) {
            return redirect()->route('dashboard')->with('error', 'Please create a Zakat year first.');
        }
        
        if ($currentYear->is_locked) {
            return back()->with('error', 'Cannot add distributions to a locked year.');
        }
        
        $recipients = Recipient::where('user_id', $user->id)->orderBy('name')->get();
        $calculation = ZakatCalculation::where('user_id', $user->id)
            ->where('zakat_year_id', $currentYear->id)
            ->first();
        
        return view('distributions.create', compact('recipients', 'currentYear', 'calculation'));
    }
    
    public function store(Request $request)
    {
        $user = Auth::user();
        $currentYear = ZakatYear::where('user_id', $user->id)->where('year', date('Y'))->first();
        
        if (!$currentYear || $currentYear->is_locked) {
            return back()->with('error', 'Cannot add distributions to a locked year.');
        }
        
        $calculation = ZakatCalculation::where('user_id', $user->id)
            ->where('zakat_year_id', $currentYear->id)
            ->first();
        
        if (!$calculation) {
            $calculation = $this->zakatService->calculateZakat($user, $currentYear);
        }
        
        $validated = $request->validate([
            'recipient_id' => 'required|exists:recipients,id',
            'amount' => 'required|numeric|min:0.01',
            'distribution_date' => 'required|date',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        // Check if recipient belongs to user
        $recipient = Recipient::findOrFail($validated['recipient_id']);
        if ($recipient->user_id !== $user->id) {
            abort(403);
        }
        
        // Check if amount exceeds remaining Zakat
        if ($validated['amount'] > $calculation->zakat_remaining) {
            return back()->with('error', 'Distribution amount exceeds remaining Zakat.');
        }
        
        // Handle receipt upload
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }
        
        $distribution = Distribution::create([
            'user_id' => $user->id,
            'zakat_year_id' => $currentYear->id,
            'recipient_id' => $validated['recipient_id'],
            'amount' => $validated['amount'],
            'category' => $recipient->category,
            'distribution_date' => $validated['distribution_date'],
            'notes' => $validated['notes'] ?? null,
            'receipt_path' => $receiptPath,
        ]);
        
        $this->logAudit('created', $distribution, null, $distribution->toArray());
        
        // Recalculate Zakat
        $this->zakatService->calculateZakat($user, $currentYear);
        
        return redirect()->route('distributions.index')->with('success', 'Distribution recorded successfully.');
    }
    
    public function edit(Distribution $distribution)
    {
        $user = Auth::user();
        
        if ($distribution->user_id !== $user->id) {
            abort(403);
        }
        
        $currentYear = $distribution->zakatYear;
        
        if ($currentYear->is_locked) {
            return back()->with('error', 'Cannot edit distributions in a locked year.');
        }
        
        $recipients = Recipient::where('user_id', $user->id)->orderBy('name')->get();
        $calculation = ZakatCalculation::where('user_id', $user->id)
            ->where('zakat_year_id', $currentYear->id)
            ->first();
        
        return view('distributions.edit', compact('distribution', 'recipients', 'currentYear', 'calculation'));
    }
    
    public function update(Request $request, Distribution $distribution)
    {
        $user = Auth::user();
        
        if ($distribution->user_id !== $user->id) {
            abort(403);
        }
        
        $currentYear = $distribution->zakatYear;
        
        if ($currentYear->is_locked) {
            return back()->with('error', 'Cannot update distributions in a locked year.');
        }
        
        $calculation = ZakatCalculation::where('user_id', $user->id)
            ->where('zakat_year_id', $currentYear->id)
            ->first();
        
        $oldValues = $distribution->toArray();
        
        $validated = $request->validate([
            'recipient_id' => 'required|exists:recipients,id',
            'amount' => 'required|numeric|min:0.01',
            'distribution_date' => 'required|date',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        // Check if recipient belongs to user
        $recipient = Recipient::findOrFail($validated['recipient_id']);
        if ($recipient->user_id !== $user->id) {
            abort(403);
        }
        
        // Check if new amount exceeds remaining Zakat (add back old amount)
        $availableAmount = $calculation->zakat_remaining + $distribution->amount;
        if ($validated['amount'] > $availableAmount) {
            return back()->with('error', 'Distribution amount exceeds available Zakat.');
        }
        
        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            // Delete old receipt
            if ($distribution->receipt_path) {
                Storage::disk('public')->delete($distribution->receipt_path);
            }
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
            $validated['receipt_path'] = $receiptPath;
        }
        
        $distribution->update([
            'recipient_id' => $validated['recipient_id'],
            'amount' => $validated['amount'],
            'category' => $recipient->category,
            'distribution_date' => $validated['distribution_date'],
            'notes' => $validated['notes'] ?? null,
            'receipt_path' => $validated['receipt_path'] ?? $distribution->receipt_path,
        ]);
        
        $this->logAudit('updated', $distribution, $oldValues, $distribution->toArray());
        
        // Recalculate Zakat
        $this->zakatService->calculateZakat($user, $currentYear);
        
        return redirect()->route('distributions.index')->with('success', 'Distribution updated successfully.');
    }
    
    public function destroy(Distribution $distribution)
    {
        $user = Auth::user();
        
        if ($distribution->user_id !== $user->id) {
            abort(403);
        }
        
        $currentYear = $distribution->zakatYear;
        
        if ($currentYear->is_locked) {
            return back()->with('error', 'Cannot delete distributions in a locked year.');
        }
        
        $oldValues = $distribution->toArray();
        
        // Delete receipt file
        if ($distribution->receipt_path) {
            Storage::disk('public')->delete($distribution->receipt_path);
        }
        
        $distribution->delete();
        
        $this->logAudit('deleted', (object)['id' => $oldValues['id'], 'model_type' => Distribution::class], $oldValues, null);
        
        // Recalculate Zakat
        $this->zakatService->calculateZakat($user, $currentYear);
        
        return redirect()->route('distributions.index')->with('success', 'Distribution deleted successfully.');
    }
}
