<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'company_id', 'lead_id', 'type', 'naam', 'bedrijfsnaam', 'email', 'telefoon',
    'adres', 'postcode', 'plaats', 'land', 'kvk_nummer', 'btw_nummer', 'notities',
])]
class Customer extends Model
{
    use SoftDeletes;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /** Display label: company name for business customers, otherwise the person. */
    public function getLabelAttribute(): string
    {
        if ($this->type === 'zakelijk' && $this->bedrijfsnaam) {
            return $this->naam ? "{$this->bedrijfsnaam} ({$this->naam})" : $this->bedrijfsnaam;
        }

        return $this->naam ?: ($this->bedrijfsnaam ?: 'Klant');
    }

    /** Multi-line billing address block. */
    public function getAddressBlockAttribute(): string
    {
        $parts = array_filter([
            $this->type === 'zakelijk' && $this->bedrijfsnaam ? $this->bedrijfsnaam : $this->naam,
            $this->adres,
            trim(($this->postcode ?? '') . ' ' . ($this->plaats ?? '')),
            $this->land !== 'Nederland' ? $this->land : null,
            $this->btw_nummer ? 'BTW: ' . $this->btw_nummer : null,
        ]);

        return implode("\n", $parts);
    }
}
