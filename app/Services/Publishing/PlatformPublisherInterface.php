<?php

namespace App\Services\Publishing;

use App\Models\Car;
use App\Models\PlatformConnection;

interface PlatformPublisherInterface
{
    /**
     * Publish a car listing to the platform.
     *
     * @return array{external_id: string, external_url: string, metadata: array}
     */
    public function publish(Car $car, PlatformConnection $connection): array;

    /**
     * Remove a car listing from the platform.
     */
    public function unpublish(string $externalId, PlatformConnection $connection): bool;

    /**
     * Validate that the connection credentials are working.
     */
    public function validateConnection(PlatformConnection $connection): bool;
}
