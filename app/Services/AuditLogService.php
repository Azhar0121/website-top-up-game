<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * "Sistem Multi-Admin & Audit Trails":
 * "Audit Log: Merekam siapa admin yang melakukan Login, Edit Produk, Edit Margin,
 * Action Refund, dan Hapus Data (lengkap dengan timestamp & IP)."
 */
class AuditLogService
{
    public static function record(
        string $action,
        string $description,
        ?Model $subject = null,
        array $changes = [],
        ?User $actor = null
    ): AuditLog {
        $request = request();
        $actor ??= auth()->user();

        return AuditLog::create([
            'user_id'      => $actor?->id,
            'action'       => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id'   => $subject?->getKey(),
            'description'  => Str::limit($description, 490, ''),
            'changes'      => $changes ?: null,
            'ip_address'   => $request?->ip(),
            'user_agent'   => $request?->userAgent() ? Str::limit($request->userAgent(), 250, '') : null,
        ]);
    }

    /**
     * 
     * @return array<string, array{old: mixed, new: mixed}>
     */
    public static function diff(array $before, array $after, array $hidden = []): array
    {
        $changed = [];

        foreach ($after as $key => $newValue) {
            $oldValue = $before[$key] ?? null;

            if ($oldValue == $newValue) {
                continue;
            }

            if (in_array($key, $hidden, true)) {
                $changed[$key] = ['old' => '(disembunyikan)', 'new' => '(disembunyikan)'];

                continue;
            }

            $changed[$key] = ['old' => $oldValue, 'new' => $newValue];
        }

        return $changed;
    }
}