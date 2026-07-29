<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Data yang kena aksi (Product, Category, Voucher, Order, dll). Bisa null
     * kalau aksinya tidak nempel ke satu record spesifik (misal: login) atau
     * kalau record-nya sudah dihapus dari database.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Label yang enak dibaca untuk badge di UI.
     */
    public function actionLabel(): string
    {
        return match ($this->action) {
            'login'            => 'Login',
            'created'          => 'Tambah Data',
            'updated'          => 'Edit Data',
            'deleted'          => 'Hapus Data',
            'force_success'    => 'Force Success',
            'status_override'  => 'Override Status',
            'role_updated'     => 'Ubah Role',
            default            => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    public function badgeClass(): string
    {
        return match ($this->action) {
            'login'                        => 'badge-soft-primary',
            'created'                      => 'badge-soft-mint',
            'updated', 'role_updated'      => 'badge-soft-pink',
            'deleted'                      => 'badge-soft-danger',
            'force_success'                => 'badge-soft-success',
            'status_override'              => 'badge-soft-muted',
            default                        => 'badge-soft-muted',
        };
    }
}