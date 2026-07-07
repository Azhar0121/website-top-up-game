<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Provider;
use App\Models\ProviderProduct;
use App\Providers\ProviderServiceFactory;

class OrderService
{
    public function processAfterPayment(Order $order): void
    {
        $order->transitionTo(Order::STATUS_PAID, 'Pembayaran dikonfirmasi oleh payment gateway');

        $this->dispatchToProvider($order);
    }

    public function dispatchToProvider(Order $order): void
    {
        $order->transitionTo(Order::STATUS_PROCESSING, 'Mulai proses ke provider');

        $providerProducts = $order->product
            ->activeProviderProducts()
            ->get();

        if ($providerProducts->isEmpty()) {
            $order->transitionTo(
                Order::STATUS_FAILED,
                'Tidak ada provider aktif yang men-support produk ini. Masuk Retry Queue.'
            );
            return;
        }

        foreach ($providerProducts as $providerProduct) {
            /** @var ProviderProduct $providerProduct */
            $provider = $providerProduct->provider;

            $result = $this->attemptProvider($order, $provider, $providerProduct->provider_sku_code);

            if ($result['success']) {
                $order->update([
                    'provider_id' => $provider->id,
                    'cost_price'  => $providerProduct->cost_price,
                ]);

                $order->transitionTo(
                    Order::STATUS_SUCCESS,
                    "Berhasil diproses oleh provider {$provider->name}"
                );

                $this->sendSuccessNotification($order);
                return; 
            }

            $order->logs()->create([
                'status' => Order::STATUS_PROCESSING,
                'note'   => "Provider {$provider->name} gagal/timeout: {$result['message']}. Mencoba provider backup berikutnya.",
                'actor'  => 'system',
            ]);
        }

        $order->transitionTo(
            Order::STATUS_FAILED,
            'Semua provider gagal memproses order ini. Order masuk Retry Queue untuk dieksekusi manual.'
        );
    }

    public function attemptProvider(Order $order, Provider $provider, string $providerSkuCode): array
    {
        $service = ProviderServiceFactory::make($provider);

        return $service->topup($order, $providerSkuCode);
    }

    public function manualRetry(Order $order, string $actorName): void
    {
        if ($order->status !== Order::STATUS_FAILED) {
            throw new \RuntimeException('Hanya order dengan status Failed yang bisa di-retry.');
        }

        $order->logs()->create([
            'status' => Order::STATUS_PROCESSING,
            'note'   => "Retry manual dipicu oleh admin: {$actorName}",
            'actor'  => $actorName,
        ]);

        $this->dispatchToProvider($order);
    }

    public function forceSuccess(Order $order, string $actorName, string $note): void
    {
        $order->transitionTo(Order::STATUS_SUCCESS, $note, $actorName);
    }

    protected function sendSuccessNotification(Order $order): void
    {

    }
}