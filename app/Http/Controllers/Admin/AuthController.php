<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login untuk Dashboard Admin (Blade, berbasis SESSION - bukan Sanctum token).
 *
 * Ini SENGAJA dipisah dari App\Http\Controllers\Api\AuthController yang sudah ada:
 * - Api\AuthController -> login API (Sanctum token), dipakai kalau nanti ada mobile app / SPA terpisah.
 * - Admin\AuthController (file ini) -> login untuk halaman Blade /admin/*, pakai cookie session
 *   bawaan Laravel supaya CSRF protection & @csrf di form CRUD berfungsi normal.
 *
 * Kedua login ini menembak baris user yang SAMA di tabel `users`, cuma beda cara autentikasinya.
 */
class AuthController extends Controller
{
    /**
     * Tampilkan form login admin.
     * GET /admin/login
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Proses login admin.
     * POST /admin/login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate limiting sederhana (PRD 5: Security & Proteksi -> Rate Limiting).
        // Key dibuat dari kombinasi email + IP supaya satu email yang diserang brute-force
        // tidak otomatis mengunci IP lain, dan sebaliknya.
        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60); // lock 60 detik per percobaan gagal

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // Login berhasil secara kredensial, tapi role harus salah satu role staff.
        // Ini mencegah customer/member/reseller biasa (yang juga punya baris di tabel users)
        // ikut bisa masuk ke /admin/* hanya karena email & password mereka valid.
        $allowedRoles = ['owner', 'admin', 'finance', 'cs', 'marketing', 'developer'];

        if (! Auth::user()->hasAnyRole($allowedRoles)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun ini tidak memiliki akses ke Dashboard Admin.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Logout admin.
     * POST /admin/logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'Kamu sudah logout.');
    }
}
