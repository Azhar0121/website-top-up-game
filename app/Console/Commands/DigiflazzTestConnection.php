<?php

namespace App\Console\Commands;

use App\Models\Provider;
use App\Providers\DigiflazzService;
use Illuminate\Console\Command;

class DigiflazzTestConnection extends Command
{
    protected $signature = 'digiflazz:test-connection';
    protected $description = 'Validasi kredensial Digiflazz dengan cek saldo & daftar harga (tanpa transaksi)';

    public function handle(): int
    {
        $provider = Provider::where('code', 'digiflazz')->first();

        if (! $provider) {
            $this->error('Provider dengan code "digiflazz" tidak ditemukan di database. Jalankan seeder dulu.');
            return self::FAILURE;
        }

        $this->info("Provider ditemukan: {$provider->name} (ID: {$provider->id})");
        $this->line("Username tersimpan: " . substr($provider->api_key, 0, 3) . str_repeat('*', max(strlen($provider->api_key) - 3, 0)));
        $this->newLine();

        $service = new DigiflazzService($provider);

        // 1. Cek Saldo
        $this->info('1. Mengecek saldo/deposit...');
        $balanceResult = $service->checkBalance();

        if ($balanceResult['success']) {
            $this->info("   ✅ BERHASIL - Saldo kamu: Rp" . number_format($balanceResult['deposit'], 0, ',', '.'));
        } else {
            $this->error('   ❌ GAGAL - Response mentah:');
            $this->line(json_encode($balanceResult['raw'], JSON_PRETTY_PRINT));
            $this->newLine();
            $this->warn('Kemungkinan penyebab: username/API Key salah, atau IP kamu belum di-whitelist di dashboard Digiflazz.');
            return self::FAILURE;
        }

        $this->newLine();

        // 2. Cek Daftar Harga
        $this->info('2. Mengecek daftar harga produk...');
        $priceListResult = $service->getPriceList();

        if ($priceListResult['success']) {
            $count = count($priceListResult['products']);
            $this->info("   ✅ BERHASIL - Total {$count} produk ditemukan di akun kamu.");

            if ($count > 0) {
                $this->newLine();
                $this->line('Contoh 5 produk pertama:');
                $this->table(
                    ['buyer_sku_code', 'product_name', 'price', 'status'],
                    collect($priceListResult['products'])->take(5)->map(fn ($p) => [
                        $p['buyer_sku_code'],
                        $p['product_name'],
                        'Rp' . number_format($p['price'], 0, ',', '.'),
                        $p['buyer_product_status'] ? 'Aktif' : 'Nonaktif',
                    ])->toArray()
                );
            }
        } else {
            $this->error('   ❌ GAGAL mengambil daftar harga.');
            $this->line(json_encode($priceListResult['raw'], JSON_PRETTY_PRINT));
        }

        $this->newLine();
        $this->info('Selesai. Kalau kedua langkah di atas ✅, kredensial Digiflazz kamu valid dan siap dipakai untuk transaksi.');
        $this->warn('Catatan: pastikan buyer_sku_code yang kamu simpan di tabel provider_products PERSIS SAMA dengan yang muncul di daftar harga di atas.');

        return self::SUCCESS;
    }
}