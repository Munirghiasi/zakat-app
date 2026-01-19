<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\ZakatYear;
use App\Services\ZakatCalculationService;
use App\Traits\Auditable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
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
        
        $assets = Asset::where('user_id', $user->id)
            ->where('zakat_year_id', $currentYear->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('assets.index', compact('assets', 'currentYear'));
    }
    
    public function create()
    {
        $user = Auth::user();
        $currentYear = ZakatYear::where('user_id', $user->id)->where('year', date('Y'))->first();
        
        if (!$currentYear) {
            return redirect()->route('dashboard')->with('error', 'Please create a Zakat year first.');
        }
        
        return view('assets.create', compact('currentYear'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:zakatable,non_zakatable',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $user = Auth::user();
        $currentYear = ZakatYear::where('user_id', $user->id)->where('year', date('Y'))->first();
        
        if (!$currentYear || $currentYear->is_locked) {
            return back()->with('error', 'Cannot add assets to a locked year.');
        }
        
        $asset = Asset::create([
            'user_id' => $user->id,
            'zakat_year_id' => $currentYear->id,
            ...$validated,
        ]);
        
        $this->logAudit('created', $asset, null, $asset->toArray());
        
        // Recalculate Zakat
        $this->zakatService->calculateZakat($user, $currentYear);
        
        return redirect()->route('assets.index')->with('success', 'Asset added successfully.');
    }
    
    public function edit(Asset $asset)
    {
        $user = Auth::user();
        
        if ($asset->user_id !== $user->id) {
            abort(403);
        }
        
        $currentYear = $asset->zakatYear;
        
        if ($currentYear->is_locked) {
            return back()->with('error', 'Cannot edit assets in a locked year.');
        }
        
        return view('assets.edit', compact('asset', 'currentYear'));
    }
    
    public function update(Request $request, Asset $asset)
    {
        $user = Auth::user();
        
        if ($asset->user_id !== $user->id) {
            abort(403);
        }
        
        if ($asset->zakatYear->is_locked) {
            return back()->with('error', 'Cannot update assets in a locked year.');
        }
        
        $oldValues = $asset->toArray();
        
        $validated = $request->validate([
            'type' => 'required|in:zakatable,non_zakatable',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'quantity' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $asset->update($validated);
        
        $this->logAudit('updated', $asset, $oldValues, $asset->toArray());
        
        // Recalculate Zakat
        $this->zakatService->calculateZakat($user, $asset->zakatYear);
        
        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }
    
    public function destroy(Asset $asset)
    {
        $user = Auth::user();
        
        if ($asset->user_id !== $user->id) {
            abort(403);
        }
        
        if ($asset->zakatYear->is_locked) {
            return back()->with('error', 'Cannot delete assets in a locked year.');
        }
        
        $oldValues = $asset->toArray();
        $zakatYear = $asset->zakatYear;
        
        $asset->delete();
        
        $this->logAudit('deleted', (object)['id' => $oldValues['id'], 'model_type' => Asset::class], $oldValues, null);
        
        // Recalculate Zakat
        $this->zakatService->calculateZakat($user, $zakatYear);
        
        return redirect()->route('assets.index')->with('success', 'Asset deleted successfully.');
    }
}
