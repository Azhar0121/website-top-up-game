<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Login - TopUp Kilat</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth-theme.css') }}">
</head>
<body>

    <div class="auth-shell auth-shell--centered">
        <div class="auth-form-card text-center">

            <div class="auth-otp-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>

            <h1 class="auth-form-title">Verifikasi 2 Langkah</h1>
            <p class="auth-form-subtitle mb-4">
                Kode 6 digit sudah dikirim ke
                <strong class="text-dark">{{ $user?->email ? \Illuminate\Support\Str::mask($user->email, '*', 2, -8) : 'email kamu' }}</strong>.
                Cek inbox Mailtrap kamu.
            </p>

            @if (session('status'))
                <div class="alert alert-success py-2 small text-start">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger py-2 small text-start">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 small text-start">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('two-factor.verify') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3 text-start">
                    <label for="code" class="form-label fw-semibold small">Kode Verifikasi</label>
                    <input type="text" name="code" id="code" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                           class="form-control app-input auth-otp-input @error('code') is-invalid @enderror"
                           placeholder="------" autocomplete="one-time-code" autofocus required>
                </div>

                <button type="submit" class="btn app-btn-cta w-100">
                    <i class="bi bi-check2-circle me-1"></i> Verifikasi &amp; Masuk
                </button>
            </form>

            <form action="{{ route('two-factor.resend') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-link w-100 small text-decoration-none">
                    Tidak dapat kode? Kirim ulang
                </button>
            </form>

            <p class="text-center small mt-2 mb-0" style="color: var(--color-text-muted);">
                Kode berlaku 5 menit. Kalau ini bukan kamu, tutup halaman ini dan segera ganti password.
            </p>
        </div>
    </div>

</body>
</html>
