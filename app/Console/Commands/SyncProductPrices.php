<?php

namespace App\Console\Commands;

use App\Services\PriceSyncService;
use Illuminate\Console\Command;

/**
 * Jalankan manual: php artisan products:sync-prices
 *
 * Nanti kalau mau otomatis terjadwal (misal tiap jam), tambahkan ke
 * routes/console.php:
 *   Schedule::command('products:sync-prices')->hourly();
 */
class SyncProductPrices extends Command
{
    protected $signature = 'products:sync-prices';
    protected $description = 'Hitung ulang base_price semua produk dari cost_price provider + margin';

    public function handle(PriceSyncService $service): int
    {
        $this->info('Menyinkronkan harga produk...');

        $results = $service->syncAll();

        if ($results->isEmpty()) {
            $this->warn('Tidak ada produk yang disinkronkan (cek apakah produk punya provider aktif & auto_price = true).');
            return self::SUCCESS;
        }

        $this->table(
            ['Produk', 'Modal', 'Harga Lama', 'Harga Baru', 'Margin', 'Berubah?'],
            $results->map(fn ($r) => [
                $r['product_name'],
                'Rp' . number_format($r['cost_price'], 0, ',', '.'),
                'Rp' . number_format($r['old_price'], 0, ',', '.'),
                'Rp' . number_format($r['new_price'], 0, ',', '.'),
                $r['margin_type'] === 'fixed' ? 'Rp' . number_format($r['margin_value'], 0, ',', '.') : $r['margin_value'] . '%',
                $r['changed'] ? '✅ Ya' : '- Tidak',
            ])->toArray()
        );

        $changedCount = $results->where('changed', true)->count();
        $this->info("Selesai. {$changedCount} dari {$results->count()} produk mengalami perubahan harga.");

        return self::SUCCESS;
    }
}