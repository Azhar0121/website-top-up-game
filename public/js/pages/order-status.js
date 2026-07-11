(function () {
    'use strict';

    const API_BASE = window.APP_CONFIG.apiBase;
    const app = document.getElementById('order-status-app');
    const lookupForm = document.getElementById('lookup-form');
    const invoiceInput = document.getElementById('invoice-input');
    const resultEl = document.getElementById('order-result');

    const FINAL_STATUSES = ['success', 'failed', 'expired', 'refunded', 'cancelled'];

    const STATUS_LABEL = {
        pending_payment: { text: 'Menunggu Pembayaran', color: '#6B6482', emoji: '⏳' },
        paid: { text: 'Pembayaran Diterima', color: '#34E4B8', emoji: '💰' },
        processing: { text: 'Sedang Diproses', color: '#FFC93C', emoji: '⚙️' },
        success: { text: 'Berhasil', color: '#34E4B8', emoji: '✅' },
        failed: { text: 'Gagal', color: '#FF5D8F', emoji: '❌' },
        expired: { text: 'Kedaluwarsa', color: '#6B6482', emoji: '⌛' },
        refunded: { text: 'Dana Dikembalikan', color: '#6B6482', emoji: '↩️' },
        cancelled: { text: 'Dibatalkan', color: '#6B6482', emoji: '🚫' },
    };

    let pollTimer = null;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function formatRupiah(value) {
        return 'Rp' + Number(value).toLocaleString('id-ID');
    }

    function formatDate(isoString) {
        const date = new Date(isoString);
        return date.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
    }

    function renderLoading() {
        resultEl.innerHTML = `
            <div class="checkout-panel text-center py-5">
                <div class="spinner-border" style="color: var(--color-accent-pink);" role="status"></div>
                <p class="mt-3 mb-0 text-muted">Memuat data transaksi...</p>
            </div>`;
    }

    function renderNotFound() {
        resultEl.innerHTML = `
            <div class="catalog-state">
                <div class="catalog-state-emoji">🔍</div>
                <p class="fw-bold mb-1">Transaksi tidak ditemukan</p>
                <p class="mb-0 small">Periksa kembali nomor invoice kamu.</p>
            </div>`;
    }

    function renderOrder(order) {
        const statusInfo = STATUS_LABEL[order.status] || { text: order.status, color: '#6B6482', emoji: 'ℹ️' };

        const timelineHtml = (order.logs || [])
            .slice()
            .reverse()
            .map((log) => `
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div>
                        <div class="fw-semibold small">${escapeHtml((STATUS_LABEL[log.status] || {}).text || log.status)}</div>
                        <div class="text-muted small">${escapeHtml(log.note || '')}</div>
                        <div class="text-muted" style="font-size:.72rem;">${formatDate(log.created_at)}</div>
                    </div>
                </div>
            `).join('');

        resultEl.innerHTML = `
            <div class="checkout-panel mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-muted small">No. Invoice</div>
                        <div class="fw-bold">${escapeHtml(order.invoice_number)}</div>
                    </div>
                    <span class="status-badge" style="background:${statusInfo.color}22; color:${statusInfo.color};">
                        ${statusInfo.emoji} ${statusInfo.text}
                    </span>
                </div>

                <hr>

                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Produk</span>
                    <span class="fw-semibold">${escapeHtml(order.product ? order.product.name : '-')}</span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">ID Game Tujuan</span>
                    <span class="fw-semibold">${escapeHtml(order.target_game_id)}${order.target_server_id ? ' (' + escapeHtml(order.target_server_id) + ')' : ''}</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span class="text-muted">Total Bayar</span>
                    <span class="fw-bold">${formatRupiah(order.price)}</span>
                </div>

                ${order.status === 'pending_payment' ? `
                    <div class="sla-note mt-3 mb-0">
                        Belum menyelesaikan pembayaran? <a href="/">Kembali ke halaman utama</a> untuk coba lagi.
                    </div>` : ''}
                ${!['success','failed','expired','refunded','cancelled'].includes(order.status) ? `
                    <div class="text-center text-muted small mt-3">
                        <span class="spinner-border spinner-border-sm me-1"></span> Memantau status secara otomatis...
                    </div>` : ''}
            </div>

            <h2 class="section-heading mb-3" style="font-size:1.1rem;">Riwayat Status</h2>
            <div class="timeline">
                ${timelineHtml || '<p class="text-muted small">Belum ada riwayat.</p>'}
            </div>
        `;
    }

    async function loadOrder(invoice) {
        renderLoading();
        clearTimeout(pollTimer);

        try {
            const response = await fetch(`${API_BASE}/orders/${encodeURIComponent(invoice)}`, {
                headers: { Accept: 'application/json' },
            });

            if (response.status === 404) {
                renderNotFound();
                return;
            }

            if (!response.ok) throw new Error('Gagal memuat data');

            const result = await response.json();
            const order = result.data;
            renderOrder(order);

            // Auto-refresh tiap 5 detik selama status belum final
            if (!FINAL_STATUSES.includes(order.status)) {
                pollTimer = setTimeout(() => loadOrder(invoice), 10000);
            }
        } catch (err) {
            console.error(err);
            resultEl.innerHTML = `<div class="catalog-state"><div class="catalog-state-emoji">⚠️</div><p class="fw-bold mb-0">Gagal memuat transaksi</p></div>`;
        }
    }

    lookupForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const invoice = invoiceInput.value.trim();
        if (!invoice) return;

        window.history.pushState({}, '', '/order/' + encodeURIComponent(invoice));
        loadOrder(invoice);
    });

    const initialInvoice = app.dataset.invoice;
    if (initialInvoice) {
        loadOrder(initialInvoice);
    }
})();
