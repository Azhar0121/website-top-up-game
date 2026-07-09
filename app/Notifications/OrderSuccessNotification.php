<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderSuccessNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Top Up Berhasil - {$this->order->invoice_number}")
            ->greeting('Top Up Kamu Berhasil! 🎉')
            ->line("Invoice: {$this->order->invoice_number}")
            ->line("Produk: {$this->order->product->name}")
            ->line("ID Tujuan: {$this->order->target_game_id}")
            ->line("Total: Rp " . number_format($this->order->price, 0, ',', '.'))
            ->line('Terima kasih sudah top up di tempat kami!');
    }
}
