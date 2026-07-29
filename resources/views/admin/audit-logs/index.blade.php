@extends('layouts.admin')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')
@section('page-subtitle', 'Riwayat aktivitas semua admin - siapa, aksi apa, kapan, dan dari IP mana')

@section('content')

    <div class="admin-card mb-3">
        <div class="admin-card-body">
            <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           class="form-control form-control-sm" placeholder="Cari deskripsi aktivitas...">
                </div>
                <div class="col-md-2">
                    <select name="action" class="form-select form-select-sm">
                        <option value="">Semua Aksi</option>
                        @foreach ($actionOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['action'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua Admin</option>
                        @foreach ($staffUsers as $staff)
                            <option value="{{ $staff->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $staff->id)>{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    <button type="submit" class="btn btn-sm btn-admin-primary">
                        <i class="bi bi-funnel"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $logs->total() }} Aktivitas Tercatat</div>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th style="width:150px;">Waktu</th>
                            <th style="width:160px;">Admin</th>
                            <th style="width:130px;">Aksi</th>
                            <th>Deskripsi</th>
                            <th style="width:130px;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td class="text-muted small">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td>{{ $log->user?->name ?? 'Sistem' }}</td>
                                <td><span class="badge {{ $log->badgeClass() }}">{{ $log->actionLabel() }}</span></td>
                                <td>
                                    {{ $log->description }}

                                    @if (! empty($log->changes))
                                        <a href="#" class="small ms-1" data-bs-toggle="collapse" data-bs-target="#changes-{{ $log->id }}">
                                            Lihat detail perubahan
                                        </a>
                                        <div class="collapse mt-1" id="changes-{{ $log->id }}">
                                            <table class="table table-sm table-borderless mb-0 small bg-light rounded">
                                                <thead>
                                                    <tr class="text-muted">
                                                        <th>Field</th>
                                                        <th>Sebelum</th>
                                                        <th>Sesudah</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($log->changes as $field => $value)
                                                        <tr>
                                                            <td class="fw-semibold">{{ $field }}</td>
                                                            @if (is_array($value) && array_key_exists('old', $value))
                                                                <td class="text-danger">{{ is_array($value['old']) ? implode(', ', $value['old']) : $value['old'] }}</td>
                                                                <td class="text-success">{{ is_array($value['new']) ? implode(', ', $value['new']) : $value['new'] }}</td>
                                                            @else
                                                                <td colspan="2">{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada aktivitas tercatat.</td>
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
