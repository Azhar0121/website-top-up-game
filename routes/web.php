<?php

use App\Http\Controllers\PageController;    
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\AccountController;


Route::get('/', function () {
    return view('customer.home');
});

Route::get('/game/{slug}', [PageController::class, 'gameDetail']);

Route::get('/order/{invoice?}', [PageController::class, 'orderStatus']);

Route::get('/cek-transaksi', function () {
    return redirect('/order');
});

// ==================== AUTH (customer & staff, satu form yang sama) ====================
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1')->name('register.submit');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/akun', [AccountController::class, 'index'])->name('account.index');
});

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

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/retry', [AdminOrderController::class, 'retry'])->name('orders.retry');
        Route::post('/orders/{order}/force-success', [AdminOrderController::class, 'forceSuccess'])->name('orders.force-success');
        Route::post('/orders/{order}/resend-callback', [AdminOrderController::class, 'resendCallback'])->name('orders.resend-callback');
        Route::post('/orders/{order}/check-payment-status', [AdminOrderController::class, 'checkPaymentStatus'])->name('orders.check-payment-status');

        // CRUD Game
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

        // Providers & API
        Route::get('/providers', [\App\Http\Controllers\Admin\ProviderController::class, 'index'])->name('providers.index');
        Route::get('/providers/create', [\App\Http\Controllers\Admin\ProviderController::class, 'create'])->name('providers.create');
        Route::post('/providers', [\App\Http\Controllers\Admin\ProviderController::class, 'store'])->name('providers.store');
        Route::get('/providers/{provider}/edit', [\App\Http\Controllers\Admin\ProviderController::class, 'edit'])->name('providers.edit');
        Route::put('/providers/{provider}', [\App\Http\Controllers\Admin\ProviderController::class, 'update'])->name('providers.update');
        Route::post('/providers/{provider}/toggle', [\App\Http\Controllers\Admin\ProviderController::class, 'toggle'])->name('providers.toggle');

        // API & Webhook Logs
        Route::get('/api-logs', [\App\Http\Controllers\Admin\ApiLogController::class, 'index'])->name('api-logs.index');
        Route::get('/api-logs/{apiLog}', [\App\Http\Controllers\Admin\ApiLogController::class, 'show'])->name('api-logs.show');

        // Voucher & Promo Code
        Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class)->except(['show']);

        // Kelola User
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::post('/users/bulk-update-role', [\App\Http\Controllers\Admin\UserController::class, 'bulkUpdateRole'])->name('users.bulk-update-role');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    });
});