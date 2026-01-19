<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Debt extends Model
{
    protected $fillable = [
        'user_id',
        'zakat_year_id',
        'type',
        'amount',
        'description',
    ];
    
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
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
