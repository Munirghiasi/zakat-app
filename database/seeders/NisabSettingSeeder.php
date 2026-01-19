<?php

namespace Database\Seeders;

use App\Models\NisabSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class NisabSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first admin user or create one
        $admin = User::where('is_admin', true)->first();
        
        if (!$admin) {
            $admin = User::first();
        }
        
        // Default Nisab setting (example: assuming gold price of $60/gram)
        // Nisab = 87.48 grams × gold price
        $goldPricePerGram = 60.00; // Update this with current market price
        $nisabValue = $goldPricePerGram * 87.48;
        
        NisabSetting::create([
            'gold_price_per_gram' => $goldPricePerGram,
            'silver_price_per_gram' => null,
            'nisab_value' => $nisabValue,
            'source' => 'Default System Setting',
            'effective_from' => now()->startOfYear(),
            'is_active' => true,
            'updated_by' => $admin?->id,
        ]);
    }
}
