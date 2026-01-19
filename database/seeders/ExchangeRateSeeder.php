<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('is_admin', true)->first();
        
        $rates = [
            ['currency' => 'EUR', 'rate' => 0.92, 'name' => 'Euro'],
            ['currency' => 'GBP', 'rate' => 0.79, 'name' => 'British Pound'],
            ['currency' => 'SAR', 'rate' => 3.75, 'name' => 'Saudi Riyal'],
            ['currency' => 'AED', 'rate' => 3.67, 'name' => 'UAE Dirham'],
            ['currency' => 'PKR', 'rate' => 278.50, 'name' => 'Pakistani Rupee'],
            ['currency' => 'INR', 'rate' => 83.00, 'name' => 'Indian Rupee'],
            ['currency' => 'MYR', 'rate' => 4.70, 'name' => 'Malaysian Ringgit'],
            ['currency' => 'IDR', 'rate' => 15650.00, 'name' => 'Indonesian Rupiah'],
            ['currency' => 'TRY', 'rate' => 32.00, 'name' => 'Turkish Lira'],
            ['currency' => 'EGP', 'rate' => 30.90, 'name' => 'Egyptian Pound'],
            ['currency' => 'BHD', 'rate' => 0.377, 'name' => 'Bahraini Dinar'],
            ['currency' => 'KWD', 'rate' => 0.307, 'name' => 'Kuwaiti Dinar'],
            ['currency' => 'OMR', 'rate' => 0.385, 'name' => 'Omani Rial'],
            ['currency' => 'QAR', 'rate' => 3.64, 'name' => 'Qatari Riyal'],
        ];
        
        foreach ($rates as $rateData) {
            ExchangeRate::create([
                'currency' => $rateData['currency'],
                'rate' => $rateData['rate'],
                'effective_from' => now()->startOfYear(),
                'is_active' => true,
                'source' => 'Default System Setting',
                'updated_by' => $admin?->id,
            ]);
        }
    }
}
