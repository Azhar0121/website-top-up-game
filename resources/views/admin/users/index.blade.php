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

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
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
                                <td colspan="6" class="text-center text-muted py-5">Belum ada user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($users->hasPages())
            <div class="admin-card-body pt-0">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
