<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipient extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'notes',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }
    
    public static function getValidCategories(): array
    {
        return [
            'fuqara' => 'Fuqara (Poor)',
            'masakin' => 'Masakin (Needy)',
            'zakat_workers' => 'Zakat Workers',
            'new_muslims' => 'New Muslims',
            'slaves' => 'Slaves',
            'debtors' => 'Debtors',
            'fi_sabilillah' => 'Fi Sabilillah (In the cause of Allah)',
            'travelers' => 'Travelers',
        ];
    }
}
