<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Provider;
use App\Models\ProviderProduct;
use App\Notifications\OrderSuccessNotification;
use App\Providers\ProviderServiceFactory;
use App\Services\Notifications\WhatsAppNotificationService;
use Illuminate\Support\Facades\Notification;

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

                $this->decrementStock($order);
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
        $wasAlreadySuccess = $order->status === Order::STATUS_SUCCESS;

        $order->transitionTo(Order::STATUS_SUCCESS, $note, $actorName);

        // Cuma kurangi stok kalau order ini BELUM pernah berstatus success sebelumnya -
        // mencegah stok berkurang dobel kalau admin klik "Force Success" pada order yang
        // ternyata sudah success (misal klik dobel / redundant action).
        if (! $wasAlreadySuccess) {
            $this->decrementStock($order);
        }
    }

    /**
     * Kurangi stok produk setelah order berhasil (PRD tidak eksplisit bahas stok top up,
     * tapi kolom `products.stock` sudah ada di database - kalau nilainya NULL berarti
     * produk itu dianggap unlimited/tanpa batas stok (wajar untuk barang digital on-demand
     * lewat provider), jadi sengaja tidak disentuh. Kalau nilainya angka, berarti admin
     * sengaja membatasi stok (misal untuk flash sale/limited item), baru kita kurangi.
     */
    protected function decrementStock(Order $order): void
    {
        $product = $order->product;

        if (! $product || is_null($product->stock)) {
            return;
        }

        $product->decrement('stock', $order->quantity);

        // Jaga-jaga supaya stok tidak pernah minus di database walau ada race condition.
        if ($product->fresh()->stock < 0) {
            $product->update(['stock' => 0]);
        }
    }

    public function resendCallback(Order $order, string $actorName): void
    {
        $order->logs()->create([
            'status' => $order->status,
            'note'   => "Notifikasi diulang secara manual oleh admin: {$actorName}",
            'actor'  => $actorName,
        ]);

        $this->sendSuccessNotification($order);
    }

    protected function sendSuccessNotification(Order $order): void
    {
        if ($order->customer_email) {
            Notification::route('mail', $order->customer_email)
                ->notify(new OrderSuccessNotification($order));
        }

        app(WhatsAppNotificationService::class)->sendOrderSuccess($order);
    }
}