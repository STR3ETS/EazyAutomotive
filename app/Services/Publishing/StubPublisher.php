<?php

namespace App\Services\Publishing;

use App\Models\Car;
use App\Models\PlatformConnection;
use Illuminate\Support\Str;

class StubPublisher implements PlatformPublisherInterface
{
    public function publish(Car $car, PlatformConnection $connection): array
    {
        return [
            'external_id' => 'stub_' . Str::random(12),
            'external_url' => 'https://' . $connection->platform . '.nl/listing/stub-' . $car->id,
            'metadata' => [
                'published_via' => 'stub',
                'simulated' => true,
            ],
        ];
    }

    public function unpublish(string $externalId, PlatformConnection $connection): bool
    {
        return true;
    }

    public function validateConnection(PlatformConnection $connection): bool
    {
        return !empty($connection->credentials);
    }
}
