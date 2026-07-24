@extends('layouts.admin')

@section('title', 'Detail Order')
@section('page-title', 'Detail Order')
@section('page-subtitle', $order->invoice_number)

@php
    $statusLabel = str($order->status)->replace('_', ' ')->title();
    $statusClass = match ($order->status) {
        'success' => 'badge-soft-success',
        'failed', 'cancelled' => 'badge-soft-danger',
        'pending_payment', 'paid', 'processing' => 'badge-soft-primary',
        default => 'badge-soft-muted',
    };
    $payment = $order->payment->sortByDesc('id')->first();
@endphp

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke transaksi</a>
        <span class="admin-status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="admin-card mb-3">
                <div class="admin-card-header"><div class="fw-bold">Informasi Order</div></div>
                <div class="admin-card-body">
                    <div class="admin-info-row"><span class="admin-info-row-label">Invoice</span><span class="admin-info-row-value">{{ $order->invoice_number }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Produk</span><span class="admin-info-row-value">{{ $order->product?->name ?? '-' }} &times; {{ $order->quantity }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Game / tujuan</span><span class="admin-info-row-value">{{ $order->product?->game?->name ?? '-' }}<br>{{ $order->target_game_id }}{{ $order->target_server_id ? ' / '.$order->target_server_id : '' }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Pelanggan</span><span class="admin-info-row-value">{{ $order->customer_email ?: '-' }}<br>{{ $order->customer_whatsapp ?: '-' }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Total dibayar</span><span class="admin-info-row-value">Rp{{ number_format((float) $order->price, 0, ',', '.') }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Provider</span><span class="admin-info-row-value">{{ $order->provider?->name ?? 'Belum dipilih' }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Stok</span><span class="admin-info-row-value">{{ $order->product?->stock ?? 'Unlimited' }}{{ $order->stock_deducted_at ? ' (dikurangi '.$order->stock_deducted_at->format('d M H:i').')' : '' }}</span></div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><div class="fw-bold">Riwayat Status</div></div>
                <div class="admin-card-body admin-timeline">
                    @forelse ($order->logs as $log)
                        <div class="admin-timeline-item">
                            <span class="admin-timeline-dot"></span>
                            <div>
                                <div class="fw-semibold">{{ str($log->status)->replace('_', ' ')->title() }}</div>
                                @if ($log->note)<div class="small text-muted">{{ $log->note }}</div>@endif
                                <small class="text-muted">{{ $log->created_at->format('d M Y, H:i') }}{{ $log->actor ? ' · '.$log->actor : '' }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada riwayat status.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="admin-card mb-3">
                <div class="admin-card-header"><div class="fw-bold">Pembayaran</div></div>
                <div class="admin-card-body">
                    <div class="admin-info-row"><span class="admin-info-row-label">Gateway</span><span class="admin-info-row-value">{{ $payment?->paymentGateway?->name ?? '-' }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Metode</span><span class="admin-info-row-value">{{ $payment?->method ?? '-' }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Status</span><span class="admin-info-row-value">{{ $payment?->status ?? '-' }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Dibayar pada</span><span class="admin-info-row-value">{{ $payment?->paid_at?->format('d M Y, H:i') ?? '-' }}</span></div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><div class="fw-bold">Tindakan Manual</div></div>
                <div class="admin-card-body d-grid gap-2">
                    @if ($order->status === 'pending_payment' && $payment)
                        <form method="POST" action="{{ route('admin.orders.check-payment-status', $order) }}" onsubmit="return confirm('Cek status pembayaran ini langsung ke payment gateway?')">
                            @csrf
                            <button class="btn btn-outline-primary w-100"><i class="bi bi-arrow-clockwise"></i> Cek Status ke Payment Gateway</button>
                        </form>
                        <p class="small text-muted mb-0">Dipakai kalau order tetap "Menunggu Pembayaran" walau customer sudah bayar - biasanya karena webhook belum sampai (cek konfigurasi ngrok/notification URL).</p>
                    @endif

                    @if ($order->status === 'failed')
                        <form method="POST" action="{{ route('admin.orders.retry', $order) }}" onsubmit="return confirm('Jalankan ulang request ke provider?')">
                            @csrf
                            <button class="btn btn-admin-primary w-100"><i class="bi bi-arrow-repeat"></i> Retry Provider</button>
                        </form>
                    @endif

                    @if (in_array($order->status, ['paid', 'processing', 'failed'], true))
                        <button class="btn btn-outline-success w-100" data-bs-toggle="collapse" data-bs-target="#forceSuccessForm"><i class="bi bi-check2-circle"></i> Force Success</button>
                        <div class="collapse" id="forceSuccessForm">
                            <form method="POST" action="{{ route('admin.orders.force-success', $order) }}" class="border rounded p-2 mt-2" onsubmit="return confirm('Tandai order ini sukses secara manual?')">
                                @csrf
                                <label class="form-label small fw-semibold" for="note">Catatan tindakan</label>
                                <textarea id="note" name="note" class="form-control form-control-sm" rows="3" required minlength="5" placeholder="Contoh: Top up telah diselesaikan manual oleh CS."></textarea>
                                <button class="btn btn-success btn-sm w-100 mt-2">Konfirmasi Force Success</button>
                            </form>
                        </div>
                    @endif

                    @if ($order->status === 'success')
                        <form method="POST" action="{{ route('admin.orders.resend-callback', $order) }}">
                            @csrf
                            <button class="btn btn-outline-secondary w-100"><i class="bi bi-send"></i> Kirim Ulang Notifikasi</button>
                        </form>
                    @endif

                    @if (! in_array($order->status, ['failed', 'paid', 'processing', 'success'], true) && ! ($order->status === 'pending_payment' && $payment))
                        <p class="small text-muted mb-0">Tidak ada tindakan manual untuk status ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
