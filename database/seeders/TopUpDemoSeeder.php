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

        // 4. Provider
        $mockPrimary = Provider::create([
            'name' => 'Mock Provider (Utama)',
            'code' => 'mock_digiflazz',
            'base_url' => 'https://mock.local',
            'api_key' => 'mock',
            'api_secret' => 'mock',
            'priority' => 1,
            'is_active' => true,
        ]);

        $mockBackup = Provider::create([
            'name' => 'Mock Provider (Backup)',
            'code' => 'mock_backup',
            'base_url' => 'https://mock.local',
            'api_key' => 'mock',
            'api_secret' => 'mock',
            'priority' => 2,
            'is_active' => true,
        ]);

        $digiflazz = Provider::create([
            'name' => 'Digiflazz',
            'code' => 'digiflazz',
            'base_url' => 'https://api.digiflazz.com',
            'api_key' => 'username_asli_nanti',
            'api_secret' => 'apikey_asli_nanti',
            'priority' => 3,
            'is_active' => false,
        ]);

        $vip = Provider::create([
            'name' => 'VIP Reseller',
            'code' => 'vip_reseller',
            'base_url' => 'https://vip-reseller.co.id/api',
            'api_key' => 'key_sandbox_vip',
            'api_secret' => 'secret_sandbox_vip',
            'priority' => 4,
            'is_active' => false,
        ]);

        // 5. Mapping produk ke provider MOCK
        ProviderProduct::create([
            'provider_id' => $mockPrimary->id,
            'product_id'  => $product86->id,
            'provider_sku_code' => 'mlbb86d-mock',
            'cost_price'  => 22000,
        ]);

        ProviderProduct::create([
            'provider_id' => $mockBackup->id,
            'product_id'  => $product86->id,
            'provider_sku_code' => 'mlbb86d-mock-backup',
            'cost_price'  => 22500,
        ]);

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

        // 6. Payment Gateway
        PaymentGateway::create([
            'name' => 'Midtrans',
            'code' => 'midtrans',
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

        // 7. Voucher
        Voucher::create([
            'code' => 'TOPUP10',
            'type' => 'percentage',
            'value' => 10, // 10%
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
            'usage_limit' => null, // unlimited
            'is_active' => true,
        ]);

        $this->command->info('Demo data berhasil di-seed: 1 game, 1 produk, 2 provider top-up (dengan priority), 3 payment gateway (Midtrans aktif, Duitku & Tripay standby), 2 voucher.');
    }
}
