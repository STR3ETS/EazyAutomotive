<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'company_id', 'platform', 'status', 'credentials',
    'settings', 'connected_at', 'last_error',
])]
class PlatformConnection extends Model
{
    protected function casts(): array
    {
        return [
            'credentials' => 'encrypted:array',
            'settings' => 'array',
            'connected_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(CarPublication::class);
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function scopeConnected(Builder $query): Builder
    {
        return $query->where('status', 'connected');
    }
}
