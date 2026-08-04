<?php

use App\Http\Controllers\PageController;    
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CmsPageController as AdminCmsPageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\FlashSaleController as AdminFlashSaleController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentGatewayController as AdminPaymentGatewayController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Customer\AccountController;


Route::get('/', [PageController::class, 'home']);

Route::get('/game/{slug}', [PageController::class, 'gameDetail']);

Route::get('/order/{invoice?}', [PageController::class, 'orderStatus']);

Route::get('/cek-transaksi', function () {
    return redirect('/order');
});

Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/syarat-ketentuan', [PageController::class, 'terms'])->name('terms');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');

// ==================== AUTH (customer & staff, satu form yang sama) ====================
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1')->name('register.submit');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ==================== 2FA OTP (khusus staff, dipicu dari /login) ====================
Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
Route::post('/two-factor', [TwoFactorController::class, 'verify'])->middleware('throttle:10,1')->name('two-factor.verify');
Route::post('/two-factor/resend', [TwoFactorController::class, 'resend'])->name('two-factor.resend');

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::middleware('auth')->group(function () {
    Route::get('/akun', [AccountController::class, 'index'])->name('account.index');
});

Route::prefix('admin')->name('admin.')->middleware('restrict_admin_ip')->group(function () {

    // ==================== AREA ADMIN (wajib login + role staff) ====================
    Route::middleware(['auth', 'role:owner|admin|finance|cs|marketing|developer'])->group(function () {

        Route::middleware('permission:dashboard.view')->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        });

        Route::middleware('permission:orders.view')->group(function () {
            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        });
        Route::middleware('permission:orders.retry')->group(function () {
            Route::post('/orders/{order}/retry', [AdminOrderController::class, 'retry'])->name('orders.retry');
            Route::post('/orders/{order}/resend-callback', [AdminOrderController::class, 'resendCallback'])->name('orders.resend-callback');
            Route::post('/orders/{order}/check-payment-status', [AdminOrderController::class, 'checkPaymentStatus'])->name('orders.check-payment-status');
        });
        Route::middleware('permission:orders.force-success')->group(function () {
            Route::post('/orders/{order}/force-success', [AdminOrderController::class, 'forceSuccess'])->name('orders.force-success');
        });

        Route::middleware('permission:games.manage')->group(function () {
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
        });

        Route::middleware('permission:providers.manage')->group(function () {
            // Providers & API
            Route::get('/providers', [\App\Http\Controllers\Admin\ProviderController::class, 'index'])->name('providers.index');
            Route::get('/providers/create', [\App\Http\Controllers\Admin\ProviderController::class, 'create'])->name('providers.create');
            Route::post('/providers', [\App\Http\Controllers\Admin\ProviderController::class, 'store'])->name('providers.store');
            Route::get('/providers/{provider}/edit', [\App\Http\Controllers\Admin\ProviderController::class, 'edit'])->name('providers.edit');
            Route::put('/providers/{provider}', [\App\Http\Controllers\Admin\ProviderController::class, 'update'])->name('providers.update');
            Route::post('/providers/{provider}/toggle', [\App\Http\Controllers\Admin\ProviderController::class, 'toggle'])->name('providers.toggle');
        });

        Route::middleware('permission:api-logs.view')->group(function () {
            // API & Webhook Logs
            Route::get('/api-logs', [\App\Http\Controllers\Admin\ApiLogController::class, 'index'])->name('api-logs.index');
            Route::get('/api-logs/{apiLog}', [\App\Http\Controllers\Admin\ApiLogController::class, 'show'])->name('api-logs.show');
        });

        Route::middleware('permission:vouchers.manage')->group(function () {
            // Voucher & Promo Code
            Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class)->except(['show']);
        });

        Route::middleware('permission:flash-sales.manage')->group(function () {
            // Flash Sale
            Route::resource('flash-sales', AdminFlashSaleController::class)->except(['show']);
            Route::post('/flash-sales/{flash_sale}/toggle', [AdminFlashSaleController::class, 'toggle'])->name('flash-sales.toggle');
        });

        Route::middleware('permission:cms.manage')->group(function () {
            // Content & Marketing / CMS ringan (sitemap "Content & Marketing (CMS)")
            Route::resource('banners', AdminBannerController::class)->except(['show']);
            Route::post('/banners/{banner}/toggle', [AdminBannerController::class, 'toggle'])->name('banners.toggle');

            Route::resource('faqs', AdminFaqController::class)->except(['show']);

            Route::get('/pages', [AdminCmsPageController::class, 'index'])->name('pages.index');
            Route::get('/pages/{slug}/edit', [AdminCmsPageController::class, 'edit'])->name('pages.edit');
            Route::put('/pages/{slug}', [AdminCmsPageController::class, 'update'])->name('pages.update');
        });

        Route::middleware('permission:payment-gateways.manage')->group(function () {
            // Payments & Finance (sitemap "Payments & Finance > Payment Gateway Settings")
            Route::get('/payment-gateways', [AdminPaymentGatewayController::class, 'index'])->name('payment-gateways.index');
            Route::get('/payment-gateways/{paymentGateway}/edit', [AdminPaymentGatewayController::class, 'edit'])->name('payment-gateways.edit');
            Route::put('/payment-gateways/{paymentGateway}', [AdminPaymentGatewayController::class, 'update'])->name('payment-gateways.update');
            Route::post('/payment-gateways/{paymentGateway}/toggle', [AdminPaymentGatewayController::class, 'toggle'])->name('payment-gateways.toggle');
        });

        Route::middleware('permission:users.manage')->group(function () {
            // Kelola User
            Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
            Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
            Route::post('/users/bulk-update-role', [\App\Http\Controllers\Admin\UserController::class, 'bulkUpdateRole'])->name('users.bulk-update-role');
            Route::get('/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/block', [\App\Http\Controllers\Admin\UserController::class, 'block'])->name('users.block');
            Route::post('/users/{user}/unblock', [\App\Http\Controllers\Admin\UserController::class, 'unblock'])->name('users.unblock');
        });

        Route::middleware('permission:audit-logs.view')->group(function () {
            // Audit Log
            Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
        });

        Route::middleware('permission:reports.view')->group(function () {
            // Reports
            Route::get('/reports/sales-revenue', [AdminReportController::class, 'salesRevenue'])->name('reports.sales-revenue');
            Route::get('/reports/sales-revenue/export', [AdminReportController::class, 'exportSalesRevenue'])->name('reports.sales-revenue.export');
            Route::get('/reports/profit-margin', [AdminReportController::class, 'profitMargin'])->name('reports.profit-margin');
            Route::get('/reports/profit-margin/export', [AdminReportController::class, 'exportProfitMargin'])->name('reports.profit-margin.export');
            Route::get('/reports/provider-performance', [AdminReportController::class, 'providerPerformance'])->name('reports.provider-performance');
            Route::get('/reports/provider-performance/export', [AdminReportController::class, 'exportProviderPerformance'])->name('reports.provider-performance.export');
            Route::get('/reports/product-performance', [AdminReportController::class, 'productPerformance'])->name('reports.product-performance');
            Route::get('/reports/product-performance/export', [AdminReportController::class, 'exportProductPerformance'])->name('reports.product-performance.export');
        });
    });
});