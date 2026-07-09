<?php

namespace App\Providers;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\Provider;
use Illuminate\Support\Str;

class MockDigiflazzService implements ProviderInterface
{
    protected Provider $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function topup(Order $order, string $providerSkuCode): array
    {
        $refId = $order->invoice_number . '-' . Str::random(4);

        $payload = [
            'username'       => 'mock_user',
            'buyer_sku_code' => $providerSkuCode,
            'customer_no'    => $order->target_game_id,
            'ref_id'         => $refId,
            'sign'           => 'mock-signature-tidak-perlu-kredensial-asli',
        ];

        ApiLog::record([
            'order_id'    => $order->id,
            'provider_id' => $this->provider->id,
            'type'        => 'request',
            'payload'     => $payload,
        ]);

        usleep(random_int(200000, 800000));

        $shouldFail = str_ends_with($order->target_game_id, '000');

        if ($shouldFail) {
            $data = [
                'data' => [
                    'ref_id' => $refId,
                    'status' => 'Gagal',
                    'rc' => '99',
                    'message' => '[SIMULASI] Provider mock sengaja gagal karena target_game_id diakhiri "000"',
                ],
            ];
        } else {
            $data = [
                'data' => [
                    'ref_id' => $refId,
                    'status' => 'Sukses',
                    'rc' => '00',
                    'message' => '[SIMULASI] Top up berhasil (mock, bukan transaksi asli)',
                    'sn' => 'MOCK-' . strtoupper(Str::random(10)),
                    'price' => 0, 
                ],
            ];
        }

        ApiLog::record([
            'order_id'    => $order->id,
            'provider_id' => $this->provider->id,
            'type'        => 'response',
            'response'    => $data,
            'http_status' => 200,
        ]);

        return [
            'success'       => ! $shouldFail,
            'message'       => $data['data']['message'],
            'trx_id'        => $refId,
            'serial_number' => $data['data']['sn'] ?? null,
            'response_code' => $data['data']['rc'],
            'raw'           => $data,
        ];
    }

    public function checkStatus(string $providerTrxId): array
    {
        return [
            'status' => 'Sukses',
            'raw'    => ['data' => ['ref_id' => $providerTrxId, 'status' => 'Sukses']],
        ];
    }
}