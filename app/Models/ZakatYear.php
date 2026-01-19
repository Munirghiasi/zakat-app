<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZakatYear extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'start_date',
        'end_date',
        'is_locked',
    ];
    
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_locked' => 'boolean',
        ];
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
    
    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }
    
    public function zakatCalculation(): HasMany
    {
        return $this->hasMany(ZakatCalculation::class);
    }
    
    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }
}
