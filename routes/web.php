<?php

use App\Http\Controllers\PageController;    
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;


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

    // ==================== LOGIN (hanya untuk tamu / belum login) ====================
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->middleware('throttle:10,1') // proteksi tambahan di level route, selain rate limiter manual di controller
            ->name('login.submit');
    });

    // ==================== AREA ADMIN (wajib login + role staff) ====================
    // Role sesuai PRD 4.6: Owner, Admin, Finance, CS, Marketing, Developer.
    // Kalau nanti mau dibedakan per-menu (misal Finance tidak boleh akses CRUD produk),
    // tinggal pecah middleware role di masing-masing group di bawah ini.
    Route::middleware(['auth', 'role:owner|admin|finance|cs|marketing|developer'])->group(function () {

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // CRUD Game (PRD 6: menu "Games & Products" -> Categories, dst)
        Route::resource('games', AdminGameController::class)->except(['show']);

        // CRUD Category - tanpa create/edit terpisah karena pakai modal di halaman index
        // (lihat resources/views/admin/categories/index.blade.php)
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // CRUD Product
        Route::resource('products', AdminProductController::class)->except(['show']);
    });
});