<?php

namespace App\Services;

use Illuminate\Support\Carbon;

/**
 * Computes vehicle fiscal figures from the verified tables in config/fiscaal.php:
 * rest-BPM (forfaitaire tabel), bijtelling, milieuzone access, and the
 * wegenbelasting determinants. Exact MRB is not publicly tabulated, so we show
 * its determinants plus the official calculator instead of a fabricated amount.
 */
class FiscalService
{
    public function restBpm(?int $brutoBpm, ?string $eersteToelatingRdw): ?array
    {
        if (!$brutoBpm || $brutoBpm <= 0 || !$eersteToelatingRdw) {
            return null;
        }

        $months = $this->monthsSince($eersteToelatingRdw);
        if ($months === null) {
            return null;
        }

        $pct = $this->afschrijvingPercentage($months);
        $rest = (int) round($brutoBpm * (1 - $pct / 100));

        return [
            'bruto' => $this->euro($brutoBpm),
            'afschrijving' => rtrim(rtrim(number_format($pct, 2, ',', ''), '0'), ',') . '%',
            'rest' => $this->euro(max(0, $rest)),
            'toelichting' => 'Indicatie via de forfaitaire afschrijvingstabel (' . config('fiscaal.tariefjaar') . ').',
        ];
    }

    public function bijtelling(?int $catalogusEur, ?string $brandstof, ?int $leeftijdJaren, ?int $dagwaardeEur): ?array
    {
        $cfg = config('fiscaal.bijtelling');

        // Youngtimer: flat percentage over the (estimated) market value.
        if ($leeftijdJaren !== null && $leeftijdJaren >= $cfg['youngtimer_jaar'] && $dagwaardeEur && $dagwaardeEur > 0) {
            $perJaar = (int) round($dagwaardeEur * $cfg['youngtimer_pct'] / 100);
            return [
                'percentage' => $cfg['youngtimer_pct'] . '%',
                'grondslag' => 'dagwaarde (geschat) ' . $this->euro($dagwaardeEur),
                'per_jaar' => $this->euro($perJaar),
                'per_maand' => $this->euro((int) round($perJaar / 12)),
                'toelichting' => 'Youngtimer (' . $cfg['youngtimer_jaar'] . ' jaar of ouder): ' . $cfg['youngtimer_pct'] . '% over de dagwaarde.',
            ];
        }

        if (!$catalogusEur || $catalogusEur <= 0) {
            return null;
        }

        $isEv = strtolower((string) $brandstof) === 'elektriciteit';
        if ($isEv) {
            $drempel = $cfg['ev_drempel_eur'];
            $perJaar = (int) round(
                min($catalogusEur, $drempel) * $cfg['ev_pct'] / 100
                + max(0, $catalogusEur - $drempel) * $cfg['ev_pct_boven_drempel'] / 100
            );
            $pctLabel = $catalogusEur <= $drempel
                ? $cfg['ev_pct'] . '%'
                : $cfg['ev_pct'] . '% tot ' . $this->euro($drempel) . ', daarboven ' . $cfg['ev_pct_boven_drempel'] . '%';
        } else {
            $perJaar = (int) round($catalogusEur * $cfg['standaard_pct'] / 100);
            $pctLabel = $cfg['standaard_pct'] . '%';
        }

        return [
            'percentage' => $pctLabel,
            'grondslag' => 'catalogusprijs ' . $this->euro($catalogusEur),
            'per_jaar' => $this->euro($perJaar),
            'per_maand' => $this->euro((int) round($perJaar / 12)),
            'toelichting' => 'Indicatie op basis van de bijtellingsregels ' . config('fiscaal.tariefjaar') . '.',
        ];
    }

    public function milieuzone(?string $brandstof, ?int $euroKlasse): array
    {
        $diesel = strtolower((string) $brandstof) === 'diesel';
        $steden = [];

        foreach (config('fiscaal.milieuzone.diesel_min_euro') as $stad => $min) {
            if (!$diesel) {
                $toegang = true;
            } elseif ($euroKlasse === null) {
                $toegang = null; // unknown
            } else {
                $toegang = $euroKlasse >= $min;
            }
            $steden[$stad] = $toegang;
        }

        return [
            'steden' => $steden,
            'diesel' => $diesel,
            'euroklasse' => $euroKlasse,
            'toelichting' => $diesel
                ? 'Voor diesel geldt een minimale EURO-klasse per stad. Benzine, LPG en elektrisch hebben vrije toegang.'
                : 'Benzine, LPG en elektrische auto\'s hebben vrije toegang tot alle milieuzones.',
        ];
    }

    public function wegenbelasting(?int $ledigGewicht, ?string $brandstof, ?string $provincie): array
    {
        $opcenten = ($provincie && isset(config('fiscaal.opcenten')[$provincie]))
            ? config('fiscaal.opcenten')[$provincie]
            : null;

        return [
            'ledig_gewicht' => $ledigGewicht ? number_format($ledigGewicht, 0, ',', '.') . ' kg' : null,
            'brandstof' => $brandstof,
            'provincie' => $provincie,
            'opcenten' => $opcenten !== null ? rtrim(rtrim(number_format($opcenten, 1, ',', ''), '0'), ',') . '%' : null,
            'rekenhulp' => config('fiscaal.mrb_rekenhulp_url'),
            'toelichting' => 'Het exacte bedrag hangt af van gewicht, brandstof en provincie. Bereken het via de officiele rekenhulp van de Belastingdienst.',
        ];
    }

    public function provincies(): array
    {
        return array_keys(config('fiscaal.opcenten'));
    }

    /**
     * Actual elapsed full years since first registration (not a calendar-year diff),
     * so the youngtimer boundary uses the same date logic as rest-BPM.
     */
    public function leeftijdJaren(?string $eersteToelatingRdw): ?int
    {
        if (!$eersteToelatingRdw) {
            return null;
        }
        $months = $this->monthsSince($eersteToelatingRdw);
        return $months === null ? null : intdiv($months, 12);
    }

    private function afschrijvingPercentage(int $months): float
    {
        foreach (config('fiscaal.bpm_afschrijving') as $bracket) {
            if ($months < $bracket['to']) {
                return (float) $bracket['pct'];
            }
        }
        return 81.0;
    }

    private function monthsSince(string $rdwDate): ?int
    {
        if (strlen($rdwDate) < 8) {
            return null;
        }
        try {
            $date = Carbon::createFromFormat('Ymd', substr($rdwDate, 0, 8))->startOfDay();
            if ($date->isFuture()) {
                return 0; // eerste toelating in de toekomst: geen afschrijving
            }
            // Bewust naar beneden afgerond op volle maanden (diffInMonths levert in Carbon 3 een float).
            return (int) floor($date->diffInMonths(now()));
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function euro(int $amount): string
    {
        return '€ ' . number_format($amount, 0, ',', '.');
    }
}
