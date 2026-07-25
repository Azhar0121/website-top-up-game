@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional TopUp Kilat hari ini')

@section('content')
    <div class="row g-3 mb-1">
        <div class="col-md-3 col-6">
            <div class="admin-card admin-card-accent admin-card-body h-100" style="border-top-color: var(--admin-primary);">
                <div class="d-flex align-items-center gap-3">
                    <div class="admin-icon-badge" style="background: linear-gradient(135deg, #5B21B6, #431693);">
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
        <div class="col-lg-8">
            <div class="admin-card h-100">
                <div class="admin-card-header">
                    <div>
                        <div class="admin-page-title mb-0">Tren Sales 7 Hari Terakhir</div>
                        <div class="admin-page-subtitle">Revenue harian dari order yang sudah dibayar</div>
                    </div>
                    <a href="{{ route('admin.reports.sales-revenue') }}" class="btn btn-admin-primary btn-sm">
                        <i class="bi bi-bar-chart-fill"></i> Lihat Laporan Lengkap
                    </a>
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
        <div class="col-md-3">
            <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">
                <div class="admin-card admin-card-accent admin-card-body h-100" style="border-top-color: var(--admin-primary);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-icon-badge" style="background: linear-gradient(135deg, #5B21B6, #431693);">
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
                            <div class="text-muted small">Kelola daftar game & banner</div>
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
    const salesTrendData = @json($kpi['last_7_days']);

    if (typeof Chart === 'undefined') {
        console.error('Chart.js gagal dimuat dari CDN. Cek koneksi internet atau apakah cdnjs.cloudflare.com diblokir jaringan/firewall.');
    } else {
    new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: salesTrendData.map(row => {
                const d = new Date(row.date + 'T00:00:00');
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
            }),
            datasets: [{
                label: 'Revenue',
                data: salesTrendData.map(row => row.revenue),
                borderColor: '#FF5D8F',
                backgroundColor: 'rgba(255, 93, 143, 0.12)',
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#FF5D8F',
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
