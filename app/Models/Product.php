<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', 'category_id', 'name', 'region', 'base_price', 'stock', 'is_active', 'sort_order',
        'margin_type', 'margin_value', 'auto_price',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'margin_value' => 'decimal:2',
        'auto_price' => 'boolean',
        'is_active'  => 'boolean',
    ];

    protected $appends = ['flash_sale_price', 'flash_sale_ends_at'];

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
            ->where('provider_products.is_active', true)
            ->whereHas('provider', fn ($q) => $q->where('providers.is_active', true))
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

    public function flashSales(): BelongsToMany
    {
        return $this->belongsToMany(FlashSale::class, 'flash_sale_products');
    }

    private ?FlashSale $cachedActiveFlashSale = null;
    private bool $activeFlashSaleResolved = false;

    public function activeFlashSale(): ?FlashSale
    {
        if ($this->activeFlashSaleResolved) {
            return $this->cachedActiveFlashSale;
        }

        $this->cachedActiveFlashSale = $this->flashSales()
            ->running()
            ->get()
            ->sortByDesc(fn (FlashSale $fs) => $this->calculateDiscount($fs))
            ->first();

        $this->activeFlashSaleResolved = true;

        return $this->cachedActiveFlashSale;
    }

    public function flashSalePrice(?string $role = null): ?float
    {
        $flashSale = $this->activeFlashSale();

        if (! $flashSale) {
            return null;
        }

        $base = $role ? $this->priceForRole($role) : (float) $this->base_price;

        return max($base - $this->calculateDiscount($flashSale, $base), 0);
    }

    private function calculateDiscount(FlashSale $flashSale, ?float $base = null): float
    {
        $base ??= (float) $this->base_price;

        return $flashSale->discount_type === 'percentage'
            ? $base * ((float) $flashSale->discount_value / 100)
            : (float) $flashSale->discount_value;
    }

    public function getFlashSalePriceAttribute(): ?string
    {
        $price = $this->flashSalePrice();

        return $price !== null ? number_format($price, 2, '.', '') : null;
    }

    public function getFlashSaleEndsAtAttribute(): ?string
    {
        return $this->activeFlashSale()?->end_at?->toIso8601String();
    }
}