@extends('layouts.admin')

@section('title', 'Sales & Revenue Report')
@section('page-title', 'Sales & Revenue Report')
@section('page-subtitle', 'Total transaksi & revenue berdasarkan order yang sudah dibayar')

@php
    $indexRoute = 'admin.reports.sales-revenue';
    $exportRoute = 'admin.reports.sales-revenue.export';
    $granularityLabels = ['hourly' => 'Per Jam (Hari Ini)', 'daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan'];
@endphp

@section('content')
    <div class="row g-3 mb-1">
        <div class="col-md-4">
            <div class="admin-card admin-card-accent admin-card-body h-100" style="border-top-color: var(--admin-primary);">
                <div class="text-muted small">Total Revenue</div>
                <div class="fw-bold text-dark fs-4">Rp{{ number_format($report['total_revenue'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card admin-card-accent accent-mint admin-card-body h-100">
                <div class="text-muted small">Total Order (dibayar)</div>
                <div class="fw-bold text-dark fs-4">{{ number_format($report['total_orders'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-card admin-card-accent accent-pink admin-card-body h-100">
                <div class="text-muted small">Total Refund (info, tidak dihitung ke revenue)</div>
                <div class="fw-bold text-dark fs-4">Rp{{ number_format($report['total_refund'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="admin-card mb-3">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">Tren Revenue</div>
            @include('admin.reports.partials.chart-quick-filter')
        </div>
        <div class="admin-card-body">
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <div class="admin-page-title mb-0">Rincian {{ $granularityLabels[$granularity] }}</div>
                <div class="admin-page-subtitle">{{ $from->translatedFormat('d M Y') }} &mdash; {{ $to->translatedFormat('d M Y') }}</div>
            </div>
            @include('admin.reports.partials.filter-bar')
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>Jumlah Order</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report['daily'] as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ $row['orders_count'] }}</td>
                                <td>Rp{{ number_format($row['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-5">Tidak ada data pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-primary border-0 mt-3" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
        <i class="bi bi-info-circle me-1"></i>
        Revenue dihitung dari order berstatus <strong>Paid, Processing, Success,</strong> atau <strong>Failed</strong>
        (uang sudah masuk dari customer). Order <strong>Refunded</strong> ditampilkan terpisah karena uangnya sudah
        dikembalikan, dan <strong>Pending/Expired/Cancelled</strong> tidak dihitung karena belum/tidak pernah dibayar.
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js"></script>
<script>
    const revenueData = @json($report['daily']);

    if (typeof Chart === 'undefined') {
        console.error('Chart.js gagal dimuat dari CDN. Cek koneksi internet atau apakah cdnjs.cloudflare.com diblokir jaringan/firewall.');
    } else {
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: revenueData.map(row => row.label),
            datasets: [{
                label: 'Revenue',
                data: revenueData.map(row => row.revenue),
                backgroundColor: '#6C4FF0',
                borderRadius: 6,
            }],
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { ticks: { callback: (value) => 'Rp' + Number(value).toLocaleString('id-ID') } },
            },
        },
    });
    }
</script>
@endpush
