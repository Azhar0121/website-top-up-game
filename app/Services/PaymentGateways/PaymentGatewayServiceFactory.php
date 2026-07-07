<?php

namespace App\Services\PaymentGateways;

use App\Models\PaymentGateway;
use InvalidArgumentException;

class PaymentGatewayServiceFactory
{
    public static function make(PaymentGateway $gateway): PaymentGatewayInterface
    {
        return match ($gateway->code) {
            'midtrans' => new MidtransService($gateway),
            'tripay'   => new TripayService($gateway),
            default => throw new InvalidArgumentException(
                "Payment gateway service untuk code [{$gateway->code}] belum diimplementasikan."
            ),
        };
    }
}