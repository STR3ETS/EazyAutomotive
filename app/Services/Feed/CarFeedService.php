<?php

namespace App\Services\Feed;

use App\Models\Car;
use App\Models\Company;
use DOMDocument;
use Illuminate\Support\Collection;

/**
 * Builds the public occasion feed (XML/CSV) for a company's inventory.
 *
 * The full feed exposes all ACTIVE cars; a per-platform feed exposes only the
 * cars that have a `published` CarPublication for that platform's connection.
 * External portals (Marktplaats, AutoTrack, …) pull these URLs on a schedule.
 */
class CarFeedService
{
    /**
     * Mapped vehicle rows for a company. When $platform is given, only cars
     * published to that platform are included; otherwise all active cars.
     */
    public function vehicles(Company $company, ?string $platform = null): Collection
    {
        $query = $company->cars()
            ->active()
            ->with('images')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at');

        if ($platform !== null) {
            $query->whereHas('publications', function ($q) use ($platform) {
                $q->where('status', 'published')
                    ->whereHas('platformConnection', fn ($c) => $c->where('platform', $platform));
            });
        }

        return $query->get()->map(fn (Car $car) => $this->mapVehicle($car, $company));
    }

    /**
     * Normalize a Car into a flat, portal-friendly array.
     */
    private function mapVehicle(Car $car, Company $company): array
    {
        return [
            'id' => $car->id,
            'kenteken' => $car->kenteken,
            'merk' => $car->merk,
            'model' => $car->handelsbenaming,
            'titel' => $car->display_title,
            'carrosserie' => $car->inrichting ?: $car->voertuigsoort,
            'bouwjaar' => $car->bouwjaar,
            'kilometerstand' => $car->kilometerstand,
            'prijs' => $car->prijs !== null ? number_format($car->prijs / 100, 2, '.', '') : null,
            'prijs_type' => $car->prijs_type,
            'valuta' => 'EUR',
            'brandstof' => $car->brandstof_omschrijving,
            'kleur' => $car->eerste_kleur,
            'tweede_kleur' => $car->tweede_kleur,
            'aantal_deuren' => $car->aantal_deuren,
            'aantal_zitplaatsen' => $car->aantal_zitplaatsen,
            'cilinderinhoud' => $car->cilinderinhoud,
            'vermogen' => $car->vermogen,
            'apk_vervaldatum' => optional($car->vervaldatum_apk)->format('Y-m-d'),
            'eerste_toelating' => optional($car->datum_eerste_toelating)->format('Y-m-d'),
            'conditie' => 'gebruikt',
            'opties' => array_values(array_filter((array) ($car->extra_opties ?? []))),
            'beschrijving' => $car->beschrijving,
            'url' => $company->website ?: null,
            'afbeeldingen' => $car->images
                ->sortByDesc('is_primary')
                ->map(fn ($img) => $img->url)
                ->values()
                ->all(),
        ];
    }

    /**
     * Render the feed as well-formed, escaped XML.
     */
    public function toXml(Company $company, Collection $vehicles, ?string $platform = null): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $root = $dom->createElement('eazyautomotive');
        $root->setAttribute('version', '1.0');
        if ($platform !== null) {
            $root->setAttribute('platform', $platform);
        }
        $dom->appendChild($root);

        // Dealer block
        $dealer = $dom->createElement('dealer');
        $this->appendText($dom, $dealer, 'naam', $company->name);
        $this->appendText($dom, $dealer, 'plaats', $company->city);
        $this->appendText($dom, $dealer, 'adres', $company->address);
        $this->appendText($dom, $dealer, 'postcode', $company->postal_code);
        $this->appendText($dom, $dealer, 'telefoon', $company->phone);
        $this->appendText($dom, $dealer, 'email', $company->email);
        $this->appendText($dom, $dealer, 'website', $company->website);
        $root->appendChild($dealer);

        $vehiclesEl = $dom->createElement('voertuigen');
        $vehiclesEl->setAttribute('aantal', (string) $vehicles->count());
        $root->appendChild($vehiclesEl);

        foreach ($vehicles as $v) {
            $vehicle = $dom->createElement('voertuig');

            foreach ([
                'id', 'kenteken', 'merk', 'model', 'titel', 'carrosserie',
                'bouwjaar', 'kilometerstand', 'brandstof', 'kleur', 'tweede_kleur',
                'aantal_deuren', 'aantal_zitplaatsen', 'cilinderinhoud', 'vermogen',
                'apk_vervaldatum', 'eerste_toelating', 'conditie', 'url',
            ] as $field) {
                $this->appendText($dom, $vehicle, $field, $v[$field] ?? null);
            }

            // Price with currency attribute
            if ($v['prijs'] !== null) {
                $price = $dom->createElement('prijs', $v['prijs']);
                $price->setAttribute('valuta', $v['valuta']);
                if (!empty($v['prijs_type'])) {
                    $price->setAttribute('type', (string) $v['prijs_type']);
                }
                $vehicle->appendChild($price);
            }

            // Options
            if (!empty($v['opties'])) {
                $opties = $dom->createElement('opties');
                foreach ($v['opties'] as $optie) {
                    $this->appendText($dom, $opties, 'optie', $optie);
                }
                $vehicle->appendChild($opties);
            }

            // Description (CDATA, may contain markup/newlines)
            if (!empty($v['beschrijving'])) {
                $desc = $dom->createElement('beschrijving');
                $desc->appendChild($dom->createCDATASection($v['beschrijving']));
                $vehicle->appendChild($desc);
            }

            // Images
            if (!empty($v['afbeeldingen'])) {
                $images = $dom->createElement('afbeeldingen');
                foreach ($v['afbeeldingen'] as $i => $url) {
                    $img = $dom->createElement('afbeelding', htmlspecialchars($url, ENT_XML1));
                    $img->setAttribute('volgorde', (string) ($i + 1));
                    if ($i === 0) {
                        $img->setAttribute('hoofd', 'true');
                    }
                    $images->appendChild($img);
                }
                $vehicle->appendChild($images);
            }

            $vehiclesEl->appendChild($vehicle);
        }

        return $dom->saveXML();
    }

    /**
     * Render the feed as a semicolon-separated CSV (NL/Excel convention).
     */
    public function toCsv(Collection $vehicles): string
    {
        $columns = [
            'id', 'kenteken', 'merk', 'model', 'titel', 'carrosserie', 'bouwjaar',
            'kilometerstand', 'prijs', 'valuta', 'brandstof', 'kleur', 'aantal_deuren',
            'aantal_zitplaatsen', 'cilinderinhoud', 'vermogen', 'apk_vervaldatum',
            'eerste_toelating', 'conditie', 'opties', 'url', 'hoofdafbeelding', 'aantal_afbeeldingen',
        ];

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $columns, ';', '"', '');

        foreach ($vehicles as $v) {
            fputcsv($handle, [
                $v['id'],
                $v['kenteken'],
                $v['merk'],
                $v['model'],
                $v['titel'],
                $v['carrosserie'],
                $v['bouwjaar'],
                $v['kilometerstand'],
                $v['prijs'],
                $v['valuta'],
                $v['brandstof'],
                $v['kleur'],
                $v['aantal_deuren'],
                $v['aantal_zitplaatsen'],
                $v['cilinderinhoud'],
                $v['vermogen'],
                $v['apk_vervaldatum'],
                $v['eerste_toelating'],
                $v['conditie'],
                implode('|', $v['opties']),
                $v['url'],
                $v['afbeeldingen'][0] ?? '',
                count($v['afbeeldingen']),
            ], ';', '"', '');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        // Prepend UTF-8 BOM so Excel reads accented characters correctly.
        return "\xEF\xBB\xBF" . $csv;
    }

    private function appendText(DOMDocument $dom, \DOMElement $parent, string $name, $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $parent->appendChild($dom->createElement($name, htmlspecialchars((string) $value, ENT_XML1)));
    }
}
