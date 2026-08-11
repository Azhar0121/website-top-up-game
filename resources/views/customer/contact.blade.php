@extends('layouts.customer')

@section('title', 'Hubungi Kami')
@section('meta_description', 'Butuh bantuan seputar top up? Hubungi tim TopUp Kilat lewat WhatsApp, email, atau Instagram.')

@section('content')

    <section class="static-page-hero">
        <div class="container">
            <h1 class="mb-2">Hubungi Kami</h1>
            <p class="mb-0 text-light-muted">Tim kami siap bantu 24/7. Pilih channel yang paling nyaman buat kamu.</p>
        </div>
    </section>

    <div class="container py-5">
        <div class="row justify-content-center g-3">

            <div class="col-md-4">
                <div class="static-page-card h-100 text-center">
                    <div class="contact-channel-icon" style="background: rgba(52,228,184,.14); color: var(--color-accent-mint);">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <h5 class="fw-bold mt-3 mb-1" style="color: var(--color-text-light);">WhatsApp</h5>
                    <p class="small mb-3" style="color: var(--color-text-muted);">Respon tercepat, biasanya di bawah 10 menit.</p>
                    @if ($whatsappNumber)
                        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="btn app-btn-cta w-100">
                            Chat Sekarang
                        </a>
                    @else
                        <button type="button" class="btn app-btn-cta w-100" disabled>Belum tersedia</button>
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-page-card h-100 text-center">
                    <div class="contact-channel-icon" style="background: rgba(255,201,60,.14); color: var(--color-accent-yellow);">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <h5 class="fw-bold mt-3 mb-1" style="color: var(--color-text-light);">Email</h5>
                    <p class="small mb-3" style="color: var(--color-text-muted);">Untuk pertanyaan detail atau lampiran bukti transfer.</p>
                    <a href="mailto:{{ $supportEmail }}" class="btn app-btn-outline w-100">{{ $supportEmail }}</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="static-page-card h-100 text-center">
                    <div class="contact-channel-icon" style="background: rgba(255,93,143,.14); color: var(--color-accent-pink);">
                        <i class="bi bi-instagram"></i>
                    </div>
                    <h5 class="fw-bold mt-3 mb-1" style="color: var(--color-text-light);">Instagram</h5>
                    <p class="small mb-3" style="color: var(--color-text-muted);">Update promo & flash sale terbaru ada di sini.</p>
                    <a href="https://instagram.com/{{ ltrim($instagram, '@') }}" target="_blank" rel="noopener" class="btn app-btn-outline w-100">{{ $instagram }}</a>
                </div>
            </div>

        </div>

        <div class="row justify-content-center mt-4">
            <div class="col-lg-8">
                <div class="static-page-card d-flex align-items-center gap-3 flex-wrap flex-md-nowrap">
                    <div class="contact-channel-icon flex-shrink-0" style="background: rgba(255,255,255,.08); color: var(--color-text-light);">
                        <i class="bi bi-question-circle-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1" style="color: var(--color-text-light);">Sebelum menghubungi kami...</h6>
                        <p class="small mb-2" style="color: var(--color-text-muted);">
                            Coba cek dulu dua halaman ini, siapa tahu jawabannya sudah ada dan kamu tidak perlu menunggu balasan.
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('faq') }}" class="btn btn-sm app-btn-outline">Lihat FAQ</a>
                            <a href="{{ url('/cek-transaksi') }}" class="btn btn-sm app-btn-outline">Cek Status Transaksi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
