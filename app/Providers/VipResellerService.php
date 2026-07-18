<?php

namespace App\Providers;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\Provider;
use Illuminate\Support\Facades\Http;

class VipResellerService implements ProviderInterface
{
    protected Provider $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function topup(Order $order, string $providerSkuCode): array
    {
        $payload = [
            'key'    => $this->provider->api_key,
            'sign'   => md5($this->provider->api_key . $this->provider->api_secret),
            'type'   => 'order',
            'service' => $providerSkuCode,
            'data_no' => $order->target_game_id,
            'data_zone' => $order->target_server_id,
        ];

        ApiLog::record([
            'order_id'    => $order->id,
            'provider_id' => $this->provider->id,
            'type'        => 'request',
            'payload'     => $payload,
        ]);

        try {
            $response = Http::timeout(15)->post($this->provider->base_url . '/order', $payload);
            $data = $response->json();

            ApiLog::record([
                'order_id'    => $order->id,
                'provider_id' => $this->provider->id,
                'type'        => 'response',
                'response'    => $data,
                'http_status' => $response->status(),
            ]);

            $result = $data['result'] ?? false;

            return [
                'success' => (bool) $result,
                'message' => $data['message'] ?? 'Tidak ada pesan dari provider',
                'trx_id'  => $data['data']['trxid'] ?? null,
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
                'message' => 'Provider timeout: ' . $e->getMessage(),
                'trx_id'  => null,
                'raw'     => null,
            ];
        }
    }

    public function checkStatus(string $providerTrxId): array
    {
        $response = Http::timeout(15)->post($this->provider->base_url . '/status', [
            'key'   => $this->provider->api_key,
            'trxid' => $providerTrxId,
        ]);

        return ['status' => $response->json()['status'] ?? 'Unknown', 'raw' => $response->json()];
    }
}
