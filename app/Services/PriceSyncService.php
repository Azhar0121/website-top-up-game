<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * PriceSyncService - jantung dari "harga otomatis mengikuti modal" (PRD 4.3).
 *
 * Alur pemakaian:
 * 1. Cost price berubah (baik manual, atau nanti hasil sync dari API provider asli
 *    lewat DigiflazzService::getPriceList())
 * 2. Jalankan PriceSyncService::syncAll() atau ->sync($product)
 * 3. base_price otomatis dihitung ulang dari cost_price provider ter-prioritas + margin
 *
 * Kalau admin set 'auto_price' = false pada suatu produk, produk itu DILEWATI saat
 * sinkronisasi - artinya admin sengaja override harga manual untuk produk tersebut
 * (misal untuk strategi harga promosi khusus).
 */
class PriceSyncService
{
    /**
     * Hitung & update base_price satu produk, berdasarkan cost_price dari
     * provider dengan priority tertinggi (paling utama) yang masih aktif.
     *
     * Return null kalau produk tidak punya provider aktif (tidak bisa dihitung),
     * atau kalau auto_price dimatikan (sengaja dilewati).
     */
    public function sync(Product $product): ?array
    {
        if (! $product->auto_price) {
            return null; // admin sengaja pakai harga manual untuk produk ini
        }

        // Ambil cost_price dari provider paling prioritas (bukan rata-rata/termurah,
        // karena provider priority 1 itu yang paling sering dipakai transaksi asli)
        $primaryProviderProduct = $product->activeProviderProducts()->first();

        if (! $primaryProviderProduct) {
            return null; // tidak ada provider aktif untuk produk ini
        }

        $costPrice = (float) $primaryProviderProduct->cost_price;
        $oldPrice = (float) $product->base_price;

        $newPrice = $product->margin_type === 'fixed'
            ? $costPrice + (float) $product->margin_value
            : $costPrice * (1 + ((float) $product->margin_value / 100));

        // Bulatkan ke ratusan terdekat ke atas - kebiasaan umum toko top up
        // supaya harga tidak aneh (misal Rp22.517 jadi Rp22.600)
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

    /**
     * Jalankan sync untuk SEMUA produk aktif sekaligus.
     * Dipakai oleh command `php artisan products:sync-prices` dan endpoint admin.
     */
    public function syncAll(): Collection
    {
        return Product::where('is_active', true)
            ->get()
            ->map(fn (Product $product) => $this->sync($product))
            ->filter()
            ->values();
    }
}