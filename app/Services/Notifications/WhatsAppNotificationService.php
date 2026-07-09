<?php

namespace App\Services\Notifications;

use App\Models\ApiLog;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class WhatsAppNotificationService
{
    protected string $apiToken;
    protected string $baseUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->apiToken = config('whatsapp.token', '');
    }

    public function sendOrderSuccess(Order $order): bool
    {
        if (! $order->customer_whatsapp || ! $this->apiToken) {
            return false;
        }

        $message = "Top Up Berhasil! 🎉\n\n"
            . "Invoice: {$order->invoice_number}\n"
            . "Produk: " . ($order->product->name ?? '-') . "\n"
            . "ID Tujuan: {$order->target_game_id}\n"
            . "Total: Rp " . number_format($order->price, 0, ',', '.') . "\n\n"
            . "Terima kasih sudah top up di tempat kami!";

        try {
            $response = Http::withHeaders(['Authorization' => $this->apiToken])
                ->timeout(10)
                ->post($this->baseUrl, [
                    'target'  => $order->customer_whatsapp,
                    'message' => $message,
                ]);

            ApiLog::record([
                'order_id'    => $order->id,
                'type'        => 'response',
                'response'    => $response->json(),
                'http_status' => $response->status(),
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            ApiLog::record([
                'order_id' => $order->id,
                'type'     => 'error',
                'response' => ['error' => 'Gagal kirim WA: ' . $e->getMessage()],
            ]);

            return false;
        }
    }
}