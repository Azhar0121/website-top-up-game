<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request)
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

        $role = Auth::check() ? (Auth::user()->role ?? 'customer') : 'customer';
        $quantity = $request->input('quantity', 1);
        $price = $product->priceForRole($role) * $quantity;

        $order = Order::create([
            'user_id'           => Auth::id(),
            'product_id'        => $product->id,
            'target_game_id'    => $request->target_game_id,
            'target_server_id'  => $request->target_server_id,
            'customer_email'    => $request->customer_email,
            'customer_whatsapp' => $request->customer_whatsapp,
            'quantity'          => $quantity,
            'price'             => $price,
            'voucher_code'      => $request->voucher_code,
            'status'            => Order::STATUS_PENDING_PAYMENT,
        ]);

        $order->logs()->create([
            'status' => Order::STATUS_PENDING_PAYMENT,
            'note'   => 'Order dibuat, menunggu pembayaran',
            'actor'  => 'system',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat, silakan lanjut ke pembayaran',
            'data' => [
                'invoice_number' => $order->invoice_number,
                'price'          => $order->price,
                'status'         => $order->status,
            ],
        ], 201);
    }

    public function show(string $invoice)
    {
        $order = Order::with(['product', 'provider', 'logs'])
            ->where('invoice_number', $invoice)
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $order]);
    }
}