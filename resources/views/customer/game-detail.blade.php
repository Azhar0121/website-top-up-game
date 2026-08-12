@extends('layouts.customer')

@section('title', $gameName ?? 'Detail Game')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dark-theme.css') }}">
@endpush

@section('content')

    <div class="container pb-4" id="game-detail-app" data-slug="{{ $slug }}">

        <div id="game-header" class="game-header mb-3">
            <div class="skeleton-line" style="width: 220px; height: 26px;"></div>
            <div class="skeleton-line mt-2" style="width: 320px; height: 14px;"></div>
        </div>

        <nav class="mb-4">
            <a href="{{ url('/') }}" class="text-decoration-none small fw-semibold text-muted">&larr; Kembali ke Semua Game</a>
        </nav>

        {{-- SATU form membungkus KEDUA kolom, supaya semua input (kolom kiri
             maupun kanan) tetap satu kesatuan saat disubmit --}}
        <form id="checkout-form" novalidate>
            <div class="row g-4">

                {{-- ===================== KOLOM KIRI: Langkah 1-3 ===================== --}}
                <div class="col-lg-7">

                    <div id="tutorial-box" class="tutorial-box mb-4 d-none">
                        <div class="tutorial-box-icon"><i class="bi bi-lightbulb-fill"></i></div>
                        <div>
                            <div class="fw-bold mb-1">Cara menemukan ID Game kamu</div>
                            <p class="mb-0 small" id="tutorial-text"></p>
                        </div>
                    </div>

                    {{-- Langkah 1: Pilih Nominal --}}
                    <div class="checkout-step">
                        <div class="step-heading"><span class="step-number">1</span> Pilih Nominal</div>
                        <div id="category-tabs" class="d-flex gap-2 flex-wrap mb-3"></div>
                        <div id="product-grid" class="row g-2">
                            @for ($i = 0; $i < 6; $i++)
                                <div class="col-6 col-md-4">
                                    <div class="skeleton-card" style="height: 76px;"></div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- Langkah 2: Masukkan Data Akun --}}
                    <div class="checkout-step">
                        <div class="step-heading"><span class="step-number">2</span> Masukkan Data Akun</div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="target_game_id">ID Game <span class="text-danger">*</span></label>
                            <input type="text" class="form-control app-input" id="target_game_id" name="target_game_id" placeholder="Contoh: 123456789" required>
                        </div>

                        <div class="mb-0" id="server-id-wrapper">
                            <label class="form-label fw-semibold" for="target_server_id">Server ID <span class="text-muted fw-normal">(kalau ada)</span></label>
                            <input type="text" class="form-control app-input" id="target_server_id" name="target_server_id" placeholder="Contoh: 2001">
                        </div>
                    </div>

                    {{-- Langkah 3: Jumlah Pembelian --}}
                    <div class="checkout-step">
                        <div class="step-heading"><span class="step-number">3</span> Jumlah Pembelian</div>

                        <div class="qty-stepper">
                            <button type="button" id="qty-minus" aria-label="Kurangi jumlah">&minus;</button>
                            <input type="text" id="qty-input" name="quantity" value="1" inputmode="numeric" readonly>
                            <button type="button" id="qty-plus" aria-label="Tambah jumlah">&plus;</button>
                        </div>
                        <p class="small mb-0 mt-2" id="qty-note" style="color: var(--color-text-muted);">
                            Pilih nominal dulu buat lihat batas maksimal pembelian.
                        </p>
                    </div>

                </div>

                {{-- ===================== KOLOM KANAN: Langkah 4-6 + Ringkasan (sticky) ===================== --}}
                <div class="col-lg-5">
                    <div class="checkout-panel">

                        {{-- Langkah 4: Detail Kontak --}}
                        <div class="checkout-step">
                            <div class="step-heading"><span class="step-number">4</span> Detail Kontak</div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="customer_email">Email <span class="text-muted fw-normal">(untuk invoice & notifikasi)</span></label>
                                <input type="email" class="form-control app-input" id="customer_email" name="customer_email" placeholder="nama@email.com">
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-semibold" for="customer_whatsapp">Nomor WhatsApp <span class="text-muted fw-normal">(opsional)</span></label>
                                <input type="text" class="form-control app-input" id="customer_whatsapp" name="customer_whatsapp" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>

                        {{-- Langkah 5: Voucher Diskon --}}
                        <div class="checkout-step">
                            <div class="step-heading"><span class="step-number">5</span> Voucher Diskon</div>
                            <input type="text" class="form-control app-input text-uppercase" id="voucher_code" name="voucher_code" placeholder="Contoh: TOPUP10">
                        </div>

                        {{-- Langkah 6: Metode Pembayaran (preview - pemilihan sungguhan tetap
                             terjadi di popup Snap Midtrans begitu tombol Bayar diklik) --}}
                        <div class="checkout-step">
                            <div class="step-heading"><span class="step-number">6</span> Metode Pembayaran</div>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="payment-chip"><i class="bi bi-qr-code me-1"></i> QRIS</span>
                                <span class="payment-chip"><i class="bi bi-wallet2 me-1"></i> GoPay</span>
                                <span class="payment-chip"><i class="bi bi-wallet2 me-1"></i> E-Wallet Lain</span>
                                <span class="payment-chip"><i class="bi bi-bank me-1"></i> BCA VA</span>
                                <span class="payment-chip"><i class="bi bi-bank me-1"></i> BNI VA</span>
                                <span class="payment-chip"><i class="bi bi-bank me-1"></i> BRI VA</span>
                                <span class="payment-chip"><i class="bi bi-bank me-1"></i> Permata VA</span>
                            </div>
                            <p class="small mb-0" style="color: var(--color-text-muted);">
                                Pilihan lengkap & instruksi pembayaran muncul otomatis setelah klik tombol Bayar di bawah.
                            </p>
                        </div>

                        <div class="order-summary mb-3 d-none" id="order-summary">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Produk dipilih</span>
                                <span class="fw-semibold" id="summary-product-name">-</span>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Jumlah</span>
                                <span class="fw-semibold" id="summary-product-qty">1</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Total Harga</span>
                                <span class="fw-bold" id="summary-product-price">Rp0</span>
                            </div>
                        </div>

                        <div class="sla-note mb-3">
                            <i class="bi bi-lightning-charge-fill"></i> Proses otomatis, estimasi <strong>1-3 menit</strong> setelah pembayaran berhasil.
                        </div>

                        <div id="form-alert" class="alert d-none" role="alert"></div>

                        <button type="submit" class="btn app-btn-cta w-100" id="submit-btn" disabled>
                            Pilih nominal dulu
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

@endsection

@push('scripts')
    <script src="{{ $midtransIsProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ $midtransClientKey }}"></script>

    <script src="{{ asset('js/pages/game-detail.js') }}"></script>
@endpush
