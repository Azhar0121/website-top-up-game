@extends('layouts.admin')

@section('title', 'Product & Game Performance Report')
@section('page-title', 'Product & Game Performance Report')
@section('page-subtitle', 'Produk paling laris berdasarkan qty terjual & revenue')

@php
    $indexRoute = 'admin.reports.product-performance';
    $exportRoute = 'admin.reports.product-performance.export';
@endphp

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <div class="admin-page-title mb-0">Top 50 Produk</div>
                <div class="admin-page-subtitle">{{ $from->translatedFormat('d M Y') }} &mdash; {{ $to->translatedFormat('d M Y') }} &middot; diurutkan dari revenue tertinggi</div>
            </div>
            @include('admin.reports.partials.filter-bar')
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Produk</th>
                            <th>Game</th>
                            <th>Qty Terjual</th>
                            <th>Jumlah Order</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report as $index => $row)
                            <tr>
                                <td class="text-muted">{{ $index + 1 }}</td>
                                <td class="fw-semibold text-dark">{{ $row['name'] }}</td>
                                <td>{{ $row['game_name'] ?? '-' }}</td>
                                <td>{{ $row['qty_sold'] }}</td>
                                <td>{{ $row['order_count'] }}</td>
                                <td>Rp{{ number_format($row['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Tidak ada data pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-primary border-0 mt-3" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
        <i class="bi bi-info-circle me-1"></i>
        Revenue &amp; qty di sini pakai definisi yang sama dengan Sales &amp; Revenue Report (order Paid/Processing/Success/Failed).
        Tabel dibatasi 50 produk teratas — kalau perlu data lengkap semua produk, pakai tombol Export CSV.
    </div>
@endsection
