<?php

use App\Http\Controllers\PageController;    
use Illuminate\Support\Facades\Route;

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