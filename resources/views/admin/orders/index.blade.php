@extends('layouts.admin')

@section('title', 'Transactions')
@section('page-title', 'Transactions')
@section('page-subtitle', 'Pantau pembayaran dan proses top up pelanggan')

@php
    $statusLabel = fn (string $status) => str($status)->replace('_', ' ')->title();
    $statusClass = fn (string $status) => match ($status) {
        'success' => 'badge-soft-success',
        'failed', 'cancelled' => 'badge-soft-danger',
        'pending_payment', 'paid', 'processing' => 'badge-soft-primary',
        'expired', 'refunded' => 'badge-soft-muted',
        default => 'badge-soft-muted',
    };
@endphp

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <div class="admin-page-title mb-0">Daftar Transaksi</div>
                <div class="admin-page-subtitle">{{ $orders->total() }} order ditemukan</div>
            </div>
            <form action="{{ route('admin.orders.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                       placeholder="Invoice, email, WhatsApp, ID game">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $statusLabel($status) }}</option>
                    @endforeach
                </select>
                <button class="btn btn-admin-primary btn-sm" type="submit"><i class="bi bi-funnel"></i> Filter</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table admin-table mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Produk</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $order->invoice_number }}</div>
                                <small class="text-muted">ID game: {{ $order->target_game_id }}{{ $order->target_server_id ? ' / '.$order->target_server_id : '' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->product?->name ?? '-' }}</div>
                                <small class="text-muted">{{ $order->product?->game?->name ?? '-' }} &times; {{ $order->quantity }}</small>
                            </td>
                            <td>{{ $order->customer_email ?: ($order->customer_whatsapp ?: '-') }}</td>
                            <td class="fw-semibold">Rp{{ number_format((float) $order->price, 0, ',', '.') }}</td>
                            <td><span class="badge {{ $statusClass($order->status) }}">{{ $statusLabel($order->status) }}</span></td>
                            <td><small>{{ $order->created_at->format('d M Y, H:i') }}</small></td>
                            <td class="text-end"><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada transaksi untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="admin-card-body">{{ $orders->links() }}</div>
        @endif
    </div>
@endsection
