<?php

use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\ProviderController as AdminProviderController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Katalog game & produk
    Route::get('/games', [GameController::class, 'index']);
    Route::get('/games/{slug}', [GameController::class, 'show']);

    // Login admin
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Checkout & cek status order
    Route::post('/checkout', [OrderController::class, 'store']);
    Route::get('/orders/{invoice}', [OrderController::class, 'show']);

    Route::get('/payment/methods', [PaymentController::class, 'paymentMethods']);

    // Transaksi pembayaran
    Route::post('/payment/initiate', [PaymentController::class, 'initiate']);

    Route::post('/webhook/payment/{gatewayCode}', [PaymentController::class, 'callback']);
    
    // Admin
    Route::prefix('admin')
        ->middleware(['auth:sanctum', 'role:owner|admin|cs'])
        ->group(function () {

            Route::get('/orders', [AdminOrderController::class, 'index']);
            Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
            Route::post('/orders/{order}/retry', [AdminOrderController::class, 'retry']);
            Route::post('/orders/{order}/force-success', [AdminOrderController::class, 'forceSuccess']);
            Route::post('/orders/{order}/resend-callback', [AdminOrderController::class, 'resendCallback']);

            Route::get('/providers', [AdminProviderController::class, 'index']);
            Route::patch('/providers/{provider}/toggle', [AdminProviderController::class, 'toggle']);
            Route::patch('/providers/{provider}/priority', [AdminProviderController::class, 'updatePriority']);

            Route::patch('/products/{product}/margin', [ProductController::class, 'updateMargin']);
            Route::post('/products/sync-prices', [ProductController::class, 'syncPrices']);
        });
});