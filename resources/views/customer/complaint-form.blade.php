@extends('layouts.customer')

@section('title', 'Form Keluhan')
@section('meta_description', 'Kirim keluhan seputar transaksi top up kamu ke tim CS TopUp Kilat, lengkap dengan lampiran gambar.')

@section('content')

    <section class="static-page-hero">
        <div class="container">
            <h1 class="mb-2">Form Keluhan</h1>
            <p class="mb-0 text-light-muted">Ceritakan kendala kamu selengkap mungkin, tim CS kami akan menindaklanjuti lewat email.</p>
        </div>
    </section>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="static-page-card">

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold" style="font-size:.9rem; color: var(--color-text-light);">Nama</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}"
                                   class="form-control app-input @error('name') is-invalid @enderror"
                                   placeholder="Nama kamu" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold" style="font-size:.9rem; color: var(--color-text-light);">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}"
                                   class="form-control app-input @error('email') is-invalid @enderror"
                                   placeholder="nama@email.com" required>
                            <div class="form-text" style="color: var(--color-text-muted);">Balasan dari tim CS akan dikirim ke email ini.</div>
                        </div>

                        <div class="mb-3">
                            <label for="whatsapp" class="form-label fw-semibold" style="font-size:.9rem; color: var(--color-text-light);">Nomor WhatsApp <span class="fw-normal" style="color: var(--color-text-muted);">(opsional)</span></label>
                            <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}"
                                   class="form-control app-input @error('whatsapp') is-invalid @enderror"
                                   placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label fw-semibold" style="font-size:.9rem; color: var(--color-text-light);">Subjek</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                                   class="form-control app-input @error('subject') is-invalid @enderror"
                                   placeholder="Contoh: Pesanan sudah bayar tapi item belum masuk" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label fw-semibold" style="font-size:.9rem; color: var(--color-text-light);">Detail Keluhan</label>
                            <textarea name="message" id="message" rows="5"
                                      class="form-control app-input @error('message') is-invalid @enderror"
                                      placeholder="Jelaskan kendala kamu selengkap mungkin, sertakan nomor invoice jika ada." required>{{ old('message') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label fw-semibold" style="font-size:.9rem; color: var(--color-text-light);">Lampiran Gambar <span class="fw-normal" style="color: var(--color-text-muted);">(opsional)</span></label>
                            <input type="file" name="image" id="image" accept="image/png, image/jpeg, image/webp"
                                   class="form-control app-input @error('image') is-invalid @enderror">
                            <div class="form-text" style="color: var(--color-text-muted);">Format JPG, PNG, atau WEBP, maksimal 2MB. Berguna untuk melampirkan bukti transfer atau tangkapan layar error.</div>
                        </div>

                        <button type="submit" class="btn app-btn-cta w-100">
                            <i class="bi bi-send-fill me-1"></i> Kirim Keluhan
                        </button>
                    </form>

                </div>

                <p class="text-center small mt-3" style="color: var(--color-text-muted);">
                    Butuh respon lebih cepat? <a href="{{ route('contact') }}" class="fw-semibold">Chat lewat WhatsApp</a> saja.
                </p>
            </div>
        </div>
    </div>

@endsection
