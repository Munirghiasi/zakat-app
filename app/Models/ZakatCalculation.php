<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZakatCalculation extends Model
{
    protected $fillable = [
        'user_id',
        'zakat_year_id',
        'total_assets',
        'total_debts',
        'net_zakatable_wealth',
        'nisab',
        'zakat_due',
        'zakat_paid',
        'zakat_remaining',
    ];
    
    protected function casts(): array
    {
        return [
            'total_assets' => 'decimal:2',
            'total_debts' => 'decimal:2',
            'net_zakatable_wealth' => 'decimal:2',
            'nisab' => 'decimal:2',
            'zakat_due' => 'decimal:2',
            'zakat_paid' => 'decimal:2',
            'zakat_remaining' => 'decimal:2',
        ];
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function zakatYear(): BelongsTo
    {
        return $this->belongsTo(ZakatYear::class);
    }
}
