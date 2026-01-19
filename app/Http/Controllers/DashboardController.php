<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\NisabSetting;
use App\Models\ZakatYear;
use App\Services\ZakatCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $zakatService;
    
    public function __construct(ZakatCalculationService $zakatService)
    {
        $this->zakatService = $zakatService;
    }
    
    public function index()
    {
        $user = Auth::user();
        
        // Get or create current year
        $currentYear = ZakatYear::firstOrCreate(
            [
                'user_id' => $user->id,
                'year' => date('Y'),
            ],
            [
                'start_date' => date('Y') . '-01-01',
                'end_date' => date('Y') . '-12-31',
                'is_locked' => false,
            ]
        );
        
        // Calculate Zakat
        $calculation = $this->zakatService->calculateZakat($user, $currentYear);
        
        // Get recent assets and debts
        $recentAssets = $user->assets()->where('zakat_year_id', $currentYear->id)->latest()->take(5)->get();
        $recentDebts = $user->debts()->where('zakat_year_id', $currentYear->id)->latest()->take(5)->get();
        
        // Get recent distributions
        $recentDistributions = $user->distributions()->where('zakat_year_id', $currentYear->id)->latest()->take(5)->get();
        
        // Get asset breakdown by category for zakat calculation display
        $nisabSetting = \App\Models\NisabSetting::getActive();
        $assetBreakdown = $this->getAssetBreakdownForZakat($user, $currentYear, $nisabSetting, $calculation);
        
        return view('dashboard', compact('calculation', 'currentYear', 'recentAssets', 'recentDebts', 'recentDistributions', 'assetBreakdown'));
    }
    
    /**
     * Get asset breakdown with zakat calculations
     */
    private function getAssetBreakdownForZakat($user, $zakatYear, $nisabSetting, $calculation)
    {
        $zakatableAssets = Asset::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->where('type', 'zakatable')
            ->selectRaw('category, SUM(amount) as total_amount, SUM(quantity) as total_quantity')
            ->groupBy('category')
            ->get();
        
        $breakdown = [];
        
        foreach ($zakatableAssets as $asset) {
            $categoryTotal = (float)$asset->total_amount;
            
            // Handle gold and silver with price conversion
            if ($asset->category === 'gold' && $asset->total_quantity && $nisabSetting) {
                $categoryTotal = (float)$asset->total_quantity * $nisabSetting->gold_price_per_gram;
            } elseif ($asset->category === 'silver' && $asset->total_quantity && $nisabSetting && $nisabSetting->silver_price_per_gram) {
                $categoryTotal = (float)$asset->total_quantity * $nisabSetting->silver_price_per_gram;
            }
            
            // Calculate zakat for this category (2.5% of the category total)
            // Only if net wealth is above Nisab
            $zakatAmount = 0;
            if ($calculation && $calculation->net_zakatable_wealth >= $calculation->nisab) {
                // Proportionally calculate zakat based on this category's share
                if ($calculation->total_assets > 0) {
                    $categoryShare = $categoryTotal / $calculation->total_assets;
                    $zakatAmount = $calculation->zakat_due * $categoryShare;
                }
            }
            
            $breakdown[] = [
                'category' => ucfirst(str_replace('_', ' ', $asset->category)),
                'total' => $categoryTotal,
                'zakat_percentage' => 2.5,
                'zakat_amount' => $zakatAmount,
                'remaining_after_zakat' => $categoryTotal - $zakatAmount,
            ];
        }
        
        return $breakdown;
    }
}
