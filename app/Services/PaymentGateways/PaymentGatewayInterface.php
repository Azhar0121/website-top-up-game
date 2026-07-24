<?php

namespace App\Services\PaymentGateways;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function createTransaction(Order $order, ?string $paymentMethodCode = null): array;

    public function verifySignature(array $payload): bool;

    public function extractReference(array $payload): ?string;

    public function mapStatus(array $payload): string;

    public function getAvailablePaymentMethods(int $amount): array;

    public function checkStatus(Order $order): ?array;
}