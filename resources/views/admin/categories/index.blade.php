@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories')
@section('page-subtitle', 'Diamond, Battle Pass, Gift Card, Skin, dan pembagian produk lainnya')

@section('content')
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <div class="admin-page-title mb-0">Daftar Kategori</div>
                <div class="admin-page-subtitle">{{ $categories->total() }} kategori</div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex gap-2">
                    <select name="game_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Game</option>
                        @foreach ($games as $game)
                            <option value="{{ $game->id }}" {{ request('game_id') == $game->id ? 'selected' : '' }}>{{ $game->name }}</option>
                        @endforeach
                    </select>
                </form>
                <button type="button" class="btn btn-admin-primary btn-sm" data-bs-toggle="modal" data-bs-target="#categoryModal"
                        onclick="openCategoryModal()">
                    <i class="bi bi-plus-lg"></i> Tambah Kategori
                </button>
            </div>
        </div>

        <div class="admin-card-body p-0">
            <div class="table-responsive">
                <table class="table admin-table mb-0">
                    <thead>
                        <tr>
                            <th>Game</th>
                            <th>Nama Kategori</th>
                            <th>Urutan</th>
                            <th>Produk</th>
                            <th>Status</th>
                            <th class="text-end" style="width:140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->game->name ?? '-' }}</td>
                                <td class="fw-semibold text-dark">{{ $category->name }}</td>
                                <td>{{ $category->sort_order }}</td>
                                <td>{{ $category->products_count }}</td>
                                <td>
                                    @if ($category->is_active)
                                        <span class="badge badge-soft-success">Aktif</span>
                                    @else
                                        <span class="badge badge-soft-muted">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" title="Edit"
                                            onclick='openCategoryModal(@json($category))'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" data-confirm-delete="{{ $category->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($categories->hasPages())
            <div class="admin-card-body pt-0">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah/Edit Kategori -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="categoryForm" method="POST">
                    @csrf
                    <div id="categoryMethodField"></div>

                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalTitle">Tambah Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="modal_game_id" class="form-label fw-semibold">Game <span class="text-danger">*</span></label>
                            <select name="game_id" id="modal_game_id" class="form-select" required>
                                <option value="">-- Pilih Game --</option>
                                @foreach ($games as $game)
                                    <option value="{{ $game->id }}">{{ $game->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modal_name" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="modal_name" class="form-control" placeholder="Diamond" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal_sort_order" class="form-label fw-semibold">Urutan Tampil</label>
                            <input type="number" name="sort_order" id="modal_sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="modal_is_active" value="1" class="form-check-input" checked>
                            <label for="modal_is_active" class="form-check-label">Aktif / Tampil</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-admin-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    'use strict';

    // Kategori sengaja pakai satu modal untuk create & edit (lihat komentar di
    // CategoryController) - fungsi ini yang mengatur perbedaannya: ganti action URL,
    // method (POST vs PUT-spoofed), judul modal, dan isi field sesuai data yang diklik.
    function openCategoryModal(category) {
        const form = document.getElementById('categoryForm');
        const methodField = document.getElementById('categoryMethodField');
        const title = document.getElementById('categoryModalTitle');

        if (category) {
            form.action = `{{ url('admin/categories') }}/${category.id}`;
            methodField.innerHTML = '@method('PUT')';
            title.textContent = `Edit Kategori: ${category.name}`;
            document.getElementById('modal_game_id').value = category.game_id;
            document.getElementById('modal_name').value = category.name;
            document.getElementById('modal_sort_order').value = category.sort_order;
            document.getElementById('modal_is_active').checked = !!category.is_active;
        } else {
            form.action = "{{ route('admin.categories.store') }}";
            methodField.innerHTML = '';
            title.textContent = 'Tambah Kategori';
            form.reset();
            document.getElementById('modal_is_active').checked = true;
        }
    }
</script>
@endpush
