<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'user_id', 'product_id', 'provider_id',
        'target_game_id', 'target_server_id', 'customer_email', 'customer_whatsapp',
        'quantity', 'price', 'cost_price', 'voucher_code', 'discount_amount',
        'status', 'paid_at', 'completed_at', 'expired_at', 'stock_deducted_at',
    ];

    protected $casts = [
        'price'           => 'decimal:2',
        'cost_price'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'paid_at'         => 'datetime',
        'completed_at'    => 'datetime',
        'expired_at'      => 'datetime',
        'stock_deducted_at' => 'datetime',
    ];

    // Konstanta status
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID            = 'paid';
    public const STATUS_PROCESSING      = 'processing';
    public const STATUS_SUCCESS         = 'success';
    public const STATUS_FAILED          = 'failed';
    public const STATUS_EXPIRED         = 'expired';
    public const STATUS_REFUNDED        = 'refunded';
    public const STATUS_CANCELLED       = 'cancelled';

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->invoice_number)) {
                $order->invoice_number = 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class);
    }

    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function apiLogs(): HasMany
    {
        return $this->hasMany(ApiLog::class);
    }

    public function transitionTo(string $status, ?string $note = null, string $actor = 'system'): void
    {
        $this->update(['status' => $status]);

        $this->logs()->create([
            'status' => $status,
            'note'   => $note,
            'actor'  => $actor,
        ]);
    }
}
