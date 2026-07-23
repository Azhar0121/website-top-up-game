<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

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