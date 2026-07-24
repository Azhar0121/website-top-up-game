<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\OrderService;
use App\Services\PaymentGateways\PaymentGatewayServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function paymentMethods(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gateway_code' => 'required|string|exists:payment_gateways,code',
            'amount'       => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $gateway = PaymentGateway::where('code', $request->gateway_code)->where('is_active', true)->firstOrFail();
        $service = PaymentGatewayServiceFactory::make($gateway);

        return response()->json([
            'success' => true,
            'data' => $service->getAvailablePaymentMethods((int) $request->amount),
        ]);
    }

    public function initiate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_number' => 'required|string|exists:orders,invoice_number',
            'gateway_code'   => 'required|string|exists:payment_gateways,code',
            'payment_method' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $order = Order::where('invoice_number', $request->invoice_number)->firstOrFail();

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return response()->json([
                'success' => false,
                'message' => 'Order ini sudah diproses sebelumnya, tidak bisa membuat transaksi baru.',
            ], 400);
        }

        $gateway = PaymentGateway::where('code', $request->gateway_code)
            ->where('is_active', true)
            ->firstOrFail();

        $service = PaymentGatewayServiceFactory::make($gateway);
        $result = $service->createTransaction($order, $request->payment_method);

        $payment = Payment::create([
            'order_id'            => $order->id,
            'payment_gateway_id'  => $gateway->id,
            'method'              => $request->payment_method ?? ($gateway->code === 'midtrans' ? 'SNAP' : 'DEFAULT'),
            'reference_number'    => $result['reference'],
            'amount'              => $order->price,
            'status'              => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'payment_id'   => $payment->id,
                'snap_token'   => $result['snap_token'],   
                'redirect_url' => $result['redirect_url'], 
            ],
        ]);
    }

    public function callback(Request $request, string $gatewayCode, OrderService $orderService)
    {
        $payload = $request->all();

        // Simpan dulu log webhook mentahnya SEBELUM tahu ini punya order mana -
        // supaya webhook yang aneh/tidak valid pun tetap tercatat buat investigasi.
        // $webhookLog di-update belakangan begitu order-nya ketemu (lihat bawah),
        // supaya di halaman admin baris ini bisa dihubungkan ke invoice yang tepat.
        $webhookLog = ApiLog::record([
            'type'    => 'webhook',
            'payload' => $payload,
            'headers' => $request->headers->all(),
        ]);

        $gateway = PaymentGateway::where('code', $gatewayCode)->where('is_active', true)->first();

        if (! $gateway) {
            return response()->json(['success' => false, 'message' => 'Gateway tidak dikenal'], 404);
        }

        $service = PaymentGatewayServiceFactory::make($gateway);

        if (! $service->verifySignature($payload)) {
            ApiLog::record(['type' => 'error', 'payload' => $payload, 'response' => ['reason' => 'invalid signature']]);

            return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
        }

        $referenceNumber = $service->extractReference($payload);
        $mappedStatus = $service->mapStatus($payload); // 'paid' | 'pending' | 'failed' | 'expired'

        if (! $referenceNumber) {
            return response()->json(['success' => false, 'message' => 'Reference tidak ditemukan di payload'], 400);
        }

        $payment = Payment::where('reference_number', $referenceNumber)
            ->where('payment_gateway_id', $gateway->id)
            ->first();

        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Payment record tidak ditemukan'], 404);
        }

        $alreadyPaid = $payment->status === 'paid';

        $payment->update([
            'status'       => $mappedStatus,
            'raw_callback' => json_encode($payload),
            'paid_at'      => $mappedStatus === 'paid' ? now() : $payment->paid_at,
        ]);

        $order = $payment->order;

        $webhookLog->update(['order_id' => $order->id]);

        if ($mappedStatus === 'paid' && ! $alreadyPaid && $order->status === Order::STATUS_PENDING_PAYMENT) {
            if ($orderService->processAfterPayment($order)) {
                \App\Jobs\ProcessTopUpOrder::dispatch($order->fresh());
            }
        } elseif (in_array($mappedStatus, ['failed', 'expired']) && $order->status === Order::STATUS_PENDING_PAYMENT) {
            $order->transitionTo(
                $mappedStatus === 'expired' ? Order::STATUS_EXPIRED : Order::STATUS_CANCELLED,
                "Pembayaran {$mappedStatus} menurut {$gateway->name}"
            );
        }

        return response()->json(['success' => true]);
    }
}