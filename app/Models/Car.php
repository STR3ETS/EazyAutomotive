<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'company_id', 'kenteken', 'merk', 'handelsbenaming', 'voertuigsoort',
    'inrichting', 'eerste_kleur', 'tweede_kleur', 'aantal_cilinders',
    'cilinderinhoud', 'vermogen', 'massa_rijklaar', 'massa_ledig_voertuig',
    'toegestane_maximum_massa_voertuig', 'aantal_zitplaatsen',
    'aantal_deuren', 'aantal_wielen', 'wielbasis', 'datum_eerste_toelating',
    'datum_eerste_tenaamstelling_in_nederland', 'vervaldatum_apk',
    'catalogusprijs', 'europese_voertuigcategorie', 'typegoedkeuringsnummer',
    'zuinigheidsclassificatie', 'bruto_bpm', 'brandstof_omschrijving',
    'uitlaatemissieniveau', 'wam_verzekerd', 'export_indicator',
    'tellerstandoordeel', 'rdw_raw_data',
    'titel', 'beschrijving', 'prijs', 'prijs_type', 'kilometerstand',
    'bouwjaar', 'status', 'is_featured', 'extra_opties', 'custom_fields',
])]
class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'datum_eerste_toelating' => 'date',
            'datum_eerste_tenaamstelling_in_nederland' => 'date',
            'vervaldatum_apk' => 'date',
            'wam_verzekerd' => 'boolean',
            'export_indicator' => 'boolean',
            'is_featured' => 'boolean',
            'rdw_raw_data' => 'array',
            'extra_opties' => 'array',
            'custom_fields' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(CarImage::class)->where('is_primary', true);
    }

    public function views(): HasMany
    {
        return $this->hasMany(CarView::class);
    }

    public function publications(): HasMany
    {
        return $this->hasMany(CarPublication::class);
    }

    public function activePublications(): HasMany
    {
        return $this->hasMany(CarPublication::class)->where('status', 'published');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getFormattedPriceAttribute(): string
    {
        if (!$this->prijs) {
            return 'Prijs op aanvraag';
        }

        return '€ ' . number_format($this->prijs / 100, 0, ',', '.');
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->titel ?: trim("{$this->merk} {$this->handelsbenaming}");
    }
}
