<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verifikasi token Google reCAPTCHA v3.
 *
 * PENTING buat kamu yang baru pertama pakai:
 * - reCAPTCHA v3 TIDAK menampilkan checkbox/puzzle kayak v2. Dia jalan diam-diam
 *   di background dan menghasilkan "score" 0.0 (kemungkinan besar bot) - 1.0
 *   (kemungkinan besar manusia).
 * - Supaya fitur ini aktif, kamu WAJIB daftar dulu di
 *   https://www.google.com/recaptcha/admin/create (pilih tipe "v3"), lalu isi
 *   RECAPTCHA_SITE_KEY & RECAPTCHA_SECRET_KEY di file .env dengan key asli.
 * - Selama RECAPTCHA_SECRET_KEY masih kosong di .env, verifikasi ini otomatis
 *   di-SKIP (dianggap selalu lolos) - supaya login/checkout tidak ikut rusak
 *   cuma karena kamu belum sempat daftar akun reCAPTCHA. Begitu key diisi,
 *   verifikasi otomatis aktif tanpa perlu ubah kode lagi.
 */
class RecaptchaService
{
    /**
     * @param  string|null  $token  Token dari field g-recaptcha-response / recaptcha_token
     * @param  string  $expectedAction  Nama action yang didaftarkan di sisi JS (grecaptcha.execute(key, {action: ...}))
     */
    public function verify(?string $token, string $expectedAction): bool
    {
        $secretKey = config('services.recaptcha.secret_key');

        // Fitur belum dikonfigurasi (belum ada akun reCAPTCHA asli) - skip, jangan blokir user.
        if (blank($secretKey)) {
            return true;
        }

        if (blank($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secretKey,
                'response' => $token,
            ]);

            $result = $response->json();

            if (! ($result['success'] ?? false)) {
                Log::warning('reCAPTCHA gagal diverifikasi', ['errors' => $result['error-codes'] ?? []]);

                return false;
            }

            $minScore = (float) config('services.recaptcha.min_score', 0.5);
            $score = (float) ($result['score'] ?? 0);

            if ($score < $minScore) {
                Log::warning('reCAPTCHA score terlalu rendah, kemungkinan bot', [
                    'score' => $score, 'action' => $result['action'] ?? null,
                ]);

                return false;
            }

            // Action tidak dicocokkan secara ketat (cuma dicatat kalau beda) supaya
            // tidak ada risiko user gagal login/checkout gara-gara mismatch teknis kecil.
            if (($result['action'] ?? $expectedAction) !== $expectedAction) {
                Log::info('reCAPTCHA action berbeda dari yang diharapkan', [
                    'expected' => $expectedAction, 'actual' => $result['action'] ?? null,
                ]);
            }

            return true;
        } catch (\Throwable $e) {
            // Kalau Google API down/network error, jangan sampai user tidak bisa login sama sekali.
            Log::error('Gagal menghubungi Google reCAPTCHA', ['message' => $e->getMessage()]);

            return true;
        }
    }
}
