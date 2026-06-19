<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'company_id', 'car_id', 'naam', 'email', 'telefoon',
    'gewenste_datum', 'bericht', 'status', 'ip_address', 'user_agent',
])]
class ProefritAanvraag extends Model
{
    protected $table = 'proefrit_aanvragen';

    protected function casts(): array
    {
        return [
            'gewenste_datum' => 'date',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'nieuw' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => 'fa-circle-dot', 'label' => 'Nieuw'],
            'gecontacteerd' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'icon' => 'fa-phone', 'label' => 'Gecontacteerd'],
            'ingepland' => ['bg' => 'bg-eazy-50', 'text' => 'text-eazy-dark', 'icon' => 'fa-calendar-check', 'label' => 'Ingepland'],
            'afgerond' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'icon' => 'fa-circle-check', 'label' => 'Afgerond'],
            'geannuleerd' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'icon' => 'fa-circle-xmark', 'label' => 'Geannuleerd'],
            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'icon' => 'fa-circle', 'label' => ucfirst($this->status)],
        };
    }
}
