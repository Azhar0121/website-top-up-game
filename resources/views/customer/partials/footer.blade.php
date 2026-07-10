<footer class="app-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="app-brand mb-2">
                    <span class="app-brand-mark">⚡</span> TopUp<span class="app-brand-accent">Kilat</span>
                </div>
                <p class="text-light-muted small mb-0">
                    Top up Diamond, Voucher, dan item game favoritmu.
                    Proses otomatis, aman, dan biasanya cuma 1-3 menit.
                </p>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Bantuan</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ url('/cek-transaksi') }}">Cek Transaksi</a></li>
                    <li><a href="#">Cara Top Up</a></li>
                    <li><a href="#">Hubungi Kami</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Perusahaan</h6>
                <ul class="list-unstyled small">
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
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
            &copy; {{ date('Y') }} TopUpKilat. Dibuat untuk keperluan pembelajaran.
        </p>
    </div>
</footer>
