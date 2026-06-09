<?php

namespace App\Services\Publishing;

use App\Models\Car;
use App\Models\PlatformConnection;

/**
 * Feed-based syndication (Marktplaats, AutoTrack, …).
 *
 * "Publishing" a car means the company's per-platform feed now includes it;
 * the portal pulls that feed URL on a schedule. There is no per-car push call,
 * so this is real and immediate; the published CarPublication records drive
 * the feed contents (see CarFeedService).
 */
class FeedPublisher implements PlatformPublisherInterface
{
    public function publish(Car $car, PlatformConnection $connection): array
    {
        $apiKey = $car->company->api_key;

        return [
            'external_id' => "feed-{$connection->platform}-{$car->id}",
            'external_url' => route('feed.platform', [
                'apiKey' => $apiKey,
                'platform' => $connection->platform,
            ]),
            'metadata' => [
                'via' => 'feed',
                'platform' => $connection->platform,
            ],
        ];
    }

    public function unpublish(string $externalId, PlatformConnection $connection): bool
    {
        // PublishingService marks the publication removed, which excludes the
        // car from the platform feed on the next pull. Nothing else to do.
        return true;
    }

    public function validateConnection(PlatformConnection $connection): bool
    {
        return true;
    }
}
