<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'base_url', 'api_key', 'api_secret', 'priority', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'api_key'    => 'encrypted',
        'api_secret' => 'encrypted',
    ];

    public function providerProducts(): HasMany
    {
        return $this->hasMany(ProviderProduct::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActiveByPriority($query)
    {
        return $query->where('is_active', true)->orderBy('priority', 'asc');
    }
}