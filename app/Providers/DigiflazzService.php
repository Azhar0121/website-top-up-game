<?php

namespace App\Services\Providers;

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

    public function topup(Order $order, string $providerSkuCode): array
    {
        $username = $this->provider->api_key;   
        $apiKey   = $this->provider->api_secret; 
        $refId    = $order->invoice_number . '-' . Str::random(4);

        $payload = [
            'username'  => $username,
            'buyer_sku_code' => $providerSkuCode,
            'customer_no'    => $order->target_game_id . ($order->target_server_id ? $order->target_server_id : ''),
            'ref_id'    => $refId,
            'sign'      => md5($username . $apiKey . $refId),
        ];

        ApiLog::record([
            'order_id'    => $order->id,
            'provider_id' => $this->provider->id,
            'type'        => 'request',
            'payload'     => $payload,
        ]);

        try {
            $response = Http::timeout(15)->post($this->provider->base_url . '/v1/transaction', $payload);
            $data = $response->json();

            ApiLog::record([
                'order_id'    => $order->id,
                'provider_id' => $this->provider->id,
                'type'        => 'response',
                'response'    => $data,
                'http_status' => $response->status(),
            ]);

            $status = $data['data']['status'] ?? 'Gagal';

            return [
                'success' => in_array($status, ['Sukses', 'Pending']),
                'message' => $data['data']['message'] ?? 'Tidak ada pesan dari provider',
                'trx_id'  => $data['data']['ref_id'] ?? $refId,
                'raw'     => $data,
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
        $username = $this->provider->api_key;
        $apiKey   = $this->provider->api_secret;

        $payload = [
            'username' => $username,
            'ref_id'   => $providerTrxId,
            'sign'     => md5($username . $apiKey . 'status'),
        ];

        $response = Http::timeout(15)->post($this->provider->base_url . '/v1/transaction', $payload);
        $data = $response->json();

        return [
            'status'  => $data['data']['status'] ?? 'Unknown',
            'raw'     => $data,
        ];
    }
}