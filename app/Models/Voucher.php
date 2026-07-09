<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value', 'max_discount', 'min_transaction',
        'usage_limit', 'used_count', 'start_date', 'end_date', 'is_active',
    ];

    protected $casts = [
        'value'            => 'decimal:2',
        'max_discount'     => 'decimal:2',
        'min_transaction'  => 'decimal:2',
        'start_date'       => 'datetime',
        'end_date'         => 'datetime',
        'is_active'        => 'boolean',
    ];
}
