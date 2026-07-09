<?php

// Kredensial WA Gateway (default: Fonnte - https://fonnte.com).
// Daftar akun, buat device, lalu ambil token dari dashboard Fonnte.
// Kalau nanti ganti provider WA lain, tinggal ubah isi
// app/Services/Notifications/WhatsAppNotificationService.php - tidak perlu ubah pemanggilnya.

return [
    'token' => env('FONNTE_TOKEN', ''),
];
