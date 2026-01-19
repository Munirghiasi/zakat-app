<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $fillable = [
        'user_id',
        'zakat_year_id',
        'type',
        'category',
        'amount',
        'quantity',
        'description',
    ];
    
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'quantity' => 'decimal:4',
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
