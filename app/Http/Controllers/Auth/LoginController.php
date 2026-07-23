<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

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
        $request->session()->regenerate();

        return redirect()->intended($this->redirectPathFor(Auth::user()));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Kamu sudah logout.');
    }

    /**
     * Staff (owner/admin/finance/cs/marketing/developer) diarahkan ke dashboard admin,
     * selain itu (customer biasa) diarahkan ke halaman akunnya.
     */
    protected function redirectPathFor($user): string
    {
        if ($user->hasAnyRole($this->staffRoles)) {
            return route('admin.dashboard');
        }

        return route('account.index');
    }
}