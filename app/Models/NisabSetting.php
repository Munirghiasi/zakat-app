<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NisabSetting extends Model
{
    protected $fillable = [
        'gold_price_per_gram',
        'silver_price_per_gram',
        'nisab_value',
        'source',
        'effective_from',
        'is_active',
        'updated_by',
    ];
    
    protected function casts(): array
    {
        return [
            'gold_price_per_gram' => 'decimal:2',
            'silver_price_per_gram' => 'decimal:2',
            'nisab_value' => 'decimal:2',
            'effective_from' => 'date',
            'is_active' => 'boolean',
        ];
    }
    
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public static function getActive()
    {
        return static::where('is_active', true)->orderBy('effective_from', 'desc')->first();
    }
}
