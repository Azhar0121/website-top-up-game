<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * CRUD Game untuk Dashboard Admin (PRD 6: menu "Games & Products").
 *
 * Catatan desain: business logic di sini masih tipis (cuma validasi + simpan file),
 * jadi TIDAK ditaruh di Service Layer terpisah - konsisten dengan keputusan yang sama
 * di App\Http\Controllers\Api\Admin\ProductController (margin logic yang lebih kompleks
 * itulah yang dipindah ke PriceSyncService). Kalau nanti ada logic tambahan (misal generate
 * banner otomatis, sinkron ke CDN, dll), baru layak dipisah ke App\Services\GameService.
 */
class GameController extends Controller
{
    /**
     * GET /admin/games
     */
    public function index(Request $request)
    {
        $games = Game::withCount(['categories', 'products'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.games.index', compact('games'));
    }

    /**
     * GET /admin/games/create
     */
    public function create()
    {
        $game = new Game();

        return view('admin.games.form', compact('game'));
    }

    /**
     * POST /admin/games
     */
    public function store(Request $request)
    {
        $validated = $this->validateGame($request);

        $validated['slug'] = $this->uniqueSlug($validated['name']);
        $validated['banner_image'] = $this->storeImage($request, 'banner_image', 'games/banners');
        $validated['logo_image'] = $this->storeImage($request, 'logo_image', 'games/logos');
        $validated['is_favorite'] = $request->boolean('is_favorite');
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_active'] = $request->boolean('is_active', true);

        Game::create($validated);

        return redirect()->route('admin.games.index')
            ->with('status', 'Game baru berhasil ditambahkan.');
    }

    /**
     * GET /admin/games/{game}/edit
     */
    public function edit(Game $game)
    {
        return view('admin.games.form', compact('game'));
    }

    /**
     * PUT /admin/games/{game}
     */
    public function update(Request $request, Game $game)
    {
        $validated = $this->validateGame($request, $game->id);

        // Slug cuma di-generate ulang kalau nama berubah, supaya URL /game/{slug} yang sudah
        // dibagikan/di-bookmark customer tidak tiba-tiba berubah setiap kali admin edit game.
        if ($validated['name'] !== $game->name) {
            $validated['slug'] = $this->uniqueSlug($validated['name'], $game->id);
        }

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $this->storeImage($request, 'banner_image', 'games/banners', $game->banner_image);
        }

        if ($request->hasFile('logo_image')) {
            $validated['logo_image'] = $this->storeImage($request, 'logo_image', 'games/logos', $game->logo_image);
        }

        $validated['is_favorite'] = $request->boolean('is_favorite');
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_active'] = $request->boolean('is_active');

        $game->update($validated);

        return redirect()->route('admin.games.index')
            ->with('status', "Game \"{$game->name}\" berhasil diupdate.");
    }

    /**
     * DELETE /admin/games/{game}
     */
    public function destroy(Game $game)
    {
        // Cascade delete kategori & produk sudah diatur di migration (cascadeOnDelete),
        // tapi kita tetap kasih peringatan jelas di UI (lihat konfirmasi modal di index.blade.php)
        // karena ini operasi destruktif dan tidak bisa dibatalkan.
        if ($game->banner_image) {
            Storage::disk('public')->delete($game->banner_image);
        }

        if ($game->logo_image) {
            Storage::disk('public')->delete($game->logo_image);
        }

        $game->delete();

        return redirect()->route('admin.games.index')
            ->with('status', "Game \"{$game->name}\" berhasil dihapus.");
    }

    /**
     * Validasi form Game. $ignoreId dipakai supaya unique check slug tidak bentrok
     * dengan data game itu sendiri saat update.
     */
    private function validateGame(Request $request, ?int $ignoreId = null): array
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:150',
            'tutorial_text' => 'nullable|string',
            'description'   => 'nullable|string',
            'banner_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'logo_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
            'is_favorite'   => 'nullable|boolean',
            'is_popular'    => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
        ], [
            'banner_image.max' => 'Ukuran banner maksimal 2MB.',
            'logo_image.max'   => 'Ukuran logo maksimal 1MB.',
        ]);

        $validator->validate();

        return $validator->validated();
    }

    /**
     * Generate slug unik dari nama game (dipakai untuk URL /game/{slug} di sisi customer).
     */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (
            Game::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Simpan file upload ke disk public, hapus file lama kalau ada (saat replace di form edit).
     */
    private function storeImage(Request $request, string $field, string $folder, ?string $oldPath = null): ?string
    {
        if (! $request->hasFile($field)) {
            return $oldPath;
        }

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $request->file($field)->store($folder, 'public');
    }
}
