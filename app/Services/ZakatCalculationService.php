<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Debt;
use App\Models\NisabSetting;
use App\Models\ZakatCalculation;
use App\Models\ZakatYear;
use App\Models\User;

class ZakatCalculationService
{
    /**
     * Calculate Zakat for a user's zakat year (Hanafi Fiqh)
     */
    public function calculateZakat(User $user, ZakatYear $zakatYear): ZakatCalculation
    {
        $nisabSetting = NisabSetting::getActive();
        
        if (!$nisabSetting) {
            throw new \Exception('No active Nisab setting found. Please configure Nisab settings in admin panel.');
        }
        
        // Get all zakatable assets for this year
        $zakatableAssets = Asset::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->where('type', 'zakatable')
            ->get();
        
        // Calculate total assets
        $totalAssets = $this->calculateTotalAssets($zakatableAssets, $nisabSetting);
        
        // Get all debts for this year
        $debts = Debt::where('user_id', $user->id)
            ->where('zakat_year_id', $zakatYear->id)
            ->get();
        
        // Calculate total debts
        $totalDebts = $debts->sum('amount');
        
        // Net zakatable wealth (Hanafi: Assets - Debts)
        // Ensure it's never negative (minimum 0)
        $netZakatableWealth = max(0, $totalAssets - $totalDebts);
        
        // Nisab value
        $nisab = $nisabSetting->nisab_value;
        
        // Calculate Zakat due (2.5% if above Nisab)
        $zakatDue = 0;
        if ($netZakatableWealth >= $nisab) {
            $zakatDue = $netZakatableWealth * 0.025;
        }
        
        // Get total distributions for this year
        $zakatPaid = $zakatYear->distributions()->sum('amount');
        $zakatRemaining = max(0, $zakatDue - $zakatPaid);
        
        // Update or create calculation
        $calculation = ZakatCalculation::updateOrCreate(
            [
                'user_id' => $user->id,
                'zakat_year_id' => $zakatYear->id,
            ],
            [
                'total_assets' => $totalAssets,
                'total_debts' => $totalDebts,
                'net_zakatable_wealth' => $netZakatableWealth,
                'nisab' => $nisab,
                'zakat_due' => $zakatDue,
                'zakat_paid' => $zakatPaid,
                'zakat_remaining' => $zakatRemaining,
            ]
        );
        
        return $calculation;
    }
    
    /**
     * Calculate total zakatable assets
     */
    private function calculateTotalAssets($assets, NisabSetting $nisabSetting): float
    {
        $total = 0;
        
        foreach ($assets as $asset) {
            switch ($asset->category) {
                case 'cash':
                case 'bank':
                case 'business_inventory':
                case 'money_owed':
                case 'crypto':
                case 'investments':
                    $total += $asset->amount;
                    break;
                    
                case 'gold':
                    if ($asset->quantity) {
                        $total += $asset->quantity * $nisabSetting->gold_price_per_gram;
                    } else {
                        $total += $asset->amount;
                    }
                    break;
                    
                case 'silver':
                    if ($asset->quantity && $nisabSetting->silver_price_per_gram) {
                        $total += $asset->quantity * $nisabSetting->silver_price_per_gram;
                    } else {
                        $total += $asset->amount;
                    }
                    break;
            }
        }
        
        return $total;
    }
    
    /**
     * Recalculate Zakat for a user's current year
     */
    public function recalculateCurrentYear(User $user): ZakatCalculation
    {
        $currentYear = ZakatYear::where('user_id', $user->id)
            ->where('year', date('Y'))
            ->first();
        
        if (!$currentYear) {
            // Create current year if it doesn't exist
            $currentYear = ZakatYear::create([
                'user_id' => $user->id,
                'year' => date('Y'),
                'start_date' => date('Y') . '-01-01',
                'end_date' => date('Y') . '-12-31',
                'is_locked' => false,
            ]);
        }
        
        return $this->calculateZakat($user, $currentYear);
    }
}

