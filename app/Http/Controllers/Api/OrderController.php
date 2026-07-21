<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request, VoucherService $voucherService)
    {
        $validator = Validator::make($request->all(), [
            'product_id'        => 'required|exists:products,id',
            'target_game_id'    => 'required|string|max:100',
            'target_server_id'  => 'nullable|string|max:100',
            'customer_email'    => 'nullable|email',
            'customer_whatsapp' => 'nullable|string|max:20',
            'quantity'          => 'nullable|integer|min:1',
            'voucher_code'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $product = Product::findOrFail($request->product_id);

        if (! $product->is_active) {
            return response()->json(['success' => false, 'message' => 'Produk tidak tersedia'], 400);
        }

        $quantity = $request->input('quantity', 1);

        if (! is_null($product->stock) && $quantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => $product->stock > 0
                    ? "Stok tidak cukup. Sisa stok: {$product->stock}."
                    : 'Stok produk ini sedang habis.',
            ], 400);
        }

        $role = Auth::check() ? (Auth::user()->role ?? 'customer') : 'customer';
        $subtotal = $product->priceForRole($role) * $quantity;
        $discountAmount = 0;
        $voucherCode = $request->filled('voucher_code') ? strtoupper($request->voucher_code) : null;
        $voucher = null;

        if ($voucherCode) {
            $voucherResult = $voucherService->validate($voucherCode, $subtotal);

            if (! $voucherResult['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $voucherResult['message'],
                ], 422);
            }

            $discountAmount = $voucherResult['discount_amount'];
            $voucher = $voucherResult['voucher'];
        }

        $finalPrice = max($subtotal - $discountAmount, 0);

        $order = DB::transaction(function () use (
            $product,
            $quantity,
            $discountAmount,
            $finalPrice,
            $voucherCode,
            $voucher,
            $voucherService,
            $request
        ) {
            $order = Order::create([
                'user_id'           => Auth::id(),
                'product_id'        => $product->id,
                'target_game_id'    => $request->target_game_id,
                'target_server_id'  => $request->target_server_id,
                'customer_email'    => $request->customer_email,
                'customer_whatsapp' => $request->customer_whatsapp,
                'quantity'          => $quantity,
                'price'             => $finalPrice,
                'voucher_code'      => $voucherCode,
                'discount_amount'   => $discountAmount,
                'status'            => Order::STATUS_PENDING_PAYMENT,
            ]);

            $order->logs()->create([
                'status' => Order::STATUS_PENDING_PAYMENT,
                'note'   => 'Order dibuat, menunggu pembayaran',
                'actor'  => 'system',
            ]);

            if ($voucher) {
                $voucherService->markAsUsed($voucher);
            }

            return $order;
        });

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat, silakan lanjut ke pembayaran',
            'data' => [
                'invoice_number' => $order->invoice_number,
                'subtotal'       => number_format($subtotal, 2, '.', ''),
                'discount_amount' => $order->discount_amount,
                'voucher_code'   => $order->voucher_code,
                'price'          => $order->price,
                'status'         => $order->status,
            ],
        ], 201);
    }

    public function show(string $invoice)
    {
        $order = Order::with(['product.game', 'provider', 'logs'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $order]);
    }
}