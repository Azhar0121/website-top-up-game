<?php

namespace App\Services\PaymentGateways;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class MidtransService implements PaymentGatewayInterface
{
    protected PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    protected function baseUrl(): string
    {
        return $this->gateway->is_sandbox
            ? 'https://app.sandbox.midtrans.com/snap/v1'
            : 'https://app.midtrans.com/snap/v1';
    }

    /**
     * Server Key dipakai sebagai username Basic Auth, password dikosongkan.
     * Ini sesuai spesifikasi resmi Midtrans (bukan Bearer token).
     */
    protected function serverKey(): string
    {
        return $this->gateway->api_secret;
    }

    public function createTransaction(Order $order): array
    {
        $payload = [
            'transaction_details' => [
                // order_id dipakai Midtrans sebagai reference unik.
                // Kita pakai invoice_number kita sendiri supaya matching webhook gampang.
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

        ApiLog::record([
            'order_id' => $order->id,
            'type'     => 'request',
            'payload'  => $payload,
        ]);

        try {
            $response = Http::withBasicAuth($this->serverKey(), '')
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout(15)
                ->post($this->baseUrl() . '/transactions', $payload);

            $data = $response->json();

            ApiLog::record([
                'order_id'    => $order->id,
                'type'        => 'response',
                'response'    => $data,
                'http_status' => $response->status(),
            ]);

            return [
                'reference'    => $order->invoice_number,
                'redirect_url' => $data['redirect_url'] ?? null,
                'snap_token'   => $data['token'] ?? null,
                'raw'          => $data,
            ];
        } catch (\Throwable $e) {
            ApiLog::record([
                'order_id' => $order->id,
                'type'     => 'timeout',
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
        $orderId     = $payload['order_id'] ?? '';
        $statusCode  = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
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
}