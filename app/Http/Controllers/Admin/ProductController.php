<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Game;
use App\Models\Product;
use App\Services\PriceSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * CRUD Product untuk Dashboard Admin (PRD 3 & 4.3: katalog produk + multi-margin).
 *
 * File ini SENGAJA terpisah dari App\Http\Controllers\Api\Admin\ProductController yang
 * sudah ada. Controller API itu isinya cuma 2 aksi khusus (updateMargin & syncPrices) yang
 * dipakai lewat token Sanctum - dipertahankan apa adanya untuk kebutuhan integrasi luar/mobile
 * di masa depan. Controller ini (berbasis session Blade) menangani CRUD lengkap produk untuk
 * dashboard admin yang sedang dibangun sekarang. Untuk hitung ulang harga otomatis, kedua
 * controller sama-sama memanggil App\Services\PriceSyncService supaya logic-nya tidak dobel.
 */
class ProductController extends Controller
{
    /**
     * GET /admin/products
     */
    public function index(Request $request)
    {
        $games = Game::orderBy('name')->get(['id', 'name']);

        $products = Product::with(['game', 'category'])
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'))
            ->when($request->filled('game_id'), fn ($q) => $q->where('game_id', $request->game_id))
            ->orderBy('game_id')
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'games'));
    }

    /**
     * GET /admin/products/create
     */
    public function create()
    {
        $product = new Product();
        $games = Game::orderBy('name')->get(['id', 'name']);
        // Kategori di-load semua lalu difilter di sisi client via JS berdasarkan game yang
        // dipilih (lihat public/js/admin/product-form.js) - jumlah kategori realistis kecil,
        // jadi tidak perlu request AJAX terpisah untuk ini.
        $categories = Category::orderBy('game_id')->orderBy('sort_order')->get(['id', 'game_id', 'name']);

        return view('admin.products.form', compact('product', 'games', 'categories'));
    }

    /**
     * POST /admin/products
     */
    public function store(Request $request, PriceSyncService $priceSyncService)
    {
        $validated = $this->validateProduct($request);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['auto_price'] = $request->boolean('auto_price');

        $product = Product::create($validated);

        // Kalau admin menyalakan auto_price tapi produk ini belum punya provider mapping
        // sama sekali (baru dibuat), sync() akan otomatis skip (return null) - lihat
        // PriceSyncService::sync(). Jadi aman dipanggil di sini walau belum ada apa-apa.
        if ($product->auto_price) {
            $priceSyncService->sync($product);
        }

        return redirect()->route('admin.products.index')
            ->with('status', "Produk \"{$product->name}\" berhasil ditambahkan.");
    }

    /**
     * GET /admin/products/{product}/edit
     */
    public function edit(Product $product)
    {
        $games = Game::orderBy('name')->get(['id', 'name']);
        $categories = Category::orderBy('game_id')->orderBy('sort_order')->get(['id', 'game_id', 'name']);

        return view('admin.products.form', compact('product', 'games', 'categories'));
    }

    /**
     * PUT /admin/products/{product}
     */
    public function update(Request $request, Product $product, PriceSyncService $priceSyncService)
    {
        $validated = $this->validateProduct($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['auto_price'] = $request->boolean('auto_price');

        $product->update($validated);

        if ($product->auto_price) {
            $priceSyncService->sync($product->fresh());
        }

        return redirect()->route('admin.products.index')
            ->with('status', "Produk \"{$product->name}\" berhasil diupdate.");
    }

    /**
     * DELETE /admin/products/{product}
     */
    public function destroy(Product $product)
    {
        // providerProducts ikut terhapus otomatis (cascadeOnDelete di migration provider_products),
        // tapi kalau produk ini pernah dipakai di order, biarkan FK constraint yang mencegah -
        // tidak kita override manual, karena menghapus produk yang punya histori transaksi
        // akan merusak integritas laporan keuangan (PRD 6: Reports -> Sales & Revenue Report).
        try {
            $product->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', "Produk \"{$product->name}\" tidak bisa dihapus karena sudah punya riwayat transaksi. Nonaktifkan saja produk ini jika tidak ingin dijual lagi.");
        }

        return redirect()->route('admin.products.index')
            ->with('status', "Produk \"{$product->name}\" berhasil dihapus.");
    }

    private function validateProduct(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'game_id'      => 'required|exists:games,id',
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:150',
            'region'       => 'required|string|max:50',
            'base_price'   => 'required|numeric|min:0',
            'stock'        => 'nullable|integer|min:0',
            'sort_order'   => 'nullable|integer|min:0',
            'margin_type'  => 'required|in:fixed,percentage',
            'margin_value' => 'required|numeric|min:0',
            'is_active'    => 'nullable|boolean',
            'auto_price'   => 'nullable|boolean',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
        ]);

        $validator->after(function ($validator) use ($request) {
            // Pastikan kategori yang dipilih memang milik game yang dipilih - mencegah
            // data nyasar kalau ada yang iseng ubah value <option> lewat DevTools.
            if ($request->filled('game_id') && $request->filled('category_id')) {
                $belongs = Category::where('id', $request->category_id)
                    ->where('game_id', $request->game_id)
                    ->exists();

                if (! $belongs) {
                    $validator->errors()->add('category_id', 'Kategori yang dipilih tidak sesuai dengan game.');
                }
            }
        });

        $validated = $validator->validate();
        $validated['stock'] = $validated['stock'] ?? null;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }
}