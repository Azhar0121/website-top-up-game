<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $this->registerRateLimiters();
    }

    /**
     * Named rate limiter (PRD 5. Security & Proteksi: "Rate Limiting - Mencegah
     * spam order / DDOS application layer"). Login/register sudah punya
     * throttle manual sendiri di controller-nya masing-masing (lihat
     * LoginController & Admin\AuthController), jadi di sini fokus ke endpoint
     * lain yang sebelumnya belum dibatasi sama sekali.
     */
    protected function registerRateLimiters(): void
    {
        // Bikin order baru - paling rawan disalahgunakan buat spam/DDOS.
        RateLimiter::for('checkout', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Cek status order (GET /api/v1/orders/{invoice}) - dipanggil berulang oleh
        // halaman Cek Transaksi buat polling, jadi limitnya dilonggarkan.
        RateLimiter::for('order-status', function ($request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        // Buka Snap payment - satu order harusnya cuma butuh ini beberapa kali saja.
        RateLimiter::for('payment-initiate', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Webhook dari Midtrans - limit dilonggarkan karena payment gateway bisa
        // retry callback beberapa kali kalau respons kita lambat/timeout.
        RateLimiter::for('payment-webhook', function ($request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}