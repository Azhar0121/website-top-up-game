<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Login - TopUp Kilat</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-custom.css') }}">
</head>
<body class="admin-body">

    <div class="admin-login-page">
        <div class="admin-login-card">
            <div class="text-center mb-4">
                <div class="admin-login-brand">
                    <i class="bi bi-shield-lock-fill text-warning"></i>
                    Verifikasi 2 Langkah
                </div>
                <p class="text-muted small mb-0">
                    Kami sudah kirim kode 6 digit ke email
                    <strong>{{ $user?->email ? \Illuminate\Support\Str::mask($user->email, '*', 2, -8) : 'kamu' }}</strong>.
                    Cek inbox Mailtrap kamu.
                </p>
            </div>

            @if (session('status'))
                <div class="alert alert-success py-2 small">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('two-factor.verify') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="code" class="form-label fw-semibold small">Kode Verifikasi</label>
                    <input type="text" name="code" id="code" inputmode="numeric" pattern="[0-9]*" maxlength="6"
                           class="form-control text-center fs-4 fw-bold @error('code') is-invalid @enderror"
                           style="letter-spacing: .5rem;" placeholder="------" autocomplete="one-time-code" autofocus required>
                </div>

                <button type="submit" class="btn btn-admin-primary w-100 py-2">
                    <i class="bi bi-check2-circle me-1"></i> Verifikasi & Masuk
                </button>
            </form>

            <form action="{{ route('two-factor.resend') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-link w-100 small text-decoration-none">
                    Tidak dapat kode? Kirim ulang
                </button>
            </form>

            <p class="text-center text-muted small mt-2 mb-0">
                Kode berlaku 5 menit. Kalau ini bukan kamu, tutup halaman ini dan segera ganti password.
            </p>
        </div>
    </div>

</body>
</html>
