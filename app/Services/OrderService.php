<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ProviderProduct;
use App\Notifications\OrderSuccessNotification;
use App\Providers\ProviderServiceFactory;
use App\Services\Notifications\WhatsAppNotificationService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function processAfterPayment(Order $order): bool
    {
        $canBeProcessed = DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->status !== Order::STATUS_PENDING_PAYMENT) {
                return false;
            }

            try {
                $this->decrementStock($lockedOrder);
            } catch (\RuntimeException $exception) {
                $lockedOrder->transitionTo(
                    Order::STATUS_FAILED,
                    'Pembayaran diterima, tetapi stok tidak tersedia. Perlu tindak lanjut refund oleh admin.'
                );

                return false;
            }

            $lockedOrder->update(['paid_at' => now()]);
            $lockedOrder->transitionTo(Order::STATUS_PAID, 'Pembayaran dikonfirmasi oleh payment gateway');

            return true;
        });

        if (! $canBeProcessed) {
            return false;
        }

        return true;
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

                $order->update(['completed_at' => now()]);
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
        if (! in_array($order->status, [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_FAILED], true)) {
            throw new \RuntimeException('Force Success hanya dapat dilakukan untuk order yang sudah dibayar atau gagal diproses provider.');
        }

        $wasAlreadySuccess = $order->status === Order::STATUS_SUCCESS;

        $order->transitionTo(Order::STATUS_SUCCESS, $note, $actorName);

        if (! $wasAlreadySuccess) {
            $order->update(['completed_at' => now()]);
            $this->sendSuccessNotification($order);
        }
    }

    /**
     * Kurangi stok terbatas segera setelah payment tervalidasi. Stok NULL berarti unlimited
     * sehingga tidak diubah. Timestamp pada order membuat proses webhook yang diulang tetap
     * idempoten dan mencegah stok terpotong dua kali.
     */
    protected function decrementStock(Order $order): void
    {
        if ($order->stock_deducted_at) {
            return;
        }

        $product = Product::query()->lockForUpdate()->find($order->product_id);

        if (! $product || is_null($product->stock)) {
            return;
        }

        if ($product->stock < $order->quantity) {
            throw new \RuntimeException('Stok produk tidak mencukupi.');
        }

        $product->decrement('stock', $order->quantity);
        $order->update(['stock_deducted_at' => now()]);
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