@extends('layouts.admin')

@php
    $typeLabel = [
        'request'  => ['Request', '#2563EB'],
        'response' => ['Response', '#22C55E'],
        'webhook'  => ['Webhook', '#5B21B6'],
        'error'    => ['Error', '#EF4444'],
        'timeout'  => ['Timeout', '#F59E0B'],
    ];
    [$label, $color] = $typeLabel[$apiLog->type] ?? [$apiLog->type, '#6B6482'];

    $pretty = function (?string $raw) {
        if (! $raw) return null;
        $decoded = json_decode($raw, true);
        return $decoded === null ? $raw : json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    };
@endphp

@section('title', 'Detail Log')
@section('page-title', 'Detail API Log #'.$apiLog->id)
@section('page-subtitle', $apiLog->created_at->format('d M Y, H:i:s'))

@section('content')

    <a href="{{ route('admin.api-logs.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Log
    </a>

    <div class="admin-card admin-card-body mb-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="admin-page-title mb-0" style="font-size:1.1rem;">Ringkasan</div>
            <span class="admin-status-pill" style="background:{{ $color }}1F; color:{{ $color }};">{{ $label }}</span>
        </div>

        <div class="admin-info-row">
            <span class="admin-info-row-label">Provider</span>
            <span class="admin-info-row-value">{{ $apiLog->provider->name ?? '-' }}</span>
        </div>
        <div class="admin-info-row">
            <span class="admin-info-row-label">Order</span>
            <span class="admin-info-row-value">
                @if ($apiLog->order)
                    <a href="{{ route('admin.orders.show', $apiLog->order) }}">{{ $apiLog->order->invoice_number }}</a>
                @else
                    -
                @endif
            </span>
        </div>
        <div class="admin-info-row">
            <span class="admin-info-row-label">HTTP Status</span>
            <span class="admin-info-row-value">{{ $apiLog->http_status ?? '-' }}</span>
        </div>
    </div>

    @if ($apiLog->headers)
        <div class="admin-card admin-card-body mb-3">
            <div class="admin-page-title mb-2" style="font-size:1rem;">Headers</div>
            <pre class="admin-log-block">{{ $pretty($apiLog->headers) }}</pre>
        </div>
    @endif

    @if ($apiLog->payload)
        <div class="admin-card admin-card-body mb-3">
            <div class="admin-page-title mb-2" style="font-size:1rem;">Payload (data yang dikirim)</div>
            <pre class="admin-log-block">{{ $pretty($apiLog->payload) }}</pre>
        </div>
    @endif

    @if ($apiLog->response)
        <div class="admin-card admin-card-body">
            <div class="admin-page-title mb-2" style="font-size:1rem;">Response (data yang diterima)</div>
            <pre class="admin-log-block">{{ $pretty($apiLog->response) }}</pre>
        </div>
    @endif

@endsection
