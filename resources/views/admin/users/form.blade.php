@extends('layouts.admin')

@section('title', 'Edit Role')
@section('page-title', 'Edit Role')
@section('page-subtitle', $user->name)

@section('content')
    <div class="admin-card" style="max-width: 520px;">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">{{ $user->name }}</div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">
            @if (session('error'))
                <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
            @endif

            <div class="admin-info-row">
                <span class="admin-info-row-label">Email</span>
                <span class="admin-info-row-value">{{ $user->email }}</span>
            </div>
            <div class="admin-info-row">
                <span class="admin-info-row-label">Login via</span>
                <span class="admin-info-row-value">{{ $user->google_id ? 'Google' : 'Email & Password' }}</span>
            </div>
            <div class="admin-info-row mb-3">
                <span class="admin-info-row-label">Bergabung</span>
                <span class="admin-info-row-value">{{ $user->created_at->format('d M Y, H:i') }}</span>
            </div>

            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <label class="form-label fw-semibold">Role (boleh pilih lebih dari satu)</label>
                <div class="row g-2 mb-2">
                    @foreach ($roles as $r)
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" name="roles[]" value="{{ $r }}" id="role_{{ $r }}"
                                       class="form-check-input" {{ in_array($r, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                <label for="role_{{ $r }}" class="form-check-label">{{ ucfirst($r) }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('roles') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                <div class="form-text">
                    "Customer" cuma bisa belanja & lihat riwayat transaksi sendiri. Role lain (owner/admin/finance/cs/marketing/developer) dapat akses dashboard admin sesuai PRD 4.6 - satu user boleh punya kombinasi lebih dari satu role staff (misal Finance + Marketing).
                </div>

                <button type="submit" class="btn btn-admin-primary px-4 mt-3">
                    <i class="bi bi-check-lg"></i> Simpan Role
                </button>
            </form>
        </div>
    </div>
@endsection
