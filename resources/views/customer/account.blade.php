@extends('layouts.customer')

@section('title', 'Akun Saya')

@php
    $statusLabel = [
        'pending_payment' => ['Menunggu Pembayaran', '#A99DCB'],
        'paid'             => ['Pembayaran Diterima', '#34E4B8'],
        'processing'       => ['Sedang Diproses', '#FFC93C'],
        'success'          => ['Berhasil', '#34E4B8'],
        'failed'           => ['Gagal', '#FF5D8F'],
        'expired'          => ['Kedaluwarsa', '#A99DCB'],
        'refunded'         => ['Dana Dikembalikan', '#A99DCB'],
        'cancelled'        => ['Dibatalkan', '#A99DCB'],
    ];
    $repeatableStatuses = ['success', 'failed', 'expired', 'cancelled'];
@endphp

@section('content')

    <div class="container py-5" style="max-width: 760px;">

        <div class="mb-4 text-center text-md-start">
            <h1 class="section-heading mb-1" style="font-size:1.9rem; color: var(--color-text-light);">Halo, {{ auth()->user()->name }}</h1>
            <p class="mb-0" style="color: var(--color-text-muted);">Riwayat transaksi kamu ada di sini.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success py-2 small">{{ session('status') }}</div>
        @endif

        @forelse ($orders as $order)
            @php [$label, $color] = $statusLabel[$order->status] ?? [$order->status, '#A99DCB']; @endphp
            <div class="order-history-card mb-3">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                        <div class="small d-flex align-items-center gap-1" style="color: var(--color-text-muted);">
                            <i class="bi bi-calendar3"></i> {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
                        </div>
                    </div>
                    <span class="status-badge" style="background:{{ $color }}1f; color:{{ $color }}; border-color:{{ $color }}55;">{{ strtoupper($label) }}</span>
                </div>

                <div class="fw-bold mb-1" style="color: var(--color-text-light); font-size: 1.05rem;">
                    {{ $order->product->name ?? 'Produk sudah dihapus' }}
                </div>
                <div class="small mb-3" style="color: var(--color-text-muted);">
                    {{ $order->product->game->name ?? '-' }} &middot; {{ $order->invoice_number }}
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2" style="border-top: 1px solid var(--color-border-soft);">
                    <span class="fw-bold" style="color: var(--color-text-light); font-size: 1.05rem;">
                        Rp{{ number_format($order->price, 0, ',', '.') }}
                    </span>
                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                        <a href="{{ url('/order/'.$order->invoice_number) }}" class="btn btn-sm app-btn-outline">Lihat Detail</a>

                        @if ($order->status === 'pending_payment')
                            <a href="{{ url('/order/'.$order->invoice_number) }}" class="btn btn-sm app-btn-cta" style="padding: .35rem 1rem;">Bayar Sekarang</a>
                            <button type="button" class="btn btn-sm app-btn-danger-outline" data-cancel-order="{{ $order->invoice_number }}">Batalkan</button>
                        @endif

                        @if (in_array($order->status, $repeatableStatuses) && $order->product && $order->product->game && $order->product->is_active)
                            <a href="{{ url('/game/'.$order->product->game->slug) }}?repeat_product_id={{ $order->product_id }}&target_game_id={{ urlencode($order->target_game_id ?? '') }}&target_server_id={{ urlencode($order->target_server_id ?? '') }}"
                               class="btn btn-sm app-btn-cta" style="padding: .35rem 1rem;">
                                Pesan Lagi
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="catalog-state">
                <div class="catalog-state-emoji"><i class="bi bi-receipt"></i></div>
                <p class="fw-bold mb-1" style="color: var(--color-text-light);">Belum ada transaksi</p>
                <p class="mb-3 small">Yuk top up game favoritmu sekarang.</p>
                <a href="{{ url('/') }}" class="btn app-btn-cta px-4">Mulai Top Up</a>
            </div>
        @endforelse

        @if ($orders->hasPages())
            <div class="mt-4">{{ $orders->links() }}</div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-cancel-order]').forEach((button) => {
            button.addEventListener('click', async () => {
                const invoice = button.dataset.cancelOrder;
                if (!window.confirm(`Batalkan pesanan ${invoice}? Tindakan ini tidak dapat dibatalkan.`)) return;

                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = 'Membatalkan...';

                try {
                    const response = await fetch(`/api/v1/orders/${encodeURIComponent(invoice)}/cancel`, {
                        method: 'POST',
                        headers: { Accept: 'application/json' },
                    });
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Pesanan tidak dapat dibatalkan.');
                    }

                    window.location.reload();
                } catch (error) {
                    window.alert(error.message || 'Gagal membatalkan pesanan.');
                    button.disabled = false;
                    button.textContent = originalText;
                }
            });
        });
    </script>
@endpush
