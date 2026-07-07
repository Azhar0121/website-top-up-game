<?php

namespace App\Services\PaymentGateways;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class TripayService implements PaymentGatewayInterface
{
    protected PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    protected function baseUrl(): string
    {
        return $this->gateway->is_sandbox
            ? 'https://tripay.co.id/api-sandbox'
            : 'https://tripay.co.id/api';
    }

    public function createTransaction(Order $order): array
    {
        $merchantRef = $order->invoice_number;
        $amount = (int) $order->price;

        $signature = hash_hmac(
            'sha256',
            $this->gateway->merchant_code . $merchantRef . $amount,
            $this->gateway->api_secret
        );

        $payload = [
            'method'        => 'QRIS',
            'merchant_ref'  => $merchantRef,
            'amount'        => $amount,
            'customer_name' => $order->customer_email ?? 'Customer',
            'customer_email' => $order->customer_email,
            'order_items'   => [[
                'sku'      => (string) $order->product_id,
                'name'     => $order->product->name ?? 'Top Up Produk',
                'price'    => $amount,
                'quantity' => $order->quantity,
            ]],
            'signature' => $signature,
        ];

        ApiLog::record(['order_id' => $order->id, 'type' => 'request', 'payload' => $payload]);

        try {
            $response = Http::withToken($this->gateway->api_key)
                ->timeout(15)
                ->post($this->baseUrl() . '/transaction/create', $payload);

            $data = $response->json();

            ApiLog::record([
                'order_id' => $order->id,
                'type' => 'response',
                'response' => $data,
                'http_status' => $response->status(),
            ]);

            return [
                'reference'    => $data['data']['reference'] ?? $merchantRef,
                'redirect_url' => $data['data']['checkout_url'] ?? null,
                'snap_token'   => null, // Tripay tidak pakai konsep snap token seperti Midtrans
                'raw'          => $data,
            ];
        } catch (\Throwable $e) {
            ApiLog::record(['order_id' => $order->id, 'type' => 'timeout', 'response' => ['error' => $e->getMessage()]]);

            return ['reference' => $merchantRef, 'redirect_url' => null, 'snap_token' => null, 'raw' => ['error' => $e->getMessage()]];
        }
    }

    public function verifySignature(array $payload): bool
    {
        $callbackSignature = $payload['signature'] ?? '';

        $expected = hash_hmac(
            'sha256',
            ($payload['merchant_ref'] ?? '') . ($payload['status'] ?? ''),
            $this->gateway->api_secret
        );

        return hash_equals($expected, $callbackSignature);
    }

    public function extractReference(array $payload): ?string
    {
        return $payload['merchant_ref'] ?? null;
    }

    public function mapStatus(array $payload): string
    {
        $status = strtoupper($payload['status'] ?? '');

        return match ($status) {
            'PAID' => 'paid',
            'EXPIRED' => 'expired',
            'FAILED' => 'failed',
            default => 'pending',
        };
    }
}