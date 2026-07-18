<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Game;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Test stok memerlukan ekstensi pdo_sqlite pada PHP CLI.');
        }

        parent::setUp();
    }

    public function test_limited_stock_is_deducted_once_when_payment_is_confirmed(): void
    {
        $game = Game::create(['name' => 'Test Game', 'slug' => 'test-game']);
        $category = Category::create(['game_id' => $game->id, 'name' => 'Diamond']);
        $product = Product::create([
            'game_id' => $game->id,
            'category_id' => $category->id,
            'name' => '100 Diamond',
            'region' => 'Global',
            'base_price' => 10000,
            'stock' => 5,
        ]);
        $order = Order::create([
            'product_id' => $product->id,
            'target_game_id' => '123456',
            'quantity' => 2,
            'price' => 20000,
            'status' => Order::STATUS_PENDING_PAYMENT,
        ]);

        $service = app(OrderService::class);

        $this->assertTrue($service->processAfterPayment($order));
        $this->assertFalse($service->processAfterPayment($order->fresh()));

        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame(Order::STATUS_PAID, $order->fresh()->status);
        $this->assertNotNull($order->fresh()->stock_deducted_at);
    }
}
