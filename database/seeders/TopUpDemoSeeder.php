<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Game;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ProviderProduct;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class TopUpDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Game
        $mlbb = Game::create([
            'name' => 'Mobile Legends: Bang Bang',
            'slug' => 'mobile-legends',
            'tutorial_text' => 'ID dan Zone ID bisa dilihat di halaman profil dalam game.',
            'is_favorite' => true,
            'is_popular'  => true,
        ]);

        // 2. Kategori
        $diamond = Category::create(['game_id' => $mlbb->id, 'name' => 'Diamond']);

        // 3. Produk
        $product86 = Product::create([
            'game_id'     => $mlbb->id,
            'category_id' => $diamond->id,
            'name'        => '86 Diamonds',
            'region'      => 'Indo',
            'base_price'  => 25000,
        ]);

        // 4. Provider utama (priority 1) & backup (priority 2)
        $digiflazz = Provider::create([
            'name' => 'Digiflazz',
            'code' => 'digiflazz',
            'base_url' => 'https://api.digiflazz.com/v1', // ganti sesuai sandbox asli
            'api_key' => 'username_sandbox_digiflazz',
            'api_secret' => 'apikey_sandbox_digiflazz',
            'priority' => 1,
        ]);

        $vip = Provider::create([
            'name' => 'VIP Reseller',
            'code' => 'vip_reseller',
            'base_url' => 'https://vip-reseller.co.id/api',
            'api_key' => 'key_sandbox_vip',
            'api_secret' => 'secret_sandbox_vip',
            'priority' => 2, // backup, dicoba kalau Digiflazz gagal
        ]);

        // 5. Mapping produk ke tiap provider (SKU & harga modal berbeda tiap provider)
        ProviderProduct::create([
            'provider_id' => $digiflazz->id,
            'product_id'  => $product86->id,
            'provider_sku_code' => 'mlbb86d',
            'cost_price'  => 22000,
        ]);

        ProviderProduct::create([
            'provider_id' => $vip->id,
            'product_id'  => $product86->id,
            'provider_sku_code' => 'mlbb-86-diamond',
            'cost_price'  => 22500,
        ]);

        PaymentGateway::create([
            'name' => 'Midtrans',
            'code' => 'midtrans',
            'api_key' => 'SB-Mid-client-xxxxxxxxxxxx',   // Client Key (dari dashboard Midtrans sandbox)
            'api_secret' => 'SB-Mid-server-xxxxxxxxxxxx', // Server Key (RAHASIA, jangan expose ke frontend)
            'is_sandbox' => true,
        ]);

        PaymentGateway::create([
            'name' => 'Duitku',
            'code' => 'duitku',
            'merchant_code' => 'DXXXX',
            'api_secret' => 'XXXXXXXXXX7968XXXXXXXXXFB05332AF',
            'is_sandbox' => true,
            'is_active' => false,
        ]);

        PaymentGateway::create([
            'name' => 'Tripay',
            'code' => 'tripay',
            'api_key' => 'sandbox_api_key',
            'api_secret' => 'sandbox_private_key',
            'merchant_code' => 'T0001',
            'is_sandbox' => true,
            'is_active' => false,
        ]);

        // 7. Voucher contoh untuk testing validasi diskon saat checkout
        Voucher::create([
            'code' => 'TOPUP10',
            'type' => 'percentage',
            'value' => 10,          // 10%
            'max_discount' => 5000, 
            'min_transaction' => 20000,
            'usage_limit' => 100,
            'is_active' => true,
        ]);

        Voucher::create([
            'code' => 'HEMAT5000',
            'type' => 'fixed',
            'value' => 5000,
            'min_transaction' => 15000,
            'usage_limit' => null,
            'is_active' => true,
        ]);

        $this->command->info('Demo data berhasil di-seed: 1 game, 1 produk, 2 provider top-up (dengan priority), 3 payment gateway (Midtrans aktif, Duitku & Tripay standby), 2 voucher.');
    }
}