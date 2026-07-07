<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'api_key', 'api_secret', 'merchant_code', 'is_sandbox', 'is_active',
    ];

    protected $casts = [
        'is_sandbox' => 'boolean',
        'is_active'  => 'boolean',
        'api_key'      => 'encrypted',
        'api_secret'   => 'encrypted',
        'merchant_code' => 'encrypted',
    ];
}