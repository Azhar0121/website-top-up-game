<?php

namespace App\Services\PaymentGateways;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class DuitkuService implements PaymentGatewayInterface
{
    protected PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    protected function baseUrl(): string
    {
        return $this->gateway->is_sandbox
            ? 'https://sandbox.duitku.com/webapi/api/merchant'
            : 'https://passport.duitku.com/webapi/api/merchant';
    }

    protected function merchantCode(): string
    {
        return $this->gateway->merchant_code;
    }

    protected function apiKey(): string
    {
        return $this->gateway->api_secret;
    }

    public function createTransaction(Order $order, ?string $paymentMethodCode = null): array
    {
        $merchantOrderId = $order->invoice_number;
        $paymentAmount   = (int) $order->price;

        $paymentMethodCode = $paymentMethodCode ?? 'VA';

        $signature = hash_hmac(
            'sha256',
            $this->merchantCode() . $merchantOrderId . $paymentAmount,
            $this->apiKey()
        );

        $payload = [
            'merchantCode'    => $this->merchantCode(),
            'paymentAmount'   => $paymentAmount,
            'paymentMethod'   => $paymentMethodCode,
            'merchantOrderId' => $merchantOrderId,
            'productDetails'  => $order->product->name ?? 'Top Up Produk',
            'email'           => $order->customer_email ?: 'guest@topupgame.test',
            'phoneNumber'     => $order->customer_whatsapp,
            'customerVaName'  => $order->customer_email ?: 'Customer',
            'itemDetails' => [[
                'name'     => $order->product->name ?? 'Top Up Produk',
                'price'    => $paymentAmount,
                'quantity' => 1, 
            ]],
            
            'callbackUrl' => config('app.url') . '/api/v1/webhook/payment/duitku',
            'returnUrl'   => config('app.url') . '/order/' . $merchantOrderId,
            'signature'   => $signature,
            'expiryPeriod' => 60, // menit
        ];

        ApiLog::record(['order_id' => $order->id, 'type' => 'request', 'payload' => $payload]);

        try {
            $response = Http::timeout(15)->post($this->baseUrl() . '/v2/inquiry', $payload);
            $data = $response->json();

            ApiLog::record([
                'order_id'    => $order->id,
                'type'        => 'response',
                'response'    => $data,
                'http_status' => $response->status(),
            ]);

            if (($data['statusCode'] ?? null) !== '00') {
                return [
                    'reference'    => $merchantOrderId,
                    'redirect_url' => null,
                    'snap_token'   => null,
                    'raw'          => $data,
                ];
            }

            return [
                'reference'    => $merchantOrderId,
                'redirect_url' => $data['paymentUrl'] ?? null,
                'snap_token'   => null, 
                'raw'          => $data, 
            ];
        } catch (\Throwable $e) {
            ApiLog::record(['order_id' => $order->id, 'type' => 'timeout', 'response' => ['error' => $e->getMessage()]]);

            return [
                'reference'    => $merchantOrderId,
                'redirect_url' => null,
                'snap_token'   => null,
                'raw'          => ['error' => $e->getMessage()],
            ];
        }
    }

    public function verifySignature(array $payload): bool
    {
        $merchantCode    = $payload['merchantCode'] ?? '';
        $amount          = $payload['amount'] ?? '';
        $merchantOrderId = $payload['merchantOrderId'] ?? '';
        $signature       = $payload['signature'] ?? '';

        $expected = hash_hmac('sha256', $merchantCode . $amount . $merchantOrderId, $this->apiKey());

        return hash_equals($expected, $signature);
    }

    public function extractReference(array $payload): ?string
    {
        return $payload['merchantOrderId'] ?? null;
    }

    public function mapStatus(array $payload): string
    {
        $resultCode = $payload['resultCode'] ?? null;

        return match ($resultCode) {
            '00' => 'paid',
            '01' => 'failed',
            default => 'pending',
        };
    }

    public function getAvailablePaymentMethods(int $amount): array
    {
        $datetime = now()->format('Y-m-d H:i:s');
        $signature = hash_hmac('sha256', $this->merchantCode() . $amount . $datetime, $this->apiKey());

        try {
            $response = Http::timeout(15)->post($this->baseUrl() . '/paymentmethod/getpaymentmethod', [
                'merchantcode' => $this->merchantCode(),
                'amount'       => $amount,
                'datetime'     => $datetime,
                'signature'    => $signature,
            ]);

            $data = $response->json();

            return collect($data['paymentFee'] ?? [])->map(fn ($item) => [
                'code' => $item['paymentMethod'],
                'name' => $item['paymentName'],
                'fee'  => $item['totalFee'],
                'image' => $item['paymentImage'] ?? null,
            ])->all();
        } catch (\Throwable $e) {
            ApiLog::record(['type' => 'error', 'response' => ['error' => $e->getMessage()]]);

            return [];
        }
    }
}