<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Game;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * GET /admin/categories/create
     */
    public function create()
    {
        $category = new Category();
        $games = Game::orderBy('name')->get(['id', 'name']);

        return view('admin.categories.form', compact('category', 'games'));
    }

    /**
     * POST /admin/categories
     */
    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $validated['is_active'] = $request->boolean('is_active', true);

        $category = Category::create($validated);

        AuditLogService::record(
            action: 'created',
            description: "Membuat kategori \"{$category->name}\".",
            subject: $category,
        );

        return redirect()->route('admin.categories.index')
            ->with('status', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * GET /admin/categories/{category}/edit
     */
    public function edit(Category $category)
    {
        $games = Game::orderBy('name')->get(['id', 'name']);

        return view('admin.categories.form', compact('category', 'games'));
    }

    /**
     * PUT /admin/categories/{category}
     */
    public function update(Request $request, Category $category)
    {
        $validated = $this->validateCategory($request);
        $validated['is_active'] = $request->boolean('is_active');

        $before = $category->only(array_keys($validated));
        $category->update($validated);
        $changes = AuditLogService::diff($before, $category->only(array_keys($validated)));

        if (! empty($changes)) {
            AuditLogService::record(
                action: 'updated',
                description: "Mengedit kategori \"{$category->name}\".",
                subject: $category,
                changes: $changes,
            );
        }

        return redirect()->route('admin.categories.index')
            ->with('status', "Kategori \"{$category->name}\" berhasil diupdate.");
    }

    /**
     * DELETE /admin/categories/{category}
     */
    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', "Kategori \"{$category->name}\" tidak bisa dihapus karena masih punya produk di dalamnya. Pindahkan atau hapus dulu produknya.");
        }

        $categoryName = $category->name;
        $category->delete();

        AuditLogService::record(
            action: 'deleted',
            description: "Menghapus kategori \"{$categoryName}\".",
            subject: $category,
        );

        return redirect()->route('admin.categories.index')
            ->with('status', "Kategori \"{$categoryName}\" berhasil dihapus.");
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