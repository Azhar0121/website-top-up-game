<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blokir akses ke /admin/* kalau IP request tidak ada di whitelist.
 * Nonaktif otomatis kalau config('admin.allowed_ips') kosong (lihat config/admin.php).
 */
class RestrictAdminIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = config('admin.allowed_ips', []);

        // Whitelist tidak dikonfigurasi - fitur ini dianggap nonaktif, izinkan semua.
        if (empty($allowedIps)) {
            return $next($request);
        }

        if (! in_array($request->ip(), $allowedIps, true)) {
            abort(403, 'Akses dashboard admin ditolak dari alamat IP ini.');
        }

        return $next($request);
    }
}
