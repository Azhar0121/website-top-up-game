<footer class="app-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="app-brand mb-2">
                    TopUp<span class="app-brand-accent">Kilat</span>
                </div>
                <div class="footer-social mb-3">
                    <a href="{{ config('support.facebook') }}" target="_blank" rel="noopener" class="footer-social-icon" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://instagram.com/{{ ltrim(config('support.instagram'), '@') }}" target="_blank" rel="noopener" class="footer-social-icon" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://tiktok.com/{{ '@' . ltrim(config('support.tiktok'), '@') }}" target="_blank" rel="noopener" class="footer-social-icon" aria-label="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                </div>
                <a href="mailto:{{ config('support.email') }}" class="footer-email-link">
                    <i class="bi bi-envelope-fill me-1"></i> {{ config('support.email') }}
                </a>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Bantuan</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ url('/cek-transaksi') }}"><i class="bi bi-receipt me-1"></i> Cek Transaksi</a></li>
                    <li><a href="{{ route('faq') }}"><i class="bi bi-question-circle me-1"></i> FAQ</a></li>
                    <li><a href="{{ route('contact') }}"><i class="bi bi-headset me-1"></i> Hubungi Kami</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2">
                <h6 class="footer-heading">Perusahaan</h6>
                <ul class="list-unstyled small">
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
            Copyright &copy; {{ date('Y') }} TopUpKilat All Rights Reserved
        </p>
    </div>
</footer>
