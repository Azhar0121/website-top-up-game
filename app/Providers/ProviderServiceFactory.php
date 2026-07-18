<?php

namespace App\Providers;

use App\Models\Provider;
use InvalidArgumentException;

class ProviderServiceFactory
{
    public static function make(Provider $provider): ProviderInterface
    {
        return match ($provider->code) {
            'digiflazz'    => new DigiflazzService($provider),
            'mock_digiflazz', 'mock_backup' => new MockDigiflazzService($provider),
            'vip_reseller' => new VipResellerService($provider),
            default => throw new InvalidArgumentException(
                "Provider service untuk code [{$provider->code}] belum diimplementasikan."
            ),
        };
    }
}
