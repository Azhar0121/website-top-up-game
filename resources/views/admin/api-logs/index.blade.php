@extends('layouts.admin')

@php
    $typeLabel = [
        'request'  => ['Request', '#2563EB'],
        'response' => ['Response', '#22C55E'],
        'webhook'  => ['Webhook', '#5B21B6'],
        'error'    => ['Error', '#EF4444'],
        'timeout'  => ['Timeout', '#F59E0B'],
    ];
@endphp

@section('title', 'API & Webhook Logs')
@section('page-title', 'API & Webhook Logs')
@section('page-subtitle', 'Raw data request/response ke provider & payment gateway, buat debugging')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.api-logs.index') }}" class="btn btn-sm {{ ! request('type') ? 'btn-admin-primary' : 'btn-outline-secondary' }}">
                    Semua ({{ $typeCounts->sum() }})
                </a>
                @foreach ($typeLabel as $key => [$label, $color])
                    <a href="{{ route('admin.api-logs.index', array_filter(['type' => $key, 'provider_id' => request('provider_id')])) }}"
                       class="btn btn-sm {{ request('type') === $key ? 'btn-admin-primary' : 'btn-outline-secondary' }}">
                        {{ $label }} ({{ $typeCounts[$key] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <form action="{{ route('admin.api-logs.index') }}" method="GET" class="d-flex gap-2 flex-wrap">
                @if (request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <select name="provider_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto;">
                    <option value="">Semua Provider</option>
                    @foreach ($providers as $provider)
                        <option value="{{ $provider->id }}" {{ (string) request('provider_id') === (string) $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari no. invoice...">
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Tipe</th>
                            <th>Invoice</th>
                            <th>Provider</th>
                            <th>HTTP Status</th>
                            <th>Waktu</th>
                            <th class="text-end" style="width:90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            @php [$label, $color] = $typeLabel[$log->type] ?? [$log->type, '#6B6482']; @endphp
                            <tr>
                                <td><span class="admin-status-pill" style="background:{{ $color }}1F; color:{{ $color }};">{{ $label }}</span></td>
                                <td>
                                    @if ($log->order)
                                        <a href="{{ route('admin.orders.show', $log->order) }}">{{ $log->order->invoice_number }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $log->provider->name ?? '-' }}</td>
                                <td>
                                    @if ($log->http_status)
                                        <span class="{{ $log->http_status >= 400 ? 'text-danger' : 'text-success' }} fw-semibold">{{ $log->http_status }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $log->created_at->format('d M Y, H:i:s') }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.api-logs.show', $log) }}" class="btn btn-sm btn-outline-secondary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada log tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($logs->hasPages())
            <div class="admin-card-body pt-0">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
