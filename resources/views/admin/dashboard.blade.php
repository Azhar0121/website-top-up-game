@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional TopUp Kilat hari ini')

@php
    $trendLabels = [
        'hourly' => 'Hari Ini',
        'daily' => '7 Hari Terakhir',
        'weekly' => '8 Minggu Terakhir',
        'monthly' => '12 Bulan Terakhir',
        'yearly' => '5 Tahun Terakhir', 
    ];
@endphp

@section('content')
    <div class="row g-3 mb-1">
        <div class="admin-section-label mb-0">Ringkasan Hari Ini</div>
    </div>
    <div class="row g-3 mb-1">
        <div class="col-md-3 col-6">
            <div class="admin-card admin-card-accent admin-card-body h-100" style="border-top-color: var(--admin-primary);">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-icon-badge">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Sales Hari Ini</div>
                        <div class="fw-bold text-dark fs-5">Rp{{ number_format($kpi['sales_today'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="admin-card admin-card-accent accent-mint admin-card-body h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-icon-badge accent-mint">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Profit Hari Ini</div>
                        <div class="fw-bold text-dark fs-5">Rp{{ number_format($kpi['profit_today'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="admin-card admin-card-accent accent-yellow admin-card-body h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-icon-badge accent-yellow">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pending / Diproses</div>
                        <div class="fw-bold text-dark fs-5">{{ $kpi['pending_today'] }} order</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="admin-card admin-card-accent accent-pink admin-card-body h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-icon-badge accent-pink">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Success Ratio</div>
                        <div class="fw-bold text-dark fs-5">
                            {{ $kpi['success_ratio_today'] !== null ? $kpi['success_ratio_today'].'%' : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="admin-section-label mb-0">Tren &amp; Performa</div>
    </div>
    <div class="row g-3 mt-1">
        <div class="col-lg-8">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <div>
                        <div class="admin-page-title mb-0">Tren Sales &mdash; {{ $trendLabels[$trendGranularity] }}</div>
                        <div class="admin-page-subtitle">Revenue berdasarkan order yang sudah dibayar</div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <select id="trendGranularitySelect" class="form-select form-select-sm" style="max-width:150px;"
                                onchange="window.location.href = this.value">
                            @foreach (['hourly' => 'Hari Ini', 'daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan'] as $value => $label)
                                <option value="{{ route('admin.dashboard', ['trend' => $value]) }}" {{ $trendGranularity === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('admin.reports.sales-revenue') }}" class="btn btn-admin-primary btn-sm">
                            <i class="bi bi-bar-chart-fill"></i> Laporan Lengkap
                        </a>
                    </div>
                </div>
                <div class="admin-card-body">
                    <canvas id="salesTrendChart" height="90"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <div class="admin-page-title mb-0">Best Seller Hari Ini</div>
                </div>
                <div class="admin-card-body">
                    @if ($kpi['best_seller_today'])
                        <div class="d-flex align-items-center gap-3">
                            <div class="admin-icon-badge accent-mint">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $kpi['best_seller_today'] }}</div>
                                <div class="text-muted small">{{ $kpi['best_seller_qty'] }} unit terjual</div>
                            </div>
                        </div>
                    @else
                        <div class="text-muted small">Belum ada order hari ini.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="admin-section-label mb-0">Akses Cepat</div>
    </div>
    <div class="row g-3 mt-1">
        <div class="col-md-3">
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-accent admin-card-body h-100" style="border-top-color: var(--admin-primary);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon-badge">
                            <i class="bi bi-receipt"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Orders</div>
                            <div class="text-muted small">Transaksi, retry, force success</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.games.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-accent accent-yellow admin-card-body h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon-badge accent-yellow">
                            <i class="bi bi-controller"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Games</div>
                            <div class="text-muted small">Kelola daftar game &amp; banner</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.categories.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-accent accent-pink admin-card-body h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon-badge accent-pink">
                            <i class="bi bi-tags-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Categories</div>
                            <div class="text-muted small">Diamond, Battle Pass, Skin, dll</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.products.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-accent accent-mint admin-card-body h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon-badge accent-mint">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">Products &amp; SKUs</div>
                            <div class="text-muted small">Harga, margin, stok</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js"></script>
<script>
    const salesTrendCtx = document.getElementById('salesTrendChart');
    const salesTrendData = @json($trendData);

    if (typeof Chart === 'undefined') {
        console.error('Chart.js gagal dimuat dari CDN. Cek koneksi internet atau apakah cdnjs.cloudflare.com diblokir jaringan/firewall.');
    } else {
    new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: salesTrendData.map(row => row.label),
            datasets: [{
                label: 'Revenue',
                data: salesTrendData.map(row => row.revenue),
                borderColor: '#6C4FF0',
                backgroundColor: 'rgba(108, 79, 240, 0.12)',
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#6C4FF0',
            }],
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    ticks: {
                        callback: (value) => 'Rp' + Number(value).toLocaleString('id-ID'),
                    },
                },
            },
        },
    });
    }
</script>
@endpush