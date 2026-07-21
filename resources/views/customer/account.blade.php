@extends('layouts.customer')

@section('title', 'Akun Saya')

@php
    $statusLabel = [
        'pending_payment' => ['Menunggu Pembayaran', '#6B6482'],
        'paid'             => ['Pembayaran Diterima', '#34E4B8'],
        'processing'       => ['Sedang Diproses', '#FFC93C'],
        'success'          => ['Berhasil', '#34E4B8'],
        'failed'           => ['Gagal', '#FF5D8F'],
        'expired'          => ['Kedaluwarsa', '#6B6482'],
        'refunded'         => ['Dana Dikembalikan', '#6B6482'],
        'cancelled'        => ['Dibatalkan', '#6B6482'],
    ];
    $repeatableStatuses = ['success', 'failed', 'expired', 'cancelled'];
@endphp

@section('content')

    <div class="container py-5" style="max-width: 760px;">

        <div class="mb-4">
            <h1 class="section-heading mb-1" style="font-size:1.6rem;">Halo, {{ auth()->user()->name }}</h1>
            <p class="mb-0" style="color: var(--color-text-muted);">Riwayat transaksi kamu ada di sini.</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success py-2 small">{{ session('status') }}</div>
        @endif

        @forelse ($orders as $order)
            @php [$label, $color] = $statusLabel[$order->status] ?? [$order->status, '#6B6482']; @endphp
            <div class="checkout-panel mb-3">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                    <div>
                        <div class="small" style="color: var(--color-text-muted);">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</div>
                        <div class="fw-bold">{{ $order->product->name ?? 'Produk sudah dihapus' }}</div>
                        <div class="small" style="color: var(--color-text-muted);">{{ $order->product->game->name ?? '-' }} &middot; {{ $order->invoice_number }}</div>
                    </div>
                    <span class="status-badge" style="background:{{ $color }}22; color:{{ $color }};">{{ $label }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <span class="fw-bold">Rp{{ number_format($order->price, 0, ',', '.') }}</span>
                    <div class="d-flex gap-2">
                        <a href="{{ url('/order/'.$order->invoice_number) }}" class="btn btn-sm app-btn-outline">Lihat Detail</a>

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
                <div class="catalog-state-emoji">🛒</div>
                <p class="fw-bold mb-1">Belum ada transaksi</p>
                <p class="mb-3 small">Yuk top up game favoritmu sekarang.</p>
                <a href="{{ url('/') }}" class="btn app-btn-cta px-4">Mulai Top Up</a>
            </div>
        @endforelse

        @if ($orders->hasPages())
            <div class="mt-4">{{ $orders->links() }}</div>
        @endif
    </div>

@endsection
