@extends('layouts.admin')

@section('title', 'Vouchers')
@section('page-title', 'Voucher & Promo Code')
@section('page-subtitle', 'Kode diskon yang bisa dipakai customer saat checkout')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $vouchers->total() }} Voucher</div>
            <div class="d-flex gap-2">
                <form action="{{ route('admin.vouchers.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari kode...">
                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                </form>
                <a href="{{ route('admin.vouchers.create') }}" class="btn btn-admin-primary btn-sm text-nowrap">
                    <i class="bi bi-plus-lg"></i> Tambah Voucher
                </a>
            </div>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Diskon</th>
                            <th>Min. Transaksi</th>
                            <th>Pemakaian</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th class="text-end" style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vouchers as $voucher)
                            @php
                                $isExpired = $voucher->end_date && $voucher->end_date->isPast();
                                $isQuotaFull = $voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit;
                            @endphp
                            <tr>
                                <td class="fw-semibold text-dark"><code>{{ $voucher->code }}</code></td>
                                <td>
                                    {{ $voucher->type === 'fixed' ? 'Rp'.number_format($voucher->value, 0, ',', '.') : $voucher->value.'%' }}
                                    @if ($voucher->type === 'percentage' && $voucher->max_discount)
                                        <div class="text-muted small">Maks Rp{{ number_format($voucher->max_discount, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td>Rp{{ number_format($voucher->min_transaction, 0, ',', '.') }}</td>
                                <td>{{ $voucher->used_count }}{{ $voucher->usage_limit ? ' / '.$voucher->usage_limit : ' / ∞' }}</td>
                                <td class="small text-muted">
                                    @if ($voucher->start_date || $voucher->end_date)
                                        {{ $voucher->start_date?->format('d/m/y') ?? '...' }} - {{ $voucher->end_date?->format('d/m/y') ?? '...' }}
                                    @else
                                        Tanpa batas waktu
                                    @endif
                                </td>
                                <td>
                                    @if (! $voucher->is_active)
                                        <span class="badge badge-soft-muted">Nonaktif</span>
                                    @elseif ($isExpired)
                                        <span class="badge badge-soft-danger">Kedaluwarsa</span>
                                    @elseif ($isQuotaFull)
                                        <span class="badge badge-soft-danger">Kuota Habis</span>
                                    @else
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus voucher {{ $voucher->code }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    Belum ada voucher. <a href="{{ route('admin.vouchers.create') }}">Buat yang pertama</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($vouchers->hasPages())
            <div class="admin-card-body pt-0">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>
@endsection
