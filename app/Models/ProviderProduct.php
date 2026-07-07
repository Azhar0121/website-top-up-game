<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id', 'product_id', 'provider_sku_code', 'cost_price', 'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}