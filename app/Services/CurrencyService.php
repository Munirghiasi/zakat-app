<?php

namespace App\Services;

use App\Models\ExchangeRate;
use App\Models\User;

class CurrencyService
{
    /**
     * Get base currency (USD)
     */
    public function getBaseCurrency(): string
    {
        return 'USD';
    }
    
    /**
     * Get supported currencies
     */
    public function getSupportedCurrencies(): array
    {
        return [
            'USD' => ['name' => 'US Dollar', 'symbol' => '$'],
            'EUR' => ['name' => 'Euro', 'symbol' => '€'],
            'GBP' => ['name' => 'British Pound', 'symbol' => '£'],
            'SAR' => ['name' => 'Saudi Riyal', 'symbol' => '﷼'],
            'AED' => ['name' => 'UAE Dirham', 'symbol' => 'د.إ'],
            'PKR' => ['name' => 'Pakistani Rupee', 'symbol' => '₨'],
            'INR' => ['name' => 'Indian Rupee', 'symbol' => '₹'],
            'MYR' => ['name' => 'Malaysian Ringgit', 'symbol' => 'RM'],
            'IDR' => ['name' => 'Indonesian Rupiah', 'symbol' => 'Rp'],
            'TRY' => ['name' => 'Turkish Lira', 'symbol' => '₺'],
            'EGP' => ['name' => 'Egyptian Pound', 'symbol' => '£'],
            'BHD' => ['name' => 'Bahraini Dinar', 'symbol' => '.د.ب'],
            'KWD' => ['name' => 'Kuwaiti Dinar', 'symbol' => 'د.ك'],
            'OMR' => ['name' => 'Omani Rial', 'symbol' => '﷼'],
            'QAR' => ['name' => 'Qatari Riyal', 'symbol' => '﷼'],
        ];
    }
    
    /**
     * Convert amount from base currency to target currency
     */
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        
        // Get exchange rates
        $fromRate = $this->getExchangeRate($fromCurrency);
        $toRate = $this->getExchangeRate($toCurrency);
        
        // Convert to base currency first, then to target currency
        $baseAmount = $amount / $fromRate;
        return $baseAmount * $toRate;
    }
    
    /**
     * Convert amount to user's preferred currency
     */
    public function convertToUserCurrency(float $amount, ?User $user = null): float
    {
        if (!$user) {
            $user = auth()->user();
        }
        
        if (!$user || !$user->currency) {
            return $amount; // Return as-is if no currency preference
        }
        
        return $this->convert($amount, $this->getBaseCurrency(), $user->currency);
    }
    
    /**
     * Format amount with currency symbol
     */
    public function format(float $amount, ?string $currency = null, ?User $user = null): string
    {
        if (!$currency) {
            $currency = $user?->currency ?? $this->getBaseCurrency();
        }
        
        $currencies = $this->getSupportedCurrencies();
        $symbol = $currencies[$currency]['symbol'] ?? $currency;
        
        return $symbol . ' ' . number_format($amount, 2);
    }
    
    /**
     * Get exchange rate for a currency
     */
    public function getExchangeRate(string $currency): float
    {
        if ($currency === $this->getBaseCurrency()) {
            return 1.0;
        }
        
        $rate = ExchangeRate::where('currency', $currency)
            ->where('is_active', true)
            ->orderBy('effective_from', 'desc')
            ->first();
        
        if ($rate) {
            return $rate->rate;
        }
        
        // Return default rate if not found (you might want to fetch from API)
        return $this->getDefaultRates()[$currency] ?? 1.0;
    }
    
    /**
     * Get default exchange rates (fallback)
     */
    private function getDefaultRates(): array
    {
        return [
            'EUR' => 0.92,
            'GBP' => 0.79,
            'SAR' => 3.75,
            'AED' => 3.67,
            'PKR' => 278.50,
            'INR' => 83.00,
            'MYR' => 4.70,
            'IDR' => 15650.00,
            'TRY' => 32.00,
            'EGP' => 30.90,
            'BHD' => 0.377,
            'KWD' => 0.307,
            'OMR' => 0.385,
            'QAR' => 3.64,
        ];
    }
}

