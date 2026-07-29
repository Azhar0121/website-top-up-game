<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminOtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * 2FA OTP khusus untuk login STAFF (owner/admin/finance/cs/marketing/developer),
 * Customer biasa TIDAK melalui alur ini.
 */
class TwoFactorService
{
    protected const SESSION_KEY = 'two_factor_challenge';

    protected const CODE_TTL_MINUTES = 5;

    /**
     * Mulai challenge OTP baru untuk user ini: generate kode, simpan hash-nya
     * ke session, dan kirim email berisi kode asli.
     */
    public function challenge(User $user, bool $remember, string $intendedUrl): void
    {
        $code = (string) random_int(100000, 999999);

        session([
            self::SESSION_KEY => [
                'user_id'     => $user->id,
                'code_hash'   => Hash::make($code),
                'expires_at'  => now()->addMinutes(self::CODE_TTL_MINUTES)->timestamp,
                'remember'    => $remember,
                'intended'    => $intendedUrl,
                'attempts'    => 0,
            ],
        ]);

        $user->notify(new AdminOtpNotification($code));
    }

    public function hasPendingChallenge(): bool
    {
        return session()->has(self::SESSION_KEY);
    }

    public function pendingUser(): ?User
    {
        $data = session(self::SESSION_KEY);

        return $data ? User::find($data['user_id']) : null;
    }

    public function intendedUrl(): string
    {
        return session(self::SESSION_KEY.'.intended', '/');
    }

    /**
     * Cek kode yang diinput user. Return true kalau valid (dan otomatis
     * membersihkan challenge dari session supaya kode tidak bisa dipakai ulang).
     */
    public function verify(string $inputCode): bool
    {
        $data = session(self::SESSION_KEY);

        if (! $data) {
            return false;
        }

        // Batasi maksimal 5x coba salah per challenge, agar tidak bisa di-brute-force.
        if (($data['attempts'] ?? 0) >= 5) {
            $this->clear();

            return false;
        }

        if (now()->timestamp > $data['expires_at']) {
            $this->clear();

            return false;
        }

        if (! Hash::check($inputCode, $data['code_hash'])) {
            $data['attempts'] = ($data['attempts'] ?? 0) + 1;
            session([self::SESSION_KEY => $data]);

            return false;
        }

        $this->clear();

        return true;
    }

    public function shouldRemember(): bool
    {
        return (bool) session(self::SESSION_KEY.'.remember', false);
    }

    /**
     * Generate & kirim ulang kode baru untuk challenge yang sedang berjalan
     * (dipanggil dari tombol "Kirim Ulang Kode").
     */
    public function resend(): bool
    {
        $user = $this->pendingUser();

        if (! $user) {
            return false;
        }

        $data = session(self::SESSION_KEY);
        $this->challenge($user, $data['remember'] ?? false, $data['intended'] ?? '/');

        return true;
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}