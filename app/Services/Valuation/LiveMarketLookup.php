<?php

namespace App\Services\Valuation;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * "Kijkende" taxatie: haalt op het moment van waarderen echte, actuele
 * advertenties op van de publieke markt (Marktplaats) voor exact het gezochte
 * merk/model/bouwjaar/brandstof. Prijs, bouwjaar, kilometerstand en brandstof
 * komen gestructureerd uit de zoek-API (het `attributes`-blok per advertentie),
 * dus er wordt niet gegist. De ValuationEngine rekent de waarde direct uit deze
 * vergelijkbare advertenties.
 *
 * Resultaten worden 6 uur gecachet zodat een taxatie snel is en de bron niet
 * onnodig belast wordt. Faalt de lookup (blokkade, timeout, formaatwijziging),
 * dan geeft hij netjes een lege set terug en valt de engine terug op de
 * verzamelde marktdata en daarna de modelschatting.
 */
class LiveMarketLookup
{
    /** Auto's op Marktplaats. */
    private const CATEGORY_AUTOS = 91;

    private const SEARCH_URL = 'https://www.marktplaats.nl/lrp/api/search';

    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36';

    /** Alleen echte vraagprijzen; geen "n.o.t.k.", "op aanvraag", gereserveerd of gratis. */
    private const PRICE_TYPES = ['FIXED', 'MIN_BID', 'ASKING'];

    /** Advertenties binnen dit aantal jaar rond het bouwjaar tellen als vergelijkbaar. */
    private const YEAR_TOLERANCE = 2;

    private const CACHE_HOURS = 6;

    public function isEnabled(): bool
    {
        return (bool) config('valuation.live_lookup', true);
    }

    /**
     * Vergelijkbare, actuele advertenties voor een voertuig.
     *
     * @return Collection<int, object> objecten met ->prijs (centen) en ->kilometerstand (?int)
     */
    public function comparables(string $merk, ?string $model, int $year, ?string $brandstof): Collection
    {
        $key = 'mktlive:v2:' . md5(implode('|', [
            strtolower(trim($merk)),
            strtolower($this->queryModel($model)),
            $year,
            (string) $this->normaliseFuel($brandstof),
        ]));

        $data = Cache::remember($key, now()->addHours(self::CACHE_HOURS), function () use ($merk, $model, $year, $brandstof) {
            try {
                return $this->fetch($merk, $model, $year, $brandstof);
            } catch (\Throwable $e) {
                report($e);

                return [];
            }
        });

        return collect($data)->map(fn ($r) => (object) $r);
    }

    /** @return array<int, array{prijs:int, kilometerstand:?int}> */
    private function fetch(string $merk, ?string $model, int $year, ?string $brandstof): array
    {
        $response = Http::withHeaders([
            'User-Agent' => self::USER_AGENT,
            'Accept' => 'application/json',
        ])->connectTimeout(5)->timeout(12)->get(self::SEARCH_URL, [
            'l1CategoryId' => self::CATEGORY_AUTOS,
            'query' => trim($merk . ' ' . $this->queryModel($model)),
            'limit' => 100,
            'searchInTitleAndDescription' => 'true',
        ]);

        if (! $response->successful()) {
            return [];
        }

        $targetFuel = $this->normaliseFuel($brandstof);
        $out = [];

        foreach ((array) $response->json('listings', []) as $listing) {
            $priceCents = (int) data_get($listing, 'priceInfo.priceCents', 0);
            $priceType = (string) data_get($listing, 'priceInfo.priceType', '');

            // Onrealistische prijzen en niet-vraagprijzen weren.
            if ($priceCents < 50000 || $priceCents > 50000000 || ! in_array($priceType, self::PRICE_TYPES, true)) {
                continue;
            }

            $attr = $this->attributeMap($listing);

            // Bouwjaar moet bekend en vergelijkbaar zijn.
            $listingYear = isset($attr['constructionYear']) ? (int) $attr['constructionYear'] : null;
            if ($listingYear === null || abs($listingYear - $year) > self::YEAR_TOLERANCE) {
                continue;
            }

            // Zelfde brandstofsoort, zodat EV/diesel/benzine niet door elkaar lopen.
            if ($targetFuel && isset($attr['fuel']) && $this->normaliseFuel($attr['fuel']) !== $targetFuel) {
                continue;
            }

            $mileage = isset($attr['mileage']) ? (int) preg_replace('/\D/', '', (string) $attr['mileage']) : 0;

            $out[] = [
                'prijs' => $priceCents,
                'kilometerstand' => ($mileage >= 1000 && $mileage <= 500000) ? $mileage : null,
            ];
        }

        return $out;
    }

    /**
     * Zet RDW-modelnamen om naar wat op de Nederlandse markt gangbaar is, zodat
     * de zoekopdracht op Marktplaats aanslaat. De RDW gebruikt vaak Duitse namen:
     *   "3ER REIHE" -> "3-serie" (BMW), "C-KLASSE" -> "C-klasse" (Mercedes).
     */
    private function queryModel(?string $model): string
    {
        $m = trim((string) $model);
        if ($m === '') {
            return '';
        }
        if (preg_match('/^(\d)\s*ER\s+REIHE\b/i', $m, $mm)) {
            return $mm[1] . '-serie';
        }
        if (preg_match('/^([A-Za-z]{1,3})[\s-]*KLASSE\b/i', $m, $mm)) {
            return strtoupper($mm[1]) . '-klasse';
        }

        return $m;
    }

    /**
     * Platte key/value-map van het attributes-blok van een advertentie.
     *
     * @return array<string, string|null>
     */
    private function attributeMap(array $listing): array
    {
        $map = [];
        foreach ((array) data_get($listing, 'attributes', []) as $attribute) {
            if (isset($attribute['key'])) {
                $map[$attribute['key']] = $attribute['value'] ?? null;
            }
        }

        return $map;
    }

    private function normaliseFuel(?string $fuel): ?string
    {
        $f = strtolower(trim((string) $fuel));
        if ($f === '') {
            return null;
        }

        return match (true) {
            str_contains($f, 'elektr'), str_contains($f, 'electric') => 'elektrisch',
            str_contains($f, 'hybride'), str_contains($f, 'hybrid') => 'hybride',
            str_contains($f, 'diesel') => 'diesel',
            str_contains($f, 'benzine'), str_contains($f, 'petrol') => 'benzine',
            str_contains($f, 'lpg'), str_contains($f, 'cng'), str_contains($f, 'gas') => 'gas',
            str_contains($f, 'waterstof'), str_contains($f, 'hydrogen') => 'waterstof',
            default => $f,
        };
    }
}
