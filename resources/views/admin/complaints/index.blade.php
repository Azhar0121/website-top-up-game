@extends('layouts.admin')

@section('title', 'Keluhan Customer')
@section('page-title', 'Keluhan Customer')
@section('page-subtitle', 'Keluhan yang masuk lewat form /hubungi-kami/keluhan')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $complaints->total() }} Keluhan</div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.complaints.index') }}" class="btn btn-sm {{ request('status') ? 'btn-outline-secondary' : 'btn-admin-primary' }}">Semua</a>
                <a href="{{ route('admin.complaints.index', ['status' => 'open']) }}" class="btn btn-sm {{ request('status') === 'open' ? 'btn-admin-primary' : 'btn-outline-secondary' }}">Baru</a>
                <a href="{{ route('admin.complaints.index', ['status' => 'in_progress']) }}" class="btn btn-sm {{ request('status') === 'in_progress' ? 'btn-admin-primary' : 'btn-outline-secondary' }}">Diproses</a>
                <a href="{{ route('admin.complaints.index', ['status' => 'resolved']) }}" class="btn btn-sm {{ request('status') === 'resolved' ? 'btn-admin-primary' : 'btn-outline-secondary' }}">Selesai</a>
            </div>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Subjek</th>
                            <th>Pengirim</th>
                            <th>Status</th>
                            <th class="text-end" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($complaints as $complaint)
                            <tr>
                                <td class="small text-muted">{{ $complaint->created_at->format('d M Y, H:i') }}</td>
                                <td class="fw-semibold text-dark">
                                    {{ $complaint->subject }}
                                    @if ($complaint->image_path)
                                        <i class="bi bi-paperclip text-muted ms-1" title="Ada lampiran gambar"></i>
                                    @endif
                                </td>
                                <td>
                                    {{ $complaint->name }}<br>
                                    <span class="small text-muted">{{ $complaint->email }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ match($complaint->status) {
                                        'open' => 'badge-soft-danger',
                                        'in_progress' => 'badge-soft-primary',
                                        'resolved' => 'badge-soft-success',
                                        default => 'badge-soft-muted',
                                    } }}">{{ $complaint->statusLabel() }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.complaints.show', $complaint) }}" class="btn btn-sm btn-outline-secondary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">Belum ada keluhan yang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $complaints->links() }}
    </div>
@endsection
