<?php

use App\Http\Controllers\PageController;    
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\ProductController;


Route::get('/', function () {
    return view('customer.home');
});

Route::get('/game/{slug}', [PageController::class, 'gameDetail']);

Route::get('/order/{invoice?}', [PageController::class, 'orderStatus']);

Route::get('/cek-transaksi', function () {
    return redirect('/order');
});

Route::redirect('/login', '/admin/login')->name('login');

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.submit');
    // ==================== AREA ADMIN (wajib login + role staff) ====================
    Route::middleware(['auth', 'role:owner|admin|finance|cs|marketing|developer'])->group(function () {

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // CRUD Game (PRD 6: menu "Games & Products" -> Categories, dst)
        Route::resource('games', AdminGameController::class)->except(['show']);

        // CRUD Category - tanpa create/edit terpisah karena pakai modal di halaman index
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // CRUD Product
        Route::resource('products', ProductController::class)->except(['show']);
    });
});