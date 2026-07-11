@extends('layouts.customer')

@section('title', $gameName ?? 'Detail Game')

@section('content')

    <div class="container py-4" id="game-detail-app" data-slug="{{ $slug }}">

        <nav class="mb-3">
            <a href="{{ url('/') }}" class="text-decoration-none small fw-semibold text-muted">&larr; Kembali ke Semua Game</a>
        </nav>

        <div id="game-header" class="game-header mb-4">
            <div class="skeleton-line" style="width: 220px; height: 26px;"></div>
            <div class="skeleton-line mt-2" style="width: 320px; height: 14px;"></div>
        </div>

        <div class="row g-4">
            {{-- ===================== KOLOM KIRI: Info & Produk ===================== --}}
            <div class="col-lg-7">

                <div id="tutorial-box" class="tutorial-box mb-4 d-none">
                    <div class="tutorial-box-icon">💡</div>
                    <div>
                        <div class="fw-bold mb-1">Cara menemukan ID Game kamu</div>
                        <p class="mb-0 small" id="tutorial-text"></p>
                    </div>
                </div>

                <h2 class="section-heading">Pilih Nominal</h2>
                <div id="category-tabs" class="d-flex gap-2 flex-wrap mb-3"></div>
                <div id="product-grid" class="row g-2 mb-4">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="col-6 col-md-4">
                            <div class="skeleton-card" style="height: 76px;"></div>
                        </div>
                    @endfor
                </div>
            </div>

            {{-- ===================== KOLOM KANAN: Form Checkout (sticky) ===================== --}}
            <div class="col-lg-5">
                <div class="checkout-panel">
                    <h2 class="section-heading mb-3">Data Akun & Pembayaran</h2>

                    <form id="checkout-form" novalidate>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="target_game_id">ID Game <span class="text-danger">*</span></label>
                            <input type="text" class="form-control app-input" id="target_game_id" name="target_game_id" placeholder="Contoh: 123456789" required>
                        </div>

                        <div class="mb-3" id="server-id-wrapper">
                            <label class="form-label fw-semibold" for="target_server_id">Server ID <span class="text-muted fw-normal">(kalau ada)</span></label>
                            <input type="text" class="form-control app-input" id="target_server_id" name="target_server_id" placeholder="Contoh: 2001">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="customer_email">Email <span class="text-muted fw-normal">(untuk invoice & notifikasi)</span></label>
                            <input type="email" class="form-control app-input" id="customer_email" name="customer_email" placeholder="nama@email.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="customer_whatsapp">Nomor WhatsApp <span class="text-muted fw-normal">(opsional, untuk notifikasi)</span></label>
                            <input type="text" class="form-control app-input" id="customer_whatsapp" name="customer_whatsapp" placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="voucher_code">Kode Voucher <span class="text-muted fw-normal">(opsional)</span></label>
                            <input type="text" class="form-control app-input text-uppercase" id="voucher_code" name="voucher_code" placeholder="Contoh: TOPUP10">
                        </div>

                        <div class="order-summary mb-3 d-none" id="order-summary">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Produk dipilih</span>
                                <span class="fw-semibold" id="summary-product-name">-</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Harga</span>
                                <span class="fw-bold" id="summary-product-price">Rp0</span>
                            </div>
                        </div>

                        <div class="sla-note mb-3">
                            ⚡ Proses otomatis, estimasi <strong>1-3 menit</strong> setelah pembayaran berhasil.
                        </div>

                        <div id="form-alert" class="alert d-none" role="alert"></div>

                        <button type="submit" class="btn app-btn-cta w-100" id="submit-btn" disabled>
                            Pilih nominal dulu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ $midtransIsProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ $midtransClientKey }}"></script>

    <script src="{{ asset('js/pages/game-detail.js') }}"></script>
@endpush
