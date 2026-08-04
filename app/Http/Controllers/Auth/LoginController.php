<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RecaptchaService;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected array $staffRoles = ['owner', 'admin', 'finance', 'cs', 'marketing', 'developer'];

    public function show()
    {
        if (Auth::check()) {
            return redirect($this->redirectPathFor(Auth::user()));
        }

        return view('auth.login');
    }

    public function login(Request $request, RecaptchaService $recaptcha, TwoFactorService $twoFactor)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! $recaptcha->verify($request->input('recaptcha_token'), 'login')) {
            throw ValidationException::withMessages([
                'email' => 'Verifikasi keamanan (reCAPTCHA) gagal. Silakan muat ulang halaman dan coba lagi.',
            ]);
        }

        $throttleKey = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $user = Auth::user();

        if ($user->is_blocked) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Akun ini telah diblokir. Hubungi admin kalau kamu merasa ini keliru.',
            ]);
        }

        $remember = $request->boolean('remember');
        $intended = $this->redirectPathFor($user);

        if ($user->hasAnyRole($this->staffRoles)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $twoFactor->challenge($user, $remember, $intended);

            return redirect()->route('two-factor.show');
        }

        $request->session()->regenerate();

        return redirect()->intended($intended);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Kamu sudah logout.');
    }

    protected function redirectPathFor($user): string
    {
        if ($user->hasAnyRole($this->staffRoles)) {
            return route('admin.dashboard');
        }

        return route('account.index');
    }
}