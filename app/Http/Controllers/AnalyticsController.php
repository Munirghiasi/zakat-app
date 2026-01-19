<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Debt;
use App\Models\Distribution;
use App\Models\ZakatCalculation;
use App\Models\ZakatYear;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    protected $currencyService;
    
    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }
    
    public function index()
    {
        $user = Auth::user();
        
        // Get current year
        $currentYear = ZakatYear::where('user_id', $user->id)
            ->where('year', date('Y'))
            ->first();
        
        if (!$currentYear) {
            return redirect()->route('dashboard')->with('error', 'Please create a Zakat year first.');
        }
        
        // Asset trends (last 12 months)
        $assetTrends = $this->getAssetTrends($user, $currentYear);
        
        // Distribution by category
        $distributionByCategory = $this->getDistributionByCategory($user, $currentYear);
        
        // Monthly distribution trends
        $monthlyDistributions = $this->getMonthlyDistributions($user, $currentYear);
        
        // Year-over-year comparison
        $yearComparison = $this->getYearComparison($user);
        
        // Asset breakdown by type
        $assetBreakdown = $this->getAssetBreakdown($user, $currentYear);
        
        // Debt breakdown
        $debtBreakdown = $this->getDebtBreakdown($user, $currentYear);
        
        // Recipient statistics
        $recipientStats = $this->getRecipientStats($user, $currentYear);
        
        return view('analytics.index', compact(
            'currentYear',
            'assetTrends',
            'distributionByCategory',
            'monthlyDistributions',
            'yearComparison',
            'assetBreakdown',
            'debtBreakdown',
            'recipientStats'
        ));
    }
    
    /**
     * Get asset trends over time
     */
    private function getAssetTrends($user, $zakatYear)
    {
        $assets = Asset::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->where('type', 'zakatable')
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $data = [];
        $labels = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthAsset = $assets->firstWhere('month', $i);
            $data[] = $monthAsset ? (float)$monthAsset->total : 0;
            $labels[] = date('M', mktime(0, 0, 0, $i, 1));
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    /**
     * Get distribution breakdown by category
     */
    private function getDistributionByCategory($user, $zakatYear)
    {
        $distributions = Distribution::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        $labels = [];
        $data = [];
        
        foreach ($distributions as $dist) {
            $labels[] = \App\Models\Recipient::getValidCategories()[$dist->category] ?? $dist->category;
            $amount = (float)$dist->total;
            // Convert to user's currency
            $amount = $this->currencyService->convertToUserCurrency($amount, $user);
            $data[] = $amount;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    /**
     * Get monthly distribution trends
     */
    private function getMonthlyDistributions($user, $zakatYear)
    {
        $distributions = Distribution::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->selectRaw('MONTH(distribution_date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $data = [];
        $labels = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthDist = $distributions->firstWhere('month', $i);
            $amount = $monthDist ? (float)$monthDist->total : 0;
            // Convert to user's currency
            $amount = $this->currencyService->convertToUserCurrency($amount, $user);
            $data[] = $amount;
            $labels[] = date('M', mktime(0, 0, 0, $i, 1));
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    /**
     * Get year-over-year comparison
     */
    private function getYearComparison($user)
    {
        $years = ZakatYear::where('user_id', $user->id)
            ->orderBy('year', 'desc')
            ->take(5)
            ->get();
        
        $labels = [];
        $zakatDue = [];
        $zakatPaid = [];
        
        foreach ($years as $year) {
            $calculation = ZakatCalculation::where('user_id', $user->id)
                ->where('zakat_year_id', $year->id)
                ->first();
            
            if ($calculation) {
                $labels[] = $year->year;
                // Convert to user's currency
                $zakatDue[] = $this->currencyService->convertToUserCurrency($calculation->zakat_due, $user);
                $zakatPaid[] = $this->currencyService->convertToUserCurrency($calculation->zakat_paid, $user);
            }
        }
        
        return [
            'labels' => array_reverse($labels),
            'zakatDue' => array_reverse($zakatDue),
            'zakatPaid' => array_reverse($zakatPaid),
        ];
    }
    
    /**
     * Get asset breakdown by type
     */
    private function getAssetBreakdown($user, $zakatYear)
    {
        $assets = Asset::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->where('type', 'zakatable')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();
        
        $labels = [];
        $data = [];
        
        foreach ($assets as $asset) {
            $labels[] = ucfirst(str_replace('_', ' ', $asset->category));
            $amount = (float)$asset->total;
            // Convert to user's currency
            $amount = $this->currencyService->convertToUserCurrency($amount, $user);
            $data[] = $amount;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    /**
     * Get debt breakdown by type
     */
    private function getDebtBreakdown($user, $zakatYear)
    {
        $debts = Debt::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->selectRaw('type, SUM(amount) as total')
            ->groupBy('type')
            ->get();
        
        $labels = [];
        $data = [];
        
        foreach ($debts as $debt) {
            $labels[] = ucfirst(str_replace('_', ' ', $debt->type));
            $amount = (float)$debt->total;
            // Convert to user's currency
            $amount = $this->currencyService->convertToUserCurrency($amount, $user);
            $data[] = $amount;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    /**
     * Get recipient statistics
     */
    private function getRecipientStats($user, $zakatYear)
    {
        $stats = Distribution::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->join('recipients', 'distributions.recipient_id', '=', 'recipients.id')
            ->selectRaw('recipients.name, COUNT(*) as count, SUM(distributions.amount) as total')
            ->groupBy('recipients.id', 'recipients.name')
            ->orderBy('total', 'desc')
            ->get();
        
        return $stats;
    }
}
