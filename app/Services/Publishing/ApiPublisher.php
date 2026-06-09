<?php

namespace App\Services\Publishing;

use App\Models\Car;
use App\Models\PlatformConnection;

/**
 * Placeholder for platforms that require a direct API push (Facebook, Instagram).
 *
 * Credentials are accepted and stored so the connection is "ready", but the
 * actual API integration is not wired up yet, so publishing throws
 * PublisherNotConfiguredException so the car is marked `pending` (NOT a fake
 * "published"). Replace this with concrete per-platform publishers
 * (e.g. FacebookPublisher) once the partner API spec + credentials are available.
 */
class ApiPublisher implements PlatformPublisherInterface
{
    public function publish(Car $car, PlatformConnection $connection): array
    {
        $name = config("platforms.{$connection->platform}.name", $connection->platform);

        throw new PublisherNotConfiguredException(
            "Directe koppeling met {$name} staat klaar maar is nog niet geactiveerd. "
            . 'Je inloggegevens zijn opgeslagen; de auto wordt automatisch geplaatst zodra de koppeling actief is.'
        );
    }

    public function unpublish(string $externalId, PlatformConnection $connection): bool
    {
        return true;
    }

    public function validateConnection(PlatformConnection $connection): bool
    {
        // Accept the stored credentials; real validation happens once the
        // platform's API integration is implemented.
        return !empty($connection->credentials);
    }
}
