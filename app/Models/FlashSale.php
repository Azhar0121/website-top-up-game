<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FlashSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'discount_type',
        'discount_value',
        'start_at',
        'end_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'start_at'       => 'datetime',
        'end_at'         => 'datetime',
        'is_active'      => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products');
    }

    /**
     * Sedang berjalan SEKARANG (aktif + di dalam rentang start_at/end_at).
     * Dipakai di checkout & tampilan produk, bukan cuma status is_active.
     */
    public function scopeRunning($query)
    {
        return $query->where('is_active', true)
            ->where('start_at', '<=', now())
            ->where('end_at', '>=', now());
    }

    public function isRunning(): bool
    {
        return $this->is_active
            && $this->start_at->isPast()
            && $this->end_at->isFuture();
    }

    public function statusLabel(): string
    {
        if (! $this->is_active) {
            return 'Nonaktif';
        }

        if ($this->start_at->isFuture()) {
            return 'Terjadwal';
        }

        if ($this->end_at->isPast()) {
            return 'Berakhir';
        }

        return 'Berjalan';
    }
}
