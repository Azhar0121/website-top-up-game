<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

/**
 * PRD 3 "User Account": Login via Google/Email/WA - ini implementasi jalur Google-nya,
 * pakai Laravel Socialite. Alur OAuth-nya:
 * 1. GET /auth/google/redirect -> lempar user ke halaman consent Google
 * 2. Google redirect balik ke /auth/google/callback bawa data profil user
 * 3. Kita cari/bikin User lokal, login-kan, lanjut redirect sesuai role (sama seperti
 *    LoginController::redirectPathFor - staff ke admin, customer ke akun).
 */
class GoogleController extends Controller
{
    protected array $staffRoles = ['owner', 'admin', 'finance', 'cs', 'marketing', 'developer'];

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Coba lagi ya.');
        }

        // Cari berdasarkan google_id dulu. Kalau belum ketemu tapi emailnya SUDAH
        // terdaftar (misal dulu daftar manual pakai email+password), hubungkan ke
        // akun yang sama - supaya satu email tidak jadi dua akun terpisah.
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'name'              => $googleUser->getName() ?: $googleUser->getNickname(),
                    'email'             => $googleUser->getEmail(),
                    // Akun Google tidak butuh password manual, tapi kolom `password`
                    // di database masih NOT NULL - jadi diisi random panjang yang
                    // tidak pernah dipakai/diberitahu ke siapapun (tidak bisa dipakai
                    // untuk login lewat form email+password sama sekali).
                    'password'          => Hash::make(Str::random(40)),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);

                $user->assignRole(Role::firstOrCreate(['name' => 'customer'])->name);
            }
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        $target = $user->hasAnyRole($this->staffRoles) ? route('admin.dashboard') : route('account.index');

        return redirect()->intended($target);
    }
}
