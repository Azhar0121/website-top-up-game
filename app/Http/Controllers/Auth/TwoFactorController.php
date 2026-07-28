<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function __construct(protected TwoFactorService $twoFactor)
    {
    }

    /**
     * GET /two-factor
     */
    public function show()
    {
        if (! $this->twoFactor->hasPendingChallenge()) {
            return redirect()->route('login');
        }

        return view('auth.two-factor', [
            'user' => $this->twoFactor->pendingUser(),
        ]);
    }

    /**
     * POST /two-factor
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);

        if (! $this->twoFactor->hasPendingChallenge()) {
            return redirect()->route('login')->with('error', 'Sesi verifikasi sudah habis, silakan login ulang.');
        }

        $user = $this->twoFactor->pendingUser();
        $remember = $this->twoFactor->shouldRemember();
        $intended = $this->twoFactor->intendedUrl();

        if (! $this->twoFactor->verify($validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'Kode verifikasi salah atau sudah kedaluwarsa.',
            ]);
        }

        Auth::loginUsingId($user->id, $remember);
        $request->session()->regenerate();

        return redirect()->to($intended);
    }

    /**
     * POST /two-factor/resend
     */
    public function resend(Request $request)
    {
        if (! $this->twoFactor->hasPendingChallenge()) {
            return redirect()->route('login');
        }

        $throttleKey = 'otp-resend:'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->with('error', "Terlalu sering minta kirim ulang. Coba lagi dalam {$seconds} detik.");
        }

        RateLimiter::hit($throttleKey, 600); // 10 menit

        $this->twoFactor->resend();

        return back()->with('status', 'Kode verifikasi baru sudah dikirim ke email kamu.');
    }
}
