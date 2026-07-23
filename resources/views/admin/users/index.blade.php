@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users & Leveling')
@section('page-subtitle', 'Semua akun (customer & staff) dan role-nya')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm {{ ! request('role') ? 'btn-admin-primary' : 'btn-outline-secondary' }}">Semua</a>
                @foreach ($roles as $r)
                    <a href="{{ route('admin.users.index', ['role' => $r]) }}"
                       class="btn btn-sm {{ request('role') === $r ? 'btn-admin-primary' : 'btn-outline-secondary' }}">
                        {{ ucfirst($r) }}
                    </a>
                @endforeach
            </div>

            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
                @if (request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari nama/email...">
                <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        {{-- Satu form membungkus seluruh tabel: checkbox tiap baris otomatis ke-submit
             sebagai user_ids[] ke endpoint bulk-update-role, tidak perlu JS sama sekali. --}}
        <form action="{{ route('admin.users.bulk-update-role') }}" method="POST" id="bulk-role-form">
            @csrf

            <div class="admin-card-body py-2 d-flex align-items-center gap-2 flex-wrap" style="background: var(--admin-bg); border-bottom: 1px solid var(--admin-border);">
                <span class="small text-muted">Aksi massal untuk user terpilih:</span>
                <select name="role" class="form-select form-select-sm" style="width: auto;" required>
                    <option value="">Tambahkan role...</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r }}">{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-admin-primary"
                        onclick="return document.querySelectorAll('.user-row-check:checked').length > 0 || alert('Pilih minimal satu user dulu.');">
                    Terapkan
                </button>
                <span class="small text-muted">(role ditambahkan, bukan mengganti role yang sudah ada)</span>
            </div>

            <div class="admin-card-body p-0">
                <div class="table-responsive">
                    <table class="table admin-table mb-0">
                        <thead>
                            <tr>
                                <th style="width:36px;">
                                    <input type="checkbox" class="form-check-input" id="select-all-users">
                                </th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Login via</th>
                                <th>Bergabung</th>
                                <th class="text-end" style="width:90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-row-check">
                                    </td>
                                    <td class="fw-semibold text-dark">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @forelse ($user->roles as $role)
                                            <span class="badge badge-soft-primary">{{ $role->name }}</span>
                                        @empty
                                            <span class="badge badge-soft-muted">customer</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if ($user->google_id)
                                            <span class="badge badge-soft-mint"><i class="bi bi-google"></i> Google</span>
                                        @else
                                            <span class="badge badge-soft-muted">Email</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Ubah Role">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">Belum ada user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        @if ($users->hasPages())
            <div class="admin-card-body pt-0">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Checkbox "pilih semua" di header tabel - centang/uncentang semua baris di halaman ini sekaligus.
        document.getElementById('select-all-users').addEventListener('change', function () {
            document.querySelectorAll('.user-row-check').forEach((cb) => { cb.checked = this.checked; });
        });
    </script>
@endpush
