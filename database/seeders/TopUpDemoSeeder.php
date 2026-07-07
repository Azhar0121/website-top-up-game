<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Game;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ProviderProduct;
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

        // 4. Provider utama
        $digiflazz = Provider::create([
            'name' => 'Digiflazz',
            'code' => 'digiflazz',
            'base_url' => 'https://api.digiflazz.com/v1',
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
            'priority' => 2, 
        ]);

        // 5. Mapping produk ke tiap provider 
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
            'name' => 'Tripay',
            'code' => 'tripay',
            'api_key' => 'sandbox_api_key',
            'api_secret' => 'sandbox_private_key',
            'merchant_code' => 'T0001',
            'is_sandbox' => true,
        ]);

        $this->command->info('Demo data berhasil di-seed: 1 game, 1 produk, 2 provider (dengan priority), 1 payment gateway.');
    }
}