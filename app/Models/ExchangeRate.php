<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    protected $fillable = [
        'currency',
        'rate',
        'effective_from',
        'is_active',
        'source',
        'updated_by',
    ];
    
    protected function casts(): array
    {
        return [
            'rate' => 'decimal:6',
            'effective_from' => 'date',
            'is_active' => 'boolean',
        ];
    }
    
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public static function getActiveRate(string $currency): ?self
    {
        return static::where('currency', $currency)
            ->where('is_active', true)
            ->orderBy('effective_from', 'desc')
            ->first();
    }
}
