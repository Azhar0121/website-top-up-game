@extends('layouts.admin')

@section('title', 'Detail Keluhan')
@section('page-title', 'Detail Keluhan')
@section('page-subtitle', $complaint->subject)

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.complaints.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke daftar keluhan</a>
        <span class="admin-status-pill {{ match($complaint->status) {
            'open' => 'badge-soft-danger',
            'in_progress' => 'badge-soft-primary',
            'resolved' => 'badge-soft-success',
            default => 'badge-soft-muted',
        } }}">{{ $complaint->statusLabel() }}</span>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="admin-card mb-3">
                <div class="admin-card-header"><div class="fw-bold">Informasi Pengirim</div></div>
                <div class="admin-card-body">
                    <div class="admin-info-row"><span class="admin-info-row-label">Nama</span><span class="admin-info-row-value">{{ $complaint->name }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Email</span><span class="admin-info-row-value"><a href="mailto:{{ $complaint->email }}">{{ $complaint->email }}</a></span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">WhatsApp</span><span class="admin-info-row-value">{{ $complaint->whatsapp ?: '-' }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Akun terdaftar</span><span class="admin-info-row-value">{{ $complaint->user?->name ?? 'Tamu (tanpa login)' }}</span></div>
                    <div class="admin-info-row"><span class="admin-info-row-label">Dikirim pada</span><span class="admin-info-row-value">{{ $complaint->created_at->format('d M Y, H:i') }}</span></div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><div class="fw-bold">Isi Keluhan</div></div>
                <div class="admin-card-body">
                    <div class="admin-info-row"><span class="admin-info-row-label">Subjek</span><span class="admin-info-row-value">{{ $complaint->subject }}</span></div>
                    <p class="mt-2" style="white-space: pre-line;">{{ $complaint->message }}</p>

                    @if ($complaint->image_path)
                        <div class="mt-3">
                            <div class="small text-muted mb-2">Lampiran gambar:</div>
                            <a href="{{ asset('storage/'.$complaint->image_path) }}" target="_blank" rel="noopener">
                                <img src="{{ asset('storage/'.$complaint->image_path) }}" alt="Lampiran keluhan" style="max-width: 100%; border-radius: 12px; border: 1px solid #E9E5F5;">
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="admin-card">
                <div class="admin-card-header"><div class="fw-bold">Update Status</div></div>
                <div class="admin-card-body">
                    <form method="POST" action="{{ route('admin.complaints.update-status', $complaint) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Status</label>
                            <select name="status" class="form-select">
                                <option value="open" {{ $complaint->status === 'open' ? 'selected' : '' }}>Baru</option>
                                <option value="in_progress" {{ $complaint->status === 'in_progress' ? 'selected' : '' }}>Diproses</option>
                                <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Catatan Internal (opsional)</label>
                            <textarea name="admin_note" rows="4" class="form-control" placeholder="Catatan untuk tim, tidak dikirim ke customer.">{{ old('admin_note', $complaint->admin_note) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-admin-primary w-100">Simpan Perubahan</button>
                    </form>

                    @if ($complaint->handledBy)
                        <p class="small text-muted mt-3 mb-0">Terakhir ditangani oleh {{ $complaint->handledBy->name }}.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
