<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\ZakatYear;
use App\Services\ZakatCalculationService;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
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
        
        $debts = Debt::where('user_id', $user->id)
            ->where('zakat_year_id', $currentYear->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('debts.index', compact('debts', 'currentYear'));
    }
    
    public function create()
    {
        $user = Auth::user();
        $currentYear = ZakatYear::where('user_id', $user->id)->where('year', date('Y'))->first();
        
        if (!$currentYear) {
            return redirect()->route('dashboard')->with('error', 'Please create a Zakat year first.');
        }
        
        return view('debts.create', compact('currentYear'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:loans_due,credit_cards,rent,bills,salary_owed',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        $currentYear = ZakatYear::where('user_id', $user->id)->where('year', date('Y'))->first();
        
        if (!$currentYear || $currentYear->is_locked) {
            return back()->with('error', 'Cannot add debts to a locked year.');
        }
        
        $debt = Debt::create([
            'user_id' => $user->id,
            'zakat_year_id' => $currentYear->id,
            ...$validated,
        ]);
        
        $this->logAudit('created', $debt, null, $debt->toArray());
        
        // Recalculate Zakat
        $this->zakatService->calculateZakat($user, $currentYear);
        
        return redirect()->route('debts.index')->with('success', 'Debt added successfully.');
    }
    
    public function edit(Debt $debt)
    {
        $user = Auth::user();
        
        if ($debt->user_id !== $user->id) {
            abort(403);
        }
        
        $currentYear = $debt->zakatYear;
        
        if ($currentYear->is_locked) {
            return back()->with('error', 'Cannot edit debts in a locked year.');
        }
        
        return view('debts.edit', compact('debt', 'currentYear'));
    }
    
    public function update(Request $request, Debt $debt)
    {
        $user = Auth::user();
        
        if ($debt->user_id !== $user->id) {
            abort(403);
        }
        
        if ($debt->zakatYear->is_locked) {
            return back()->with('error', 'Cannot update debts in a locked year.');
        }
        
        $oldValues = $debt->toArray();
        
        $validated = $request->validate([
            'type' => 'required|in:loans_due,credit_cards,rent,bills,salary_owed',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $debt->update($validated);
        
        $this->logAudit('updated', $debt, $oldValues, $debt->toArray());
        
        // Recalculate Zakat
        $this->zakatService->calculateZakat($user, $debt->zakatYear);
        
        return redirect()->route('debts.index')->with('success', 'Debt updated successfully.');
    }
    
    public function destroy(Debt $debt)
    {
        $user = Auth::user();
        
        if ($debt->user_id !== $user->id) {
            abort(403);
        }
        
        if ($debt->zakatYear->is_locked) {
            return back()->with('error', 'Cannot delete debts in a locked year.');
        }
        
        $oldValues = $debt->toArray();
        $zakatYear = $debt->zakatYear;
        
        $debt->delete();
        
        $this->logAudit('deleted', (object)['id' => $oldValues['id'], 'model_type' => Debt::class], $oldValues, null);
        
        // Recalculate Zakat
        $this->zakatService->calculateZakat($user, $zakatYear);
        
        return redirect()->route('debts.index')->with('success', 'Debt deleted successfully.');
    }
}
