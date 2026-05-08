<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'name', 'slug', 'email', 'phone', 'address', 'city',
    'postal_code', 'country', 'website', 'kvk_number',
    'btw_number', 'logo_path', 'embed_settings', 'is_active',
])]
class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'embed_settings' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Company $company) {
            if (empty($company->api_key)) {
                $company->api_key = Str::random(64);
            }
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function carViews(): HasMany
    {
        return $this->hasMany(CarView::class);
    }

    public function platformConnections(): HasMany
    {
        return $this->hasMany(PlatformConnection::class);
    }

    public function carPublications(): HasMany
    {
        return $this->hasMany(CarPublication::class);
    }

    public function publicationLogs(): HasMany
    {
        return $this->hasMany(PublicationLog::class);
    }
}
