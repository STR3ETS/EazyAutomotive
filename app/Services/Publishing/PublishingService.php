<?php

namespace App\Services\Publishing;

use App\Models\Car;
use App\Models\CarPublication;
use App\Models\PlatformConnection;
use App\Models\PublicationLog;
use Illuminate\Support\Facades\Auth;

class PublishingService
{
    public function getPublisher(string $platform): PlatformPublisherInterface
    {
        $delivery = config("platforms.{$platform}.delivery", 'feed');

        return match ($delivery) {
            // Feed-based platforms (Marktplaats, AutoTrack) pull our inventory feed.
            'feed' => app(FeedPublisher::class),
            // Direct-API platforms accept credentials but need a real integration.
            default => app(ApiPublisher::class),
        };
    }

    public function publish(Car $car, PlatformConnection $connection): CarPublication
    {
        $publication = CarPublication::updateOrCreate(
            ['car_id' => $car->id, 'platform_connection_id' => $connection->id],
            [
                'company_id' => $car->company_id,
                'status' => 'publishing',
                'error_message' => null,
            ]
        );

        try {
            $publisher = $this->getPublisher($connection->platform);
            $result = $publisher->publish($car, $connection);

            $publication->update([
                'status' => 'published',
                'external_id' => $result['external_id'],
                'external_url' => $result['external_url'],
                'metadata' => $result['metadata'] ?? null,
                'published_at' => now(),
                'unpublished_at' => null,
            ]);

            $this->log($car, $connection, 'publish', 'success', 'Auto gepubliceerd op ' . config("platforms.{$connection->platform}.name", $connection->platform));
        } catch (PublisherNotConfiguredException $e) {
            // Connection is ready but the direct integration isn't active yet,
            // keep the car queued rather than marking it failed.
            $publication->update([
                'status' => 'pending',
                'error_message' => $e->getMessage(),
                'external_id' => null,
                'external_url' => null,
            ]);

            $this->log($car, $connection, 'publish', 'pending', $e->getMessage());
        } catch (\Throwable $e) {
            $publication->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->log($car, $connection, 'publish', 'failed', $e->getMessage());
        }

        return $publication->fresh();
    }

    public function unpublish(CarPublication $publication): CarPublication
    {
        $publication->update(['status' => 'unpublishing']);

        try {
            $publisher = $this->getPublisher($publication->platformConnection->platform);
            $publisher->unpublish($publication->external_id, $publication->platformConnection);

            $publication->update([
                'status' => 'removed',
                'unpublished_at' => now(),
            ]);

            $this->log(
                $publication->car,
                $publication->platformConnection,
                'unpublish',
                'success',
                'Auto verwijderd van ' . config("platforms.{$publication->platformConnection->platform}.name", $publication->platformConnection->platform)
            );
        } catch (\Throwable $e) {
            $publication->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->log(
                $publication->car,
                $publication->platformConnection,
                'unpublish',
                'failed',
                $e->getMessage()
            );
        }

        return $publication->fresh();
    }

    private function log(Car $car, PlatformConnection $connection, string $action, string $status, ?string $message = null): void
    {
        PublicationLog::create([
            'company_id' => $car->company_id,
            'car_id' => $car->id,
            'platform_connection_id' => $connection->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'platform' => $connection->platform,
            'status' => $status,
            'message' => $message,
        ]);
    }
}
