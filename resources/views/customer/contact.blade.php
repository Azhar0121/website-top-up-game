@extends('layouts.customer')

@section('title', 'Hubungi Kami')
@section('meta_description', 'Butuh bantuan seputar top up? Hubungi tim TopUp Kilat lewat WhatsApp atau kirim keluhan lewat form.')

@section('content')

    <section class="static-page-hero">
        <div class="container">
            <h1 class="mb-2">Hubungi Kami</h1>
            <p class="mb-0 text-light-muted">Tim kami siap bantu 24/7. Pilih channel yang paling nyaman buat kamu.</p>
        </div>
    </section>

    <div class="container py-5">

        @if (session('status'))
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <div class="alert alert-success">{{ session('status') }}</div>
                </div>
            </div>
        @endif

        <div class="row justify-content-center g-3">

            <div class="col-md-5">
                <div class="static-page-card h-100 text-center contact-channel-card">
                    <div class="contact-channel-icon" style="background: rgba(52,228,184,.14); color: var(--color-accent-mint);">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <h5 class="fw-bold mt-3 mb-1" style="color: var(--color-text-light);">Chat WhatsApp</h5>
                    <p class="small mb-3" style="color: var(--color-text-muted);">Respon tercepat, biasanya di bawah 10 menit. Cocok untuk pertanyaan singkat atau kendala mendesak.</p>
                    @if ($whatsappNumber)
                        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" class="btn app-btn-cta w-100">
                            Chat Sekarang
                        </a>
                    @else
                        <button type="button" class="btn app-btn-cta w-100" disabled>Belum tersedia</button>
                    @endif
                </div>
            </div>

            <div class="col-md-5">
                <div class="static-page-card h-100 text-center contact-channel-card">
                    <div class="contact-channel-icon" style="background: rgba(255,201,60,.14); color: var(--color-accent-yellow);">
                        <i class="bi bi-flag-fill"></i>
                    </div>
                    <h5 class="fw-bold mt-3 mb-1" style="color: var(--color-text-light);">Form Keluhan</h5>
                    <p class="small mb-3" style="color: var(--color-text-muted);">Untuk keluhan yang butuh detail lengkap, seperti bukti transfer atau tangkapan layar. Akan ditindaklanjuti tim CS lewat email.</p>
                    <a href="{{ route('complaints.create') }}" class="btn app-btn-outline w-100">
                        Isi Form Keluhan
                    </a>
                </div>
            </div>

        </div>

    </div>

@endsection
