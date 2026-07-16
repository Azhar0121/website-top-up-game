<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\PriceSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Atur margin untuk satu produk (PRD 4.3: Margin dinamis Fixed/Persentase).
     * PATCH /api/v1/admin/products/{product}/margin
     * Body: { "margin_type": "percentage", "margin_value": 15, "auto_price": true }
     */
    public function updateMargin(Product $product, Request $request, PriceSyncService $priceSyncService)
    {
        $validator = Validator::make($request->all(), [
            'margin_type' => 'required|in:fixed,percentage',
            'margin_value' => 'required|numeric|min:0',
            'auto_price' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $product->update([
            'margin_type' => $request->margin_type,
            'margin_value' => $request->margin_value,
            'auto_price' => $request->boolean('auto_price', true),
        ]);

        // Langsung hitung ulang harga begitu margin diubah, supaya admin
        // langsung lihat efeknya tanpa perlu trigger sync terpisah.
        $result = $priceSyncService->sync($product->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Margin berhasil diupdate',
            'data' => $result,
        ]);
    }

    /**
     * Trigger sinkronisasi harga semua produk sekaligus (dipanggil manual dari
     * dashboard admin, atau nanti dijadwalkan otomatis lewat scheduler).
     * POST /api/v1/admin/products/sync-prices
     */
    public function syncPrices(PriceSyncService $priceSyncService)
    {
        $results = $priceSyncService->syncAll();

        return response()->json([
            'success' => true,
            'message' => "{$results->count()} produk disinkronkan, " . $results->where('changed', true)->count() . ' mengalami perubahan harga.',
            'data' => $results,
        ]);
    }
}
