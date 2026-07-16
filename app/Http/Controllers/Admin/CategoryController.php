<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * CRUD Category (PRD 3: pembagian kategori produk - Diamond, Battle Pass, Gift Card, Skin, dsb).
 *
 * Category cuma punya 3 kolom isian (game, nama, urutan) jadi sengaja TIDAK dibuatkan halaman
 * create/edit terpisah seperti Game & Product - form tambah/edit ditaruh dalam modal Bootstrap
 * di halaman index yang sama (lihat admin.categories.index), supaya alur kerja admin lebih cepat
 * untuk data sesederhana ini.
 */
class CategoryController extends Controller
{
    /**
     * GET /admin/categories
     */
    public function index(Request $request)
    {
        $games = Game::orderBy('name')->get(['id', 'name']);

        $categories = Category::with('game')
            ->withCount('products')
            ->when($request->filled('game_id'), fn ($q) => $q->where('game_id', $request->game_id))
            ->orderBy('game_id')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories', 'games'));
    }

    /**
     * POST /admin/categories
     */
    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $validated['is_active'] = $request->boolean('is_active', true);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * PUT /admin/categories/{category}
     */
    public function update(Request $request, Category $category)
    {
        $validated = $this->validateCategory($request);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('status', "Kategori \"{$category->name}\" berhasil diupdate.");
    }

    /**
     * DELETE /admin/categories/{category}
     */
    public function destroy(Category $category)
    {
        // Cek manual dulu sebelum delete, supaya admin dapat pesan yang jelas kenapa gagal -
        // daripada mengandalkan foreign key constraint error dari MySQL yang membingungkan.
        if ($category->products()->exists()) {
            return back()->with('error', "Kategori \"{$category->name}\" tidak bisa dihapus karena masih punya produk di dalamnya. Pindahkan atau hapus dulu produknya.");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('status', "Kategori \"{$category->name}\" berhasil dihapus.");
    }

    private function validateCategory(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'game_id'    => 'required|exists:games,id',
            'name'       => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $validated = $validator->validate();
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }
}
