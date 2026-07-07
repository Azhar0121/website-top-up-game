<?php

namespace App\Services\PaymentGateways;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function createTransaction(Order $order): array;

    public function verifySignature(array $payload): bool;

    public function extractReference(array $payload): ?string;

    public function mapStatus(array $payload): string;
}