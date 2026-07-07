<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'provider_id', 'type', 'headers', 'payload', 'response', 'http_status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public static function record(array $data): self
    {
        return self::create([
            'order_id'    => $data['order_id'] ?? null,
            'provider_id' => $data['provider_id'] ?? null,
            'type'        => $data['type'],
            'headers'     => isset($data['headers']) ? json_encode($data['headers']) : null,
            'payload'     => isset($data['payload']) ? json_encode($data['payload']) : null,
            'response'    => isset($data['response']) ? json_encode($data['response']) : null,
            'http_status' => $data['http_status'] ?? null,
        ]);
    }
}