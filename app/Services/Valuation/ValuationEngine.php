<?php

namespace App\Services\Valuation;

use App\Models\MarketListing;
use Illuminate\Support\Collection;

/**
 * Used-car value estimator.
 *
 * Prefers REAL market data: it finds comparable listings (same make/model/year/
 * fuel, widening progressively) and derives a value, correcting for mileage via
 * linear regression and reporting a confidence level. When there is not enough
 * market data it falls back to a transparent model-based estimate. All amounts
 * are handled in euros and returned as integers.
 */
class ValuationEngine
{
    private const MIN_COMPARABLES = 5;

    /**
     * @param array $v keys: merk, model, bouwjaar, brandstof, catalogusprijs
     */
    public function estimate(array $v, ?int $km = null): array
    {
        $merk = $v['merk'] ?? null;
        $model = $v['model'] ?? null;
        $year = isset($v['bouwjaar']) && $v['bouwjaar'] ? (int) $v['bouwjaar'] : null;
        $brandstof = $v['brandstof'] ?? null;

        if ($merk && $year) {
            $market = $this->fromMarket($merk, $model, $year, $brandstof, $km);
            if ($market) {
                return $market;
            }
        }

        return $this->fromModel($v, $year, $km);
    }

    private function fromMarket(string $merk, ?string $model, int $year, ?string $brandstof, ?int $km): ?array
    {
        // Progressive widening: tight match first, then looser.
        // Keep fuel matching as long as possible; only drop it as a last resort so
        // EV/diesel/petrol variants of the same model are not pooled into one median.
        $tiers = [
            ['model' => true, 'yr' => 1, 'fuel' => true],
            ['model' => true, 'yr' => 2, 'fuel' => true],
            ['model' => false, 'yr' => 1, 'fuel' => true],
            ['model' => true, 'yr' => 2, 'fuel' => false],
        ];

        foreach ($tiers as $t) {
            $q = MarketListing::query()->where('merk', $merk);
            if ($t['model'] && $model) {
                $q->where('model', $model);
            }
            $q->whereBetween('bouwjaar', [$year - $t['yr'], $year + $t['yr']]);
            if ($t['fuel'] && $brandstof) {
                $q->where('brandstof', $brandstof);
            }

            $rows = $q->get(['prijs', 'kilometerstand']);
            if ($rows->count() >= self::MIN_COMPARABLES) {
                return $this->compute($rows, $km);
            }
        }

        return null;
    }

    private function compute(Collection $rows, ?int $km): array
    {
        $n = $rows->count();
        // euros
        $prices = $rows->pluck('prijs')->map(fn ($p) => (int) $p / 100)->sort()->values();
        $median = $this->percentile($prices, 0.5);
        $low = $this->percentile($prices, 0.25);
        $high = $this->percentile($prices, 0.75);
        $mid = $median;

        $withKm = $rows->filter(function ($r) {
            $km = (int) $r->kilometerstand;
            return $km > 0 && $km <= 500000; // ignore implausible/typo odometers as leverage points
        });
        if ($km && $withKm->count() >= self::MIN_COMPARABLES) {
            $slope = $this->regressionSlope($withKm); // euro per km (expected negative)
            $avgKm = $withKm->avg('kilometerstand');
            $adjusted = $median + $slope * ($km - $avgKm);
            // Clamp the mileage correction to a sane band around the median.
            $mid = max($median * 0.65, min($median * 1.35, $adjusted));
        }

        $low = min($low, $mid * 0.9);
        $high = max($high, $mid * 1.1);

        $vertrouwen = $n >= 25 ? 'hoog' : ($n >= 12 ? 'midden' : 'laag');

        return [
            'beschikbaar' => true,
            'bron' => 'marktdata',
            'vertrouwen' => $vertrouwen,
            'aantal' => $n,
            'onder' => $this->roundTo($low),
            'midden' => $this->roundTo($mid),
            'boven' => $this->roundTo($high),
            'toelichting' => "Gebaseerd op {$n} vergelijkbare advertenties uit de marktdata"
                . ($km && $withKm->count() >= self::MIN_COMPARABLES ? ', gecorrigeerd voor kilometerstand' : '') . '.',
        ];
    }

    /**
     * Transparent fallback when there is not enough market data.
     */
    private function fromModel(array $v, ?int $year, ?int $km): array
    {
        if (!$year) {
            return ['beschikbaar' => false, 'toelichting' => 'Onvoldoende gegevens (geen bouwjaar) voor een indicatie.'];
        }

        $age = max(0, (int) date('Y') - $year);
        $catalogus = isset($v['catalogusprijs']) ? (int) $v['catalogusprijs'] : 0;
        $hasCatalogus = $catalogus > 0;

        $base = $hasCatalogus ? $catalogus : $this->baseByFuel($v['brandstof'] ?? null);
        $value = $base * (0.82 ** min($age, 4)) * (0.90 ** max(0, $age - 4));

        if ($km !== null && $km > 0) {
            $expected = max(10000, $age * 13000);
            $ratio = $km / $expected;
            $value *= max(0.55, min(1.15, 1.15 - 0.30 * $ratio));
        }

        $value = max(500, $value);

        return [
            'beschikbaar' => true,
            'bron' => 'model',
            'vertrouwen' => $hasCatalogus ? 'midden' : 'laag',
            'aantal' => 0,
            'onder' => $this->roundTo($value * 0.88),
            'midden' => $this->roundTo($value),
            'boven' => $this->roundTo($value * 1.12),
            'toelichting' => $hasCatalogus
                ? 'Modelschatting op basis van nieuwprijs, leeftijd' . ($km ? ' en kilometerstand' : '') . '. Nog geen marktdata voor dit model.'
                : 'Ruwe modelschatting: geen catalogusprijs bekend en nog geen marktdata. Importeer marktdata of koppel een bron voor een nauwkeurige waarde.',
        ];
    }

    private function baseByFuel(?string $brandstof): int
    {
        return match (strtolower((string) $brandstof)) {
            'elektriciteit' => 42000,
            'diesel' => 34000,
            'lpg', 'cng' => 22000,
            default => 29000, // benzine / hybride / onbekend
        };
    }

    private function percentile(Collection $sorted, float $p): float
    {
        $count = $sorted->count();
        if ($count === 0) {
            return 0.0;
        }
        if ($count === 1) {
            return (float) $sorted->first();
        }
        $rank = $p * ($count - 1);
        $low = (int) floor($rank);
        $high = (int) ceil($rank);
        $frac = $rank - $low;
        return $sorted[$low] + ($sorted[$high] - $sorted[$low]) * $frac;
    }

    /**
     * Least-squares slope of price (euro) on mileage (km).
     */
    private function regressionSlope(Collection $rows): float
    {
        $n = $rows->count();
        $sumX = $sumY = $sumXY = $sumXX = 0.0;
        foreach ($rows as $r) {
            $x = (float) $r->kilometerstand;
            $y = (float) $r->prijs / 100;
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumXX += $x * $x;
        }
        $denom = ($n * $sumXX) - ($sumX * $sumX);
        if (abs($denom) < 1e-6) {
            return 0.0;
        }
        $slope = (($n * $sumXY) - ($sumX * $sumY)) / $denom;
        // Only allow a non-positive slope (more km should not raise the value).
        return min(0.0, $slope);
    }

    private function roundTo(float $euro): int
    {
        $step = $euro < 5000 ? 100 : 250;
        return (int) (round($euro / $step) * $step);
    }
}
