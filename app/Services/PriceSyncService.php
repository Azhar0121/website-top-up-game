<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class PriceSyncService
{
    public function sync(Product $product): ?array
    {
        if (! $product->auto_price) {
            return null; // admin sengaja pakai harga manual untuk produk ini
        }

        $primaryProviderProduct = $product->activeProviderProducts()->first();

        if (! $primaryProviderProduct) {
            return null; // tidak ada provider aktif untuk produk ini
        }

        $costPrice = (float) $primaryProviderProduct->cost_price;
        $oldPrice = (float) $product->base_price;

        $newPrice = $product->margin_type === 'fixed'
            ? $costPrice + (float) $product->margin_value
            : $costPrice * (1 + ((float) $product->margin_value / 100));

        $newPrice = ceil($newPrice / 100) * 100;

        $product->update(['base_price' => $newPrice]);

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'cost_price' => $costPrice,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'margin_type' => $product->margin_type,
            'margin_value' => $product->margin_value,
            'changed' => $oldPrice != $newPrice,
        ];
    }

    public function syncAll(): Collection
    {
        return Product::where('is_active', true)
            ->get()
            ->map(fn (Product $product) => $this->sync($product))
            ->filter()
            ->values();
    }
}