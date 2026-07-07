<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 'category_id', 'name', 'region', 'base_price', 'stock', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function providerProducts(): HasMany
    {
        return $this->hasMany(ProviderProduct::class);
    }

    public function activeProviderProducts()
    {
        return $this->providerProducts()
            ->where('is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('is_active', true))
            ->join('providers', 'providers.id', '=', 'provider_products.provider_id')
            ->orderBy('providers.priority', 'asc')
            ->select('provider_products.*');
    }

    public function priceForRole(string $role): float
    {
        $discounts = [
            'customer' => 0,
            'member'   => 0.02,
            'reseller' => 0.05,
            'vip'      => 0.08,
        ];

        $discountPercentage = $discounts[$role] ?? 0;

        return (float) $this->base_price * (1 - $discountPercentage);
    }
}