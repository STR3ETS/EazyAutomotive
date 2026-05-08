<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'car_id', 'platform_connection_id', 'company_id', 'status',
    'external_id', 'external_url', 'error_message', 'metadata',
    'published_at', 'unpublished_at',
])]
class CarPublication extends Model
{
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'published_at' => 'datetime',
            'unpublished_at' => 'datetime',
        ];
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function platformConnection(): BelongsTo
    {
        return $this->belongsTo(PlatformConnection::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'published' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'icon' => 'fa-circle-check', 'label' => 'Gepubliceerd'],
            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'fa-clock', 'label' => 'Wachtend'],
            'publishing' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => 'fa-spinner fa-spin', 'label' => 'Bezig...'],
            'failed' => ['bg' => 'bg-red-50', 'text' => 'text-red-500', 'icon' => 'fa-circle-exclamation', 'label' => 'Mislukt'],
            'unpublishing' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-500', 'icon' => 'fa-spinner fa-spin', 'label' => 'Verwijderen...'],
            'removed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'icon' => 'fa-circle-minus', 'label' => 'Verwijderd'],
            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'icon' => 'fa-question', 'label' => ucfirst($this->status)],
        };
    }
}
