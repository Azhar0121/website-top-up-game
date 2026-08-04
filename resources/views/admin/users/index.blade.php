@extends('layouts.admin')

@section('title', 'Users')
@section('page-title', 'Users & Leveling')
@section('page-subtitle', 'Semua akun (customer & staff) dan role-nya')

@section('content')
    @if (session('status'))
        <div class="alert alert-success border-0 mb-3">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger border-0 mb-3">{{ session('error') }}</div>
    @endif

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

            <div class="d-flex gap-2">
                <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
                    @if (request('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari nama/email...">
                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-search"></i></button>
                </form>
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-admin-primary text-nowrap">
                    <i class="bi bi-person-plus-fill"></i> Tambah User
                </a>
            </div>
        </div>

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
                                <th>Status</th>
                                <th>Bergabung</th>
                                <th class="text-end" style="width:130px;">Aksi</th>
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
                                    <td>
                                        @if ($user->is_blocked)
                                            <span class="badge badge-soft-danger" title="{{ $user->blocked_reason }}">Diblokir</span>
                                        @else
                                            <span class="badge badge-soft-success">Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Ubah Role">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            @if ($user->is_blocked)
                                                <form action="{{ route('admin.users.unblock', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Buka Blokir">
                                                        <i class="bi bi-unlock-fill"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Blokir"
                                                        data-bs-toggle="modal" data-bs-target="#blockModal-{{ $user->id }}">
                                                    <i class="bi bi-slash-circle"></i>
                                                </button>

                                                <div class="modal fade" id="blockModal-{{ $user->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.users.block', $user) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Blokir {{ $user->name }}?</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="text-muted small">User ini tidak akan bisa login sampai dibuka blokirnya lagi. Sesi login yang sedang aktif juga langsung diputus.</p>
                                                                    <label class="form-label small fw-semibold">Alasan (opsional)</label>
                                                                    <input type="text" name="blocked_reason" class="form-control form-control-sm" placeholder="Misal: spam order, penyalahgunaan voucher">
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-sm btn-danger">Blokir User</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">Belum ada user.</td>
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
        document.getElementById('select-all-users').addEventListener('change', function () {
            document.querySelectorAll('.user-row-check').forEach((cb) => { cb.checked = this.checked; });
        });
    </script>
@endpush
