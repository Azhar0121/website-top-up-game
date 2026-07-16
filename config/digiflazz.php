<?php

// Webhook Secret didapat dari dashboard Digiflazz -> Atur Koneksi API -> tab Webhook,
// setelah kamu daftarkan URL webhook-nya. Dipakai untuk validasi signature (HMAC SHA1)
// supaya webhook tidak bisa dipalsukan pihak lain.

return [
    'webhook_secret' => env('DIGIFLAZZ_WEBHOOK_SECRET', ''),
];