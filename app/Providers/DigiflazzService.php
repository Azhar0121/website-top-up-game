<?php

namespace App\Providers;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\Provider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DigiflazzService implements ProviderInterface
{
    protected Provider $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    protected function username(): string
    {
        return $this->provider->api_key;
    }

    protected function apiKey(): string
    {
        return $this->provider->api_secret;
    }

    protected function baseUrl(): string
    {
        return rtrim($this->provider->base_url, '/');
    }

    public function topup(Order $order, string $providerSkuCode): array
    {
        $refId = $order->invoice_number . '-' . Str::random(4);

        $payload = [
            'username'       => $this->username(),
            'buyer_sku_code' => $providerSkuCode,
            'customer_no'    => $order->target_game_id . ($order->target_server_id ?: ''),
            'ref_id'         => $refId,
            'sign'           => md5($this->username() . $this->apiKey() . $refId),
        ];

        ApiLog::record([
            'order_id'    => $order->id,
            'provider_id' => $this->provider->id,
            'type'        => 'request',
            'payload'     => $payload,
        ]);

        try {
            $response = Http::timeout(15)->post($this->baseUrl() . '/v1/transaction', $payload);
            $data = $response->json();

            ApiLog::record([
                'order_id'    => $order->id,
                'provider_id' => $this->provider->id,
                'type'        => 'response',
                'response'    => $data,
                'http_status' => $response->status(),
            ]);

            $status = $data['data']['status'] ?? 'Gagal';
            $rc = $data['data']['rc'] ?? null;

            return [
                'success' => in_array($status, ['Sukses', 'Pending']),
                'message' => $data['data']['message'] ?? 'Tidak ada pesan dari provider',
                'trx_id'  => $data['data']['ref_id'] ?? $refId,
                'serial_number' => $data['data']['sn'] ?? null,
                'response_code' => $rc,
                'raw' => $data,
            ];
        } catch (\Throwable $e) {
            ApiLog::record([
                'order_id'    => $order->id,
                'provider_id' => $this->provider->id,
                'type'        => 'timeout',
                'response'    => ['error' => $e->getMessage()],
            ]);

            return [
                'success' => false,
                'message' => 'Provider timeout atau tidak dapat dihubungi: ' . $e->getMessage(),
                'trx_id'  => null,
                'raw'     => null,
            ];
        }
    }

    public function checkStatus(string $providerTrxId): array
    {
        $payload = [
            'username'  => $this->username(),
            'ref_id'    => $providerTrxId,
            'sign' => md5($this->username() . $this->apiKey() . $providerTrxId),
        ];

        $response = Http::timeout(15)->post($this->baseUrl() . '/v1/transaction', $payload);
        $data = $response->json();

        return [
            'status' => $data['data']['status'] ?? 'Unknown',
            'raw'    => $data,
        ];
    }

    public function checkBalance(): array
    {
        $payload = [
            'cmd'      => 'deposit',
            'username' => $this->username(),
            'sign'     => md5($this->username() . $this->apiKey() . 'depo'),
        ];

        $response = Http::timeout(15)->post($this->baseUrl() . '/v1/cek-saldo', $payload);
        $data = $response->json();

        return [
            'success' => isset($data['data']['deposit']),
            'deposit' => $data['data']['deposit'] ?? null,
            'raw'     => $data,
        ];
    }

    public function getPriceList(?string $skuCode = null): array
    {
        $payload = [
            'cmd'      => 'prepaid',
            'username' => $this->username(),
            'sign'     => md5($this->username() . $this->apiKey() . 'pricelist'),
        ];

        if ($skuCode) {
            $payload['code'] = $skuCode;
        }

        $response = Http::timeout(15)->post($this->baseUrl() . '/v1/price-list', $payload);
        $data = $response->json();

        return [
            'success' => isset($data['data']),
            'products' => $data['data'] ?? [],
            'raw' => $data,
        ];
    }
}