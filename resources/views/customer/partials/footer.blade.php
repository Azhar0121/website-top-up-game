<footer class="app-footer">
    <div class="container py-5">

        <div class="footer-trust-row">
            <div class="footer-trust-item"><i class="bi bi-shield-check"></i> Transaksi Aman</div>
            <div class="footer-trust-item"><i class="bi bi-lightning-charge"></i> Proses Otomatis 1-3 Menit</div>
            <div class="footer-trust-item"><i class="bi bi-headset"></i> Customer Care Siap Bantu</div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="app-brand mb-2">
                    <span class="app-brand-icon"><i class="bi bi-lightning-charge-fill"></i></span>
                    TopUp<span class="app-brand-accent">Kilat</span>
                </div>
                <p class="text-light-muted small mb-0">
                    Top up Diamond, Voucher, dan item game favoritmu.
                    Proses otomatis, aman, dan biasanya cuma 1-3 menit.
                </p>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Bantuan</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ url('/cek-transaksi') }}"><i class="bi bi-receipt me-1"></i> Cek Transaksi</a></li>
                    <li><a href="{{ route('faq') }}"><i class="bi bi-question-circle me-1"></i> FAQ</a></li>
                    <li><a href="#"><i class="bi bi-headset me-1"></i> Hubungi Kami</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Perusahaan</h6>
                <ul class="list-unstyled small">
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="{{ route('terms') }}">Syarat &amp; Ketentuan</a></li>
                    <li><a href="{{ route('privacy') }}">Kebijakan Privasi</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h6 class="footer-heading">Metode Pembayaran</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="payment-chip">QRIS</span>
                    <span class="payment-chip">Virtual Account</span>
                    <span class="payment-chip">GoPay</span>
                    <span class="payment-chip">E-Wallet</span>
                </div>
            </div>
        </div>
        <hr class="footer-divider">
        <p class="text-light-muted small mb-0 text-center">
            &copy; {{ date('Y') }} TopUpKilat
        </p>
    </div>
</footer>
