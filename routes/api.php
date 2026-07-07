<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/checkout', [OrderController::class, 'store']);
    Route::get('/orders/{invoice}', [OrderController::class, 'show']);
    Route::post('/payment/initiate', [PaymentController::class, 'initiate']);
    Route::post('/webhook/payment/{gatewayCode}', [PaymentController::class, 'callback']);
});