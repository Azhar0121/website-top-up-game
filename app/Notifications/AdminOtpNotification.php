<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminOtpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kode Verifikasi Login Dashboard Admin - TopUp Kilat')
            ->greeting('Kode Verifikasi Kamu')
            ->line('Ada percobaan login ke Dashboard Admin TopUp Kilat memakai akunmu.')
            ->line("Kode verifikasi: **{$this->code}**")
            ->line('Kode ini berlaku selama 5 menit dan hanya bisa dipakai sekali.')
            ->line('Kalau kamu tidak merasa mencoba login, abaikan email ini dan segera ganti password.');
    }
}
