<?php

use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\ProviderController as AdminProviderController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::post('/v1/login', function (Illuminate\Http\Request $request) {
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['success' => false, 'message' => 'Kredensial salah'], 401);
    }

    return response()->json([
        'success' => true,
        'token' => $user->createToken('admin-token')->plainTextToken,
    ]);
});

Route::prefix('v1')->group(function () {

    // Tanpa login
    // Katalog game & produk
    Route::get('/games', [GameController::class, 'index']);
    Route::get('/games/{slug}', [GameController::class, 'show']);

    // Checkout & cek status order
    Route::post('/checkout', [OrderController::class, 'store']);
    Route::get('/orders/{invoice}', [OrderController::class, 'show']);

    Route::get('/payment/methods', [PaymentController::class, 'paymentMethods']);

    Route::post('/payment/initiate', [PaymentController::class, 'initiate']);

    Route::post('/webhook/payment/{gatewayCode}', [PaymentController::class, 'callback']);

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
        });
});