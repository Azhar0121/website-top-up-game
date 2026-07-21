<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with(['product', 'provider', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate($request->input('per_page', 20));

        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function show(Order $order)
    {
        $order->load(['product', 'provider', 'user', 'logs', 'payment', 'apiLogs']);

        return response()->json(['success' => true, 'data' => $order]);
    }
    
    public function retry(Order $order, Request $request, OrderService $orderService)
    {
        $actorName = $request->user()->name ?? 'admin';

        try {
            $orderService->manualRetry($order, $actorName);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order sedang diproses ulang',
            'data' => $order->fresh(),
        ]);
    }

    public function forceSuccess(Order $order, Request $request, OrderService $orderService)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $actorName = $request->user()->name ?? 'admin';
        $orderService->forceSuccess($order, $actorName, $request->note);

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil diubah menjadi Success',
            'data' => $order->fresh(),
        ]);
    }

    public function resendCallback(Order $order, Request $request, OrderService $orderService)
    {
        $actorName = $request->user()->name ?? 'admin';
        $orderService->resendCallback($order, $actorName);

        return response()->json(['success' => true, 'message' => 'Notifikasi berhasil diulang']);
    }
}