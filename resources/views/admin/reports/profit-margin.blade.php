@extends('layouts.admin')

@section('title', 'Profit / Margin Report')
@section('page-title', 'Profit / Margin Report')
@section('page-subtitle', 'Margin keuntungan dari order yang berhasil diproses (status Success)')

@php
    $indexRoute = 'admin.reports.profit-margin';
    $exportRoute = 'admin.reports.profit-margin.export';
@endphp

@section('content')
    <div class="row g-3 mb-1">
        <div class="col-md-3 col-6">
            <div class="admin-card admin-card-accent admin-card-body h-100" style="border-top-color: var(--admin-primary);">
                <div class="text-muted small">Total Revenue</div>
                <div class="fw-bold text-dark fs-5">Rp{{ number_format($report['total_revenue'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="admin-card admin-card-accent accent-yellow admin-card-body h-100">
                <div class="text-muted small">Total Cost</div>
                <div class="fw-bold text-dark fs-5">Rp{{ number_format($report['total_cost'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="admin-card admin-card-accent accent-mint admin-card-body h-100">
                <div class="text-muted small">Total Profit</div>
                <div class="fw-bold text-dark fs-5">Rp{{ number_format($report['total_profit'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="admin-card admin-card-accent accent-pink admin-card-body h-100">
                <div class="text-muted small">Margin Rata-rata</div>
                <div class="fw-bold text-dark fs-5">{{ $report['total_margin_percent'] !== null ? $report['total_margin_percent'].'%' : '-' }}</div>
            </div>
        </div>
    </div>

    <div class="admin-card mb-3">
        <div class="admin-card-body">
            <canvas id="profitChart" height="80"></canvas>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <div class="admin-page-title mb-0">Rincian Harian</div>
                <div class="admin-page-subtitle">{{ $from->translatedFormat('d M Y') }} &mdash; {{ $to->translatedFormat('d M Y') }}</div>
            </div>
            @include('admin.reports.partials.filter-bar')
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Revenue</th>
                            <th>Cost</th>
                            <th>Profit</th>
                            <th>Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report['daily'] as $row)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($row['date'])->translatedFormat('d M Y') }}</td>
                                <td>Rp{{ number_format($row['revenue'], 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($row['cost'], 0, ',', '.') }}</td>
                                <td class="fw-semibold text-dark">Rp{{ number_format($row['profit'], 0, ',', '.') }}</td>
                                <td>
                                    @if ($row['margin_percent'] !== null)
                                        <span class="badge badge-soft-mint">{{ $row['margin_percent'] }}%</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">Tidak ada order Success pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-primary border-0 mt-3" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
        <i class="bi bi-info-circle me-1"></i>
        Laporan ini hanya menghitung order berstatus <strong>Success</strong>, karena harga modal (cost_price) baru
        tercatat otomatis begitu provider berhasil memproses top up.
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.umd.min.js"></script>
<script>
    const profitData = @json($report['daily']);

    if (typeof Chart === 'undefined') {
        console.error('Chart.js gagal dimuat dari CDN. Cek koneksi internet atau apakah cdnjs.cloudflare.com diblokir jaringan/firewall.');
    } else {
    new Chart(document.getElementById('profitChart'), {
        type: 'line',
        data: {
            labels: profitData.map(row => {
                const d = new Date(row.date + 'T00:00:00');
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
            }),
            datasets: [
                {
                    label: 'Revenue',
                    data: profitData.map(row => row.revenue),
                    borderColor: '#5B21B6',
                    backgroundColor: 'rgba(91,33,182,0.08)',
                    tension: 0.35,
                },
                {
                    label: 'Profit',
                    data: profitData.map(row => row.profit),
                    borderColor: '#34E4B8',
                    backgroundColor: 'rgba(52,228,184,0.12)',
                    fill: true,
                    tension: 0.35,
                },
            ],
        },
        options: {
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: { ticks: { callback: (value) => 'Rp' + Number(value).toLocaleString('id-ID') } },
            },
        },
    });
    }
</script>
@endpush
