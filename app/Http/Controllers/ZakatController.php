<?php

namespace App\Http\Controllers;

use App\Models\ZakatYear;
use App\Services\ZakatCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ZakatController extends Controller
{
    protected $zakatService;
    
    public function __construct(ZakatCalculationService $zakatService)
    {
        $this->zakatService = $zakatService;
    }
    
    public function summary()
    {
        $user = Auth::user();
        $currentYear = ZakatYear::where('user_id', $user->id)->where('year', date('Y'))->first();
        
        if (!$currentYear) {
            return redirect()->route('dashboard')->with('error', 'Please create a Zakat year first.');
        }
        
        $calculation = $this->zakatService->calculateZakat($user, $currentYear);
        
        return view('zakat.summary', compact('calculation', 'currentYear'));
    }
    
    public function recalculate()
    {
        $user = Auth::user();
        $calculation = $this->zakatService->recalculateCurrentYear($user);
        
        return redirect()->route('zakat.summary')->with('success', 'Zakat recalculated successfully.');
    }
}
