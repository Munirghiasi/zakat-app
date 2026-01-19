<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Distribution extends Model
{
    protected $fillable = [
        'user_id',
        'zakat_year_id',
        'recipient_id',
        'amount',
        'category',
        'distribution_date',
        'notes',
        'receipt_path',
    ];
    
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'distribution_date' => 'date',
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
    
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Recipient::class);
    }
}
