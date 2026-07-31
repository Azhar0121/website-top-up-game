@extends('layouts.admin')

@section('title', 'Edit Payment Gateway')
@section('page-title', 'Edit Payment Gateway')
@section('page-subtitle', $gateway->name)

@section('content')
    <div class="admin-card" style="max-width: 640px;">
        <div class="admin-card-header">
            <div class="admin-page-title mb-0">Edit: {{ $gateway->name }}</div>
            <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="admin-card-body">

            @if (! $isImplemented)
                <div class="alert alert-warning border-0">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Service untuk gateway ini belum terhubung ke <code>PaymentGatewayServiceFactory</code>. Kalau diaktifkan, checkout akan gagal untuk customer yang memilihnya.
                </div>
            @endif

            @if ($isEnvManaged)
                <div class="alert alert-primary border-0" style="background: rgba(91,33,182,.08); color: var(--admin-primary);">
                    <i class="bi bi-shield-lock-fill me-1"></i>
                    Kredensial <strong>{{ $gateway->name }}</strong> dikelola lewat file <code>.env</code> server (<code>MIDTRANS_SERVER_KEY</code>, dll), bukan dari form ini — jadi field kredensial tidak ditampilkan di sini.
                </div>
            @endif

            <form action="{{ route('admin.payment-gateways.update', $gateway) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Nama Tampilan <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $gateway->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 d-flex align-items-end gap-4">
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="is_sandbox" id="is_sandbox" value="1" class="form-check-input"
                                   {{ old('is_sandbox', $gateway->is_sandbox) ? 'checked' : '' }}>
                            <label for="is_sandbox" class="form-check-label">Mode Sandbox</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input"
                                   {{ old('is_active', $gateway->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">Aktif</label>
                        </div>
                    </div>

                    @unless ($isEnvManaged)
                        <div class="col-12"><hr class="my-1"></div>

                        <div class="col-md-6">
                            <label for="merchant_code" class="form-label fw-semibold">Merchant Code</label>
                            <input type="text" name="merchant_code" id="merchant_code"
                                   class="form-control @error('merchant_code') is-invalid @enderror"
                                   placeholder="{{ $gateway->merchant_code ? 'Biarkan kosong kalau tidak ingin ganti' : '' }}">
                            @error('merchant_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="api_key" class="form-label fw-semibold">API Key</label>
                            <input type="password" name="api_key" id="api_key" autocomplete="new-password"
                                   class="form-control @error('api_key') is-invalid @enderror"
                                   placeholder="{{ $gateway->api_key ? 'Biarkan kosong kalau tidak ingin ganti' : '' }}">
                            @error('api_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="api_secret" class="form-label fw-semibold">API Secret</label>
                            <input type="password" name="api_secret" id="api_secret" autocomplete="new-password"
                                   class="form-control @error('api_secret') is-invalid @enderror"
                                   placeholder="{{ $gateway->api_secret ? 'Biarkan kosong kalau tidak ingin ganti' : '' }}">
                            @error('api_secret') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Tersimpan terenkripsi di database. Kredensial yang sudah ada tidak pernah ditampilkan lagi di sini.</div>
                        </div>
                    @endunless
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-admin-primary px-4">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.payment-gateways.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
