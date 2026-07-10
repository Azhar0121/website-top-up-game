<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('customer.home');
});

Route::get('/game/{slug}', function ($slug) {
    return "Halaman checkout untuk game '{$slug}' akan dibangun di langkah berikutnya.";
});

Route::get('/cek-transaksi', function () {
    return "Halaman cek transaksi akan dibangun di langkah berikutnya.";
});

Route::get('/login', function () {
    return "Halaman login akan dibangun di langkah berikutnya.";
});