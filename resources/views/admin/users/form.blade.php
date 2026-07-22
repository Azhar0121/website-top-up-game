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

                <label for="role" class="form-label fw-semibold">Role</label>
                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                    @foreach ($roles as $r)
                        <option value="{{ $r }}" {{ old('role', $currentRole) === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">
                    "Customer" cuma bisa belanja & lihat riwayat transaksi sendiri. Role lain (owner/admin/finance/cs/marketing/developer) dapat akses dashboard admin sesuai PRD 4.6.
                </div>

                <button type="submit" class="btn btn-admin-primary px-4 mt-3">
                    <i class="bi bi-check-lg"></i> Simpan Role
                </button>
            </form>
        </div>
    </div>
@endsection
