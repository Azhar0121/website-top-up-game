@extends('layouts.admin')

@section('title', 'Providers')
@section('page-title', 'Provider List & Priority')
@section('page-subtitle', 'Kelola provider top up, urutan prioritas, dan kredensial API')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $providers->count() }} Provider</div>
            <a href="{{ route('admin.providers.create') }}" class="btn btn-admin-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Provider
            </a>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:70px;">Prioritas</th>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Base URL</th>
                            <th>Status</th>
                            <th class="text-end" style="width:170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($providers as $provider)
                            <tr>
                                <td><span class="badge badge-soft-primary">#{{ $provider->priority }}</span></td>
                                <td class="fw-semibold text-dark">{{ $provider->name }}</td>
                                <td><code>{{ $provider->code }}</code></td>
                                <td class="text-muted small">{{ $provider->base_url }}</td>
                                <td>
                                    @if ($provider->is_active)
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('admin.providers.toggle', $provider) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $provider->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $provider->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.providers.edit', $provider) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada provider.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-primary border-0 mt-3" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
        <i class="bi bi-info-circle me-1"></i>
        Angka prioritas lebih kecil = dicoba lebih dulu saat ada order masuk. Kalau provider prioritas #1 gagal/timeout, sistem otomatis lempar ke provider aktif prioritas berikutnya (auto-failover, PRD 4.1).
    </div>
@endsection
