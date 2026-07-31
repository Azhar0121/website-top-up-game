@extends('layouts.admin')

@section('title', 'Payment Gateway Settings')
@section('page-title', 'Payment Gateway Settings')
@section('page-subtitle', 'Kelola metode pembayaran yang tersedia saat checkout')

@section('content')

    @if (session('warning'))
        <div class="alert alert-warning border-0 mb-3">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('warning') }}
        </div>
    @endif

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $gateways->count() }} Payment Gateway</div>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Gateway</th>
                            <th>Mode</th>
                            <th>Kredensial</th>
                            <th>Status</th>
                            <th class="text-end" style="width:170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gateways as $gateway)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $gateway->name }}</div>
                                    <div class="text-muted small">{{ $gateway->code }}</div>
                                    @if (! in_array($gateway->code, $implementedCodes, true))
                                        <span class="badge badge-soft-muted mt-1">Belum diimplementasikan</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($gateway->is_sandbox)
                                        <span class="badge badge-soft-primary">Sandbox</span>
                                    @else
                                        <span class="badge badge-soft-pink">Production</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    @if ($gateway->code === 'midtrans')
                                        Dikelola via <code>.env</code>
                                    @else
                                        {{ $gateway->api_key ? 'Terisi' : 'Belum diisi' }}
                                    @endif
                                </td>
                                <td>
                                    @if ($gateway->is_active)
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('admin.payment-gateways.toggle', $gateway) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $gateway->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $gateway->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.payment-gateways.edit', $gateway) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-primary border-0 mt-3" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
        <i class="bi bi-info-circle me-1"></i>
        Kredensial Midtrans (yang aktif dipakai untuk checkout sekarang) tersimpan di file <code>.env</code>, bukan di sini — ini keputusan desain supaya kredensial produksi tidak pernah tersimpan di database. Duitku & Tripay disiapkan untuk masa depan, tapi belum ada implementasi service yang lengkap - jangan diaktifkan kecuali sudah siap.
    </div>

@endsection
