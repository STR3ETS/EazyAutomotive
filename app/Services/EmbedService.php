<?php

namespace App\Services;

use App\Models\Car;
use App\Models\Company;

class EmbedService
{
    /**
     * Get cars for a given API key, suitable for embed display.
     */
    public function getCarsForEmbed(string $apiKey, array $filters = []): ?array
    {
        $company = Company::where('api_key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$company) {
            return null;
        }

        $query = $company->cars()
            ->active()
            ->with('primaryImage')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at');

        if (!empty($filters['merk'])) {
            $query->where('merk', $filters['merk']);
        }

        if (!empty($filters['brandstof'])) {
            $query->where('brandstof_omschrijving', $filters['brandstof']);
        }

        if (!empty($filters['prijs_max'])) {
            $query->where('prijs', '<=', (int) $filters['prijs_max'] * 100);
        }

        if (!empty($filters['bouwjaar_min'])) {
            $query->where('bouwjaar', '>=', (int) $filters['bouwjaar_min']);
        }

        return [
            'company' => [
                'name' => $company->name,
                'logo' => $company->logo_path ? asset('storage/' . $company->logo_path) : null,
            ],
            'settings' => $company->embed_settings ?? [],
            'cars' => $query->paginate(24)->through(fn (Car $car) => [
                'id' => $car->id,
                'title' => $car->display_title,
                'kenteken' => $car->kenteken,
                'merk' => $car->merk,
                'model' => $car->handelsbenaming,
                'bouwjaar' => $car->bouwjaar,
                'brandstof' => $car->brandstof_omschrijving,
                'kleur' => $car->eerste_kleur,
                'km_stand' => $car->kilometerstand,
                'prijs' => $car->formatted_price,
                'prijs_raw' => $car->prijs,
                'image' => $car->primaryImage?->url,
                'apk_tot' => $car->vervaldatum_apk?->format('d-m-Y'),
            ]),
        ];
    }

    /**
     * Get detail data for a single car, scoped to a company.
     */
    public function getCarDetail(Company $company, int $carId): ?array
    {
        $car = $company->cars()
            ->active()
            ->with('images')
            ->find($carId);

        if (!$car) {
            return null;
        }

        return [
            'id' => $car->id,
            'title' => $car->display_title,
            'kenteken' => $car->kenteken,
            'merk' => $car->merk,
            'model' => $car->handelsbenaming,
            'bouwjaar' => $car->bouwjaar,
            'brandstof' => $car->brandstof_omschrijving,
            'kleur' => $car->eerste_kleur,
            'tweede_kleur' => $car->tweede_kleur,
            'km_stand' => $car->kilometerstand,
            'prijs' => $car->formatted_price,
            'prijs_raw' => $car->prijs,
            'beschrijving' => $car->beschrijving,
            'inrichting' => $car->inrichting,
            'vermogen' => $car->vermogen,
            'cilinderinhoud' => $car->cilinderinhoud,
            'aantal_zitplaatsen' => $car->aantal_zitplaatsen,
            'aantal_deuren' => $car->aantal_deuren,
            'apk_tot' => $car->vervaldatum_apk?->format('d-m-Y'),
            'datum_eerste_toelating' => $car->datum_eerste_toelating?->format('d-m-Y'),
            'extra_opties' => $car->extra_opties ?? [],
            'images' => $car->images->map(fn ($img) => $img->url)->values()->toArray(),
        ];
    }
}
