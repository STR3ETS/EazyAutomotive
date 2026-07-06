<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Een geregistreerde RDW-mutatie (vrijwaring bij opnemen in bedrijfsvoorraad, of
 * uit bedrijfsvoorraad bij verkoop). Puur audit/log: de gevoelige
 * tenaamstellingscode wordt hier nooit bewaard.
 */
#[Fillable([
    'company_id', 'user_id', 'car_id', 'type', 'kenteken', 'status', 'mode',
    'vrijwaringsbewijs', 'bewijs_datum', 'referentie', 'foutmelding',
])]
class BedrijfsvoorraadMutatie extends Model
{
    protected $table = 'bedrijfsvoorraad_mutaties';

    protected function casts(): array
    {
        return [
            'bewijs_datum' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function isGeslaagd(): bool
    {
        return $this->status === 'geslaagd';
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'vrijwaring' => 'Vrijwaring (in bedrijfsvoorraad)',
            'uitvoorraad' => 'Uit bedrijfsvoorraad',
            default => ucfirst((string) $this->type),
        };
    }
}
