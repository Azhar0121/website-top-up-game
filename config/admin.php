<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist untuk Dashboard Admin
    |--------------------------------------------------------------------------
    |
    | Daftar IP yang boleh mengakses /admin/*, dipisah koma di .env, contoh:
    | ADMIN_ALLOWED_IPS=127.0.0.1,182.1.2.3
    |
    | Kalau env ini KOSONG (default), whitelist otomatis NONAKTIF dan semua IP
    | boleh akses - supaya tidak bikin kamu sendiri ke-lock out saat development
    | (apalagi IP rumah/kampus/ngrok bisa berubah-ubah). Isi env ini nanti kalau
    | server sudah production dan kamu tahu persis IP kantor/admin yang tetap.
    |
    */
    'allowed_ips' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('ADMIN_ALLOWED_IPS', ''))
    ))),

];