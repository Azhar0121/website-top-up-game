<?php

namespace App\Services\Providers;

use App\Models\Order;

interface ProviderInterface
{
    public function topup(Order $order, string $providerSkuCode): array;

    public function checkStatus(string $providerTrxId): array;
}