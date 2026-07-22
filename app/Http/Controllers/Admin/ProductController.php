<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Game;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ProviderProduct;
use App\Services\PriceSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * GET /admin/products
     */
    public function index(Request $request)
    {
        $games = Game::orderBy('name')->get(['id', 'name']);

        $products = Product::with(['game', 'category'])
            ->withCount('providerProducts')
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
        
        $categories = Category::orderBy('game_id')->orderBy('sort_order')->get(['id', 'game_id', 'name']);

        return view('admin.products.form', compact('product', 'games', 'categories'))
            ->with(['costPrice' => null, 'providerSkuCode' => null]);
    }

    public function store(Request $request, PriceSyncService $priceSyncService)
    {
        $validated = $this->validateProduct($request);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['auto_price'] = $request->boolean('auto_price');

        $costPrice = $validated['cost_price'];
        $providerSkuCode = $validated['provider_sku_code'] ?: null;
        unset($validated['cost_price'], $validated['provider_sku_code']); // bukan kolom tabel products

        $product = Product::create($validated);

        $this->syncProviderMapping($product, $costPrice, $providerSkuCode);

        if ($product->auto_price) {
            $priceSyncService->sync($product->fresh());
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

        $existingMapping = $product->providerProducts()->first();

        return view('admin.products.form', compact('product', 'games', 'categories'))
            ->with([
                'costPrice' => $existingMapping->cost_price ?? null,
                'providerSkuCode' => $existingMapping->provider_sku_code ?? null,
            ]);
    }

    /**
     * PUT /admin/products/{product}
     */
    public function update(Request $request, Product $product, PriceSyncService $priceSyncService)
    {
        $validated = $this->validateProduct($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['auto_price'] = $request->boolean('auto_price');

        $costPrice = $validated['cost_price'];
        $providerSkuCode = $validated['provider_sku_code'] ?: null;
        unset($validated['cost_price'], $validated['provider_sku_code']);

        $product->update($validated);

        $this->syncProviderMapping($product, $costPrice, $providerSkuCode);

        if ($product->auto_price) {
            $priceSyncService->sync($product->fresh());
        }

        return redirect()->route('admin.products.index')
            ->with('status', "Produk \"{$product->name}\" berhasil diupdate.");
    }

    private function syncProviderMapping(Product $product, float $costPrice, ?string $skuCode): void
    {
        $activeProviders = Provider::where('is_active', true)->get();

        foreach ($activeProviders as $provider) {
            ProviderProduct::updateOrCreate(
                ['provider_id' => $provider->id, 'product_id' => $product->id],
                [
                    'provider_sku_code' => $skuCode ?: Str::slug($product->name).'-'.$product->id,
                    'cost_price' => $costPrice,
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * DELETE /admin/products/{product}
     */
    public function destroy(Product $product)
    {
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
            'cost_price'   => 'required|numeric|min:0',
            'provider_sku_code' => 'nullable|string|max:100',
            'is_active'    => 'nullable|boolean',
            'auto_price'   => 'nullable|boolean',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
        ]);

        $validator->after(function ($validator) use ($request) {
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