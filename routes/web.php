<?php

use App\Http\Controllers\PageController;    
use Illuminate\Support\Facades\Route;

Route::get('/test-ssl', function () {
    try {
        $response = \Illuminate\Support\Facades\Http::get('https://api.sandbox.midtrans.com');
        return 'BERHASIL - Status: ' . $response->status();
    } catch (\Exception $e) {
        return 'GAGAL - ' . $e->getMessage();
    }
});

Route::get('/', function () {
    return view('customer.home');
});

Route::get('/game/{slug}', [PageController::class, 'gameDetail']);

Route::get('/order/{invoice?}', [PageController::class, 'orderStatus']);

Route::get('/cek-transaksi', function () {
    return redirect('/order');
});

Route::get('/login', function () {
    return "Halaman login web akan dibangun di langkah berikutnya.";
});