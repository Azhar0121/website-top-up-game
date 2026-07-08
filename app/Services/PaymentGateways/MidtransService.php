<?php

namespace App\Services\PaymentGateways;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\PaymentGateway;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;

class MidtransService implements PaymentGatewayInterface
{
    protected PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
        $this->configureSdk();
    }

    protected function configureSdk(): void
    {
        MidtransConfig::$serverKey = $this->serverKey();
        MidtransConfig::$clientKey = $this->clientKey();
        MidtransConfig::$isProduction = ! $this->gateway->is_sandbox;
        MidtransConfig::$isSanitized = config('midtrans.is_sanitized', true);
        MidtransConfig::$is3ds = config('midtrans.is_3ds', true);
    }

    protected function serverKey(): string
    {
        return $this->gateway->api_secret ?: config('midtrans.server_key');
    }

    protected function clientKey(): string
    {
        return $this->gateway->api_key ?: config('midtrans.client_key');
    }

    public function createTransaction(Order $order, ?string $paymentMethodCode = null): array
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $order->invoice_number,
                'gross_amount' => (int) $order->price,
            ],
            'customer_details' => [
                'email' => $order->customer_email,
                'phone' => $order->customer_whatsapp,
            ],
            'item_details' => [[
                'id'       => (string) $order->product_id,
                'price'    => (int) $order->price,
                'quantity' => $order->quantity,
                'name'     => $order->product->name ?? 'Top Up Produk',
            ]],
            'enabled_payments' => [
                'gopay', 'qris', 'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va',
            ],
        ];

        ApiLog::record(['order_id' => $order->id, 'type' => 'request', 'payload' => $params]);

        try {
            $snapToken = Snap::getSnapToken($params);

            ApiLog::record([
                'order_id' => $order->id,
                'type'     => 'response',
                'response' => ['snap_token' => $snapToken],
            ]);

            return [
                'reference'    => $order->invoice_number,
                'redirect_url' => null, 
                'snap_token'   => $snapToken,
                'raw'          => ['snap_token' => $snapToken],
            ];
        } catch (\Throwable $e) {
            ApiLog::record([
                'order_id' => $order->id,
                'type'     => 'error',
                'response' => ['error' => $e->getMessage()],
            ]);

            return [
                'reference'    => $order->invoice_number,
                'redirect_url' => null,
                'snap_token'   => null,
                'raw'          => ['error' => $e->getMessage()],
            ];
        }
    }

    public function verifySignature(array $payload): bool
    {
        $orderId      = $payload['order_id'] ?? '';
        $statusCode   = $payload['status_code'] ?? '';
        $grossAmount  = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey());

        return hash_equals($expected, $signatureKey);
    }

    public function extractReference(array $payload): ?string
    {
        return $payload['order_id'] ?? null;
    }

    public function mapStatus(array $payload): string
    {
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        return match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
            $transactionStatus === 'settlement' => 'paid',
            $transactionStatus === 'pending' => 'pending',
            in_array($transactionStatus, ['deny', 'cancel']) => 'failed',
            $transactionStatus === 'expire' => 'expired',
            default => 'pending',
        };
    }

    public function getAvailablePaymentMethods(int $amount): array
    {
        return [];
    }
}