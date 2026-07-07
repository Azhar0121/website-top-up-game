<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTopUpOrder;
use App\Models\ApiLog;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function callback(Request $request, OrderService $orderService)
    {
        $payload = $request->all();

        ApiLog::record([
            'type'    => 'webhook',
            'payload' => $payload,
            'headers' => $request->headers->all(),
        ]);

        $referenceNumber = $payload['reference'] ?? $payload['order_id'] ?? null;
        $status = $payload['status'] ?? null; // sesuaikan mapping status per gateway

        if (! $referenceNumber) {
            return response()->json(['success' => false, 'message' => 'Reference number tidak ditemukan'], 400);
        }

        $payment = Payment::where('reference_number', $referenceNumber)->first();

        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Payment record tidak ditemukan'], 404);
        }

        $payment->update([
            'status'       => strtolower($status) === 'paid' ? 'paid' : 'failed',
            'raw_callback' => json_encode($payload),
            'paid_at'      => now(),
        ]);

        $order = $payment->order;

        if (strtolower($status) === 'paid' && $order->status === Order::STATUS_PENDING_PAYMENT) {
            $order->transitionTo(Order::STATUS_PAID, 'Konfirmasi pembayaran diterima via webhook');

            ProcessTopUpOrder::dispatch($order);
        }

        return response()->json(['success' => true]);
    }
}