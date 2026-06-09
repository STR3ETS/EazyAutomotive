<?php

namespace App\Services\Publishing;

use RuntimeException;

/**
 * Thrown when a platform requires a direct API integration that has not yet
 * been wired up (credentials stored, integration pending). Lets the publishing
 * flow mark the publication as `pending` rather than failing it.
 */
class PublisherNotConfiguredException extends RuntimeException
{
}
