@extends('layouts.admin')

@section('title', 'Provider Performance Report')
@section('page-title', 'Provider Performance Report')
@section('page-subtitle', 'Perbandingan performa tiap provider top up dalam menangani order')

@php
    $indexRoute = 'admin.reports.provider-performance';
    $exportRoute = 'admin.reports.provider-performance.export';
@endphp

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <div class="admin-page-title mb-0">Ringkasan Provider</div>
                <div class="admin-page-subtitle">{{ $from->translatedFormat('d M Y') }} &mdash; {{ $to->translatedFormat('d M Y') }}</div>
            </div>
            @include('admin.reports.partials.filter-bar')
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Provider</th>
                            <th>Status</th>
                            <th>Total Order</th>
                            <th>Success</th>
                            <th>Failed</th>
                            <th>Success Rate</th>
                            <th>Error Log</th>
                            <th>Timeout Log</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report as $row)
                            <tr>
                                <td class="fw-semibold text-dark">{{ $row['name'] }}</td>
                                <td>
                                    @if ($row['is_active'])
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td>{{ $row['total_orders'] }}</td>
                                <td>{{ $row['success_count'] }}</td>
                                <td>{{ $row['failed_count'] }}</td>
                                <td>
                                    @if ($row['success_rate'] !== null)
                                        <span class="badge {{ $row['success_rate'] >= 90 ? 'badge-soft-success' : ($row['success_rate'] >= 70 ? 'badge-soft-primary' : 'badge-soft-danger') }}">
                                            {{ $row['success_rate'] }}%
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($row['error_count'] > 0)
                                        <span class="badge badge-soft-danger">{{ $row['error_count'] }}</span>
                                    @else
                                        {{ $row['error_count'] }}
                                    @endif
                                </td>
                                <td>
                                    @if ($row['timeout_count'] > 0)
                                        <span class="badge badge-soft-danger">{{ $row['timeout_count'] }}</span>
                                    @else
                                        {{ $row['timeout_count'] }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">Belum ada provider terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-primary border-0 mt-3" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
        <i class="bi bi-info-circle me-1"></i>
        "Total Order" &amp; "Success/Failed" dihitung dari order yang pernah ditugaskan ke provider tersebut
        (kolom <code>provider_id</code>). "Error Log" &amp; "Timeout Log" diambil dari <strong>API &amp; Webhook Logs</strong>
        — kalau angkanya tinggi tapi Success Rate masih bagus, biasanya berarti auto-failover ke provider backup berjalan baik.
    </div>
@endsection
