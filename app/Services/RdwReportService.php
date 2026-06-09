<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Builds a Finnik-style vehicle research report from the free RDW open data:
 * core data, engine, dimensions and weights, environment, fiscal info,
 * status (incl. open recall flag), APK defect history and an indicative value
 * estimate. No paid sources; the value estimate is clearly an indication and
 * a NAP/taxatie source can be plugged in later for an exact figure.
 */
class RdwReportService
{
    private const BASE_URL = 'https://opendata.rdw.nl/resource';
    private const BRANDSTOF = '/8ys7-d773.json';
    private const ASSEN = '/3huj-srit.json';
    private const GEBREKEN_GECONSTATEERD = '/a34c-vvps.json';
    private const GEBREKEN_OMSCHRIJVING = '/hx2c-gt7k.json';

    public function __construct(private RdwService $rdw) {}

    public function generate(string $kenteken, ?int $km = null): ?array
    {
        $plate = $this->rdw->normalizeKenteken($kenteken);

        $voertuig = $this->rdw->fetchByKenteken($plate);
        if (!$voertuig) {
            return null;
        }

        $brandstof = $this->fetchBrandstof($plate);
        $assen = $this->fetchAssen($plate);
        $defects = $this->fetchDefectHistory($plate);

        return [
            'kenteken' => strtoupper($plate),
            'kenteken_raw' => $plate,
            'opgehaald_op' => now()->format('d-m-Y H:i'),
            'titel' => trim(($voertuig['merk'] ?? '') . ' ' . ($voertuig['handelsbenaming'] ?? '')) ?: 'Onbekend voertuig',
            'kerngegevens' => $this->coreData($voertuig),
            'motor' => $this->engine($voertuig, $brandstof),
            'afmetingen' => $this->dimensions($voertuig, $assen),
            'milieu' => $this->environment($voertuig, $brandstof),
            'fiscaal' => $this->fiscal($voertuig),
            'status' => $this->status($voertuig),
            'gebreken' => $defects,
            'waarde' => $this->valuation($voertuig, $km),
            'aandachtspunten' => $this->attentionPoints($voertuig, $defects),
        ];
    }

    private function coreData(array $v): array
    {
        return $this->clean([
            'Merk' => $v['merk'] ?? null,
            'Model' => $v['handelsbenaming'] ?? null,
            'Voertuigsoort' => $v['voertuigsoort'] ?? null,
            'Carrosserie' => $v['inrichting'] ?? null,
            'Voertuigcategorie' => $v['europese_voertuigcategorie'] ?? null,
            'Kleur' => $this->colour($v),
            'Bouwjaar' => $this->year($v['datum_eerste_toelating'] ?? null),
            'Eerste toelating' => $this->date($v['datum_eerste_toelating'] ?? null),
            'Eerste tenaamstelling NL' => $this->date($v['datum_eerste_tenaamstelling_in_nederland'] ?? null),
            'Laatste tenaamstelling' => $this->date($v['datum_tenaamstelling'] ?? null),
            'Aantal zitplaatsen' => $v['aantal_zitplaatsen'] ?? null,
            'Aantal deuren' => $v['aantal_deuren'] ?? null,
        ]);
    }

    private function engine(array $v, ?array $b): array
    {
        return $this->clean([
            'Brandstof' => $v['brandstof_omschrijving'] ?? ($b['brandstof_omschrijving'] ?? null),
            'Cilinderinhoud' => isset($v['cilinderinhoud']) ? number_format((int) $v['cilinderinhoud'], 0, ',', '.') . ' cc' : null,
            'Aantal cilinders' => $v['aantal_cilinders'] ?? null,
            'Vermogen' => isset($b['nettomaximumvermogen']) ? $this->power($b['nettomaximumvermogen']) : null,
            'Vermogen / massa' => isset($v['vermogen_massarijklaar']) && (float) $v['vermogen_massarijklaar'] > 0
                ? str_replace('.', ',', (string) $v['vermogen_massarijklaar']) . ' kW/kg' : null,
        ]);
    }

    private function dimensions(array $v, ?array $assen): array
    {
        return $this->clean([
            'Lengte' => isset($v['lengte']) ? number_format((int) $v['lengte'], 0, ',', '.') . ' cm' : null,
            'Wielbasis' => isset($v['wielbasis']) ? $v['wielbasis'] . ' cm' : null,
            'Massa ledig' => $this->kg($v['massa_ledig_voertuig'] ?? null),
            'Massa rijklaar' => $this->kg($v['massa_rijklaar'] ?? null),
            'Technische max. massa' => $this->kg($v['technische_max_massa_voertuig'] ?? null),
            'Toegestane max. massa' => $this->kg($v['toegestane_maximum_massa_voertuig'] ?? null),
            'Trekgewicht geremd' => $this->kg($v['maximum_trekken_massa_geremd'] ?? null),
            'Trekgewicht ongeremd' => $this->kg($v['maximum_massa_trekken_ongeremd'] ?? null),
            'Max. massa samenstelling' => $this->kg($v['maximum_massa_samenstelling'] ?? null),
            'Aantal assen' => $assen['aantal_assen'] ?? null,
            'Aantal wielen' => $v['aantal_wielen'] ?? null,
        ]);
    }

    private function environment(array $v, ?array $b): array
    {
        return $this->clean([
            'Brandstof' => $v['brandstof_omschrijving'] ?? ($b['brandstof_omschrijving'] ?? null),
            'CO2-uitstoot (gecombineerd)' => isset($b['co2_uitstoot_gecombineerd']) ? $b['co2_uitstoot_gecombineerd'] . ' g/km' : null,
            'Verbruik (gecombineerd)' => isset($b['brandstofverbruik_gecombineerd']) ? str_replace('.', ',', $b['brandstofverbruik_gecombineerd']) . ' l/100km' : null,
            'Energielabel' => $v['zuinigheidsclassificatie'] ?? null,
            'Emissieklasse' => $v['uitlaatemissieniveau'] ?? ($b['uitlaatemissieniveau'] ?? ($b['emissiecode_omschrijving'] ?? null)),
            'Milieuklasse EG' => $b['milieuklasse_eg_goedkeuring_licht'] ?? null,
            'Geluid stationair' => isset($b['geluidsniveau_stationair']) ? $b['geluidsniveau_stationair'] . ' dB' : null,
            'Geluid rijdend' => isset($b['geluidsniveau_rijdend']) ? $b['geluidsniveau_rijdend'] . ' dB' : null,
        ]);
    }

    private function fiscal(array $v): array
    {
        return $this->clean([
            'Catalogusprijs (nieuw)' => isset($v['catalogusprijs']) && (int) $v['catalogusprijs'] > 0 ? $this->euro((int) $v['catalogusprijs']) : null,
            'Bruto BPM' => isset($v['bruto_bpm']) ? $this->euro((int) $v['bruto_bpm']) : null,
        ]);
    }

    private function status(array $v): array
    {
        $rows = [
            'APK geldig tot' => [
                'value' => $this->date($v['vervaldatum_apk'] ?? null) ?: 'Onbekend',
                'ok' => $this->isFutureDate($v['vervaldatum_apk'] ?? null),
            ],
            'Tellerstandoordeel' => [
                'value' => $v['tellerstandoordeel'] ?? 'Onbekend',
                'ok' => ($v['tellerstandoordeel'] ?? '') === 'Logisch',
            ],
            'WAM-verzekerd' => [
                'value' => ($v['wam_verzekerd'] ?? '') === 'Ja' ? 'Ja' : 'Nee',
                'ok' => ($v['wam_verzekerd'] ?? '') === 'Ja',
            ],
            'Openstaande terugroepactie' => [
                'value' => ($v['openstaande_terugroepactie_indicator'] ?? '') === 'Ja' ? 'Ja' : 'Nee',
                'ok' => ($v['openstaande_terugroepactie_indicator'] ?? '') !== 'Ja',
            ],
            'Export-indicator' => [
                'value' => ($v['export_indicator'] ?? '') === 'Ja' ? 'Ja' : 'Nee',
                'ok' => ($v['export_indicator'] ?? '') !== 'Ja',
            ],
            'Tenaamstellen mogelijk' => [
                'value' => ($v['tenaamstellen_mogelijk'] ?? '') === 'Ja' ? 'Ja' : 'Nee',
                'ok' => ($v['tenaamstellen_mogelijk'] ?? '') === 'Ja',
            ],
        ];

        if (($v['taxi_indicator'] ?? '') === 'Ja') {
            $rows['Taxi-indicator'] = ['value' => 'Ja', 'ok' => false];
        }

        return $rows;
    }

    private function fetchDefectHistory(string $plate): array
    {
        return Cache::remember("rdw_report_defects_{$plate}", 86400, function () use ($plate) {
            $rows = $this->get(self::GEBREKEN_GECONSTATEERD, [
                'kenteken' => $plate,
                '$order' => 'meld_datum_door_keuringsinstantie_dt DESC',
                '$limit' => 200,
            ]);

            if (empty($rows)) {
                return [];
            }

            $codes = collect($rows)->pluck('gebrek_identificatie')->filter()->unique()->values();
            $descriptions = $this->fetchDescriptions($codes->all());

            return collect($rows)
                ->groupBy('meld_datum_door_keuringsinstantie')
                ->map(fn ($group, $date) => [
                    'datum' => $this->date($date),
                    'items' => collect($group)->map(fn ($row) => [
                        'omschrijving' => $descriptions[$row['gebrek_identificatie'] ?? ''] ?? ('Gebrek ' . ($row['gebrek_identificatie'] ?? '')),
                        'aantal' => (int) ($row['aantal_gebreken_geconstateerd'] ?? 1),
                    ])->values()->all(),
                ])
                ->sortByDesc('datum')
                ->values()
                ->all();
        });
    }

    private function fetchDescriptions(array $codes): array
    {
        if (empty($codes)) {
            return [];
        }

        $quoted = collect($codes)->map(fn ($c) => "'" . str_replace("'", '', $c) . "'")->implode(',');

        $rows = $this->get(self::GEBREKEN_OMSCHRIJVING, [
            '$where' => "gebrek_identificatie in ({$quoted})",
            '$limit' => 500,
        ]);

        return collect($rows)->mapWithKeys(fn ($r) => [$r['gebrek_identificatie'] => $r['gebrek_omschrijving'] ?? ''])->all();
    }

    /**
     * Indicative value estimate. NOT an official appraisal. Falls back to a
     * rough age-based curve when the RDW has no catalogue price (older cars).
     */
    private function valuation(array $v, ?int $km): array
    {
        $year = $this->year($v['datum_eerste_toelating'] ?? null);
        if (!$year) {
            return ['beschikbaar' => false, 'toelichting' => 'Onvoldoende gegevens (geen bouwjaar) voor een indicatie.'];
        }

        $age = max(0, now()->year - (int) $year);
        $catalogus = isset($v['catalogusprijs']) ? (int) $v['catalogusprijs'] : 0;
        $rough = $catalogus <= 0;
        $base = $rough ? 30000 : $catalogus;

        // Depreciation: steep early, flatter later.
        $value = $base * (0.82 ** min($age, 4)) * (0.90 ** max(0, $age - 4));

        // Mileage adjustment (only penalise meaningfully; capped both ways).
        if ($km !== null && $km > 0) {
            $expected = max(10000, $age * 13000);
            $ratio = $km / $expected;
            $factor = max(0.55, min(1.15, 1.15 - 0.30 * $ratio));
            $value *= $factor;
        }

        $value = max(500, $value);
        $step = $value < 5000 ? 100 : 250;
        $mid = (int) (round($value / $step) * $step);

        return [
            'beschikbaar' => true,
            'ruw' => $rough,
            'midden' => $this->euro($mid),
            'onder' => $this->euro((int) (round($mid * 0.88 / $step) * $step)),
            'boven' => $this->euro((int) (round($mid * 1.12 / $step) * $step)),
            'km_gebruikt' => $km,
            'toelichting' => $rough
                ? 'Ruwe indicatie: de RDW heeft geen catalogusprijs voor dit (oudere) voertuig. Voor een nauwkeurige waarde is een taxatie- of marktbron nodig.'
                : 'Indicatieve schatting op basis van nieuwprijs, leeftijd' . ($km ? ' en kilometerstand' : '') . '. Geen officiële taxatie.',
        ];
    }

    private function attentionPoints(array $v, array $defects): array
    {
        $points = [];

        if (!$this->isFutureDate($v['vervaldatum_apk'] ?? null)) {
            $points[] = ['type' => 'warn', 'tekst' => 'De APK is verlopen of de vervaldatum is onbekend.'];
        } else {
            $points[] = ['type' => 'ok', 'tekst' => 'APK is geldig tot ' . $this->date($v['vervaldatum_apk']) . '.'];
        }

        if (($v['openstaande_terugroepactie_indicator'] ?? '') === 'Ja') {
            $points[] = ['type' => 'warn', 'tekst' => 'Er staat een terugroepactie open. Laat dit controleren bij een merkdealer.'];
        } else {
            $points[] = ['type' => 'ok', 'tekst' => 'Geen openstaande terugroepactie bekend.'];
        }

        $teller = $v['tellerstandoordeel'] ?? '';
        if ($teller === 'Logisch') {
            $points[] = ['type' => 'ok', 'tekst' => 'Tellerstand is logisch volgens de RDW.'];
        } elseif ($teller !== '') {
            $points[] = ['type' => 'warn', 'tekst' => 'Let op: tellerstandoordeel is "' . $teller . '".'];
        }

        if (($v['wam_verzekerd'] ?? '') !== 'Ja') {
            $points[] = ['type' => 'warn', 'tekst' => 'Voertuig staat niet als WAM-verzekerd geregistreerd.'];
        }

        if (($v['export_indicator'] ?? '') === 'Ja') {
            $points[] = ['type' => 'warn', 'tekst' => 'Voertuig is geregistreerd voor export.'];
        }

        $totalDefects = collect($defects)->sum(fn ($visit) => collect($visit['items'])->sum('aantal'));
        if ($totalDefects > 0) {
            $points[] = ['type' => 'warn', 'tekst' => $totalDefects . ' geconstateerde gebreken in de APK-historie.'];
        } else {
            $points[] = ['type' => 'ok', 'tekst' => 'Geen geconstateerde gebreken in de APK-historie.'];
        }

        return $points;
    }

    // ---- fetch helpers ----

    private function fetchBrandstof(string $plate): ?array
    {
        return Cache::remember("rdw_report_brandstof_{$plate}", 86400, fn () => $this->get(self::BRANDSTOF, ['kenteken' => $plate])[0] ?? null);
    }

    private function fetchAssen(string $plate): ?array
    {
        return Cache::remember("rdw_report_assen_{$plate}", 86400, fn () => $this->get(self::ASSEN, ['kenteken' => $plate])[0] ?? null);
    }

    private function get(string $endpoint, array $query): array
    {
        try {
            $response = Http::timeout(12)->get(self::BASE_URL . $endpoint, $query);
            return $response->successful() ? $response->json() : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    // ---- formatting helpers ----

    private function clean(array $rows): array
    {
        return array_filter($rows, fn ($x) => $x !== null && $x !== '');
    }

    private function euro(int $amount): string
    {
        return '€ ' . number_format($amount, 0, ',', '.');
    }

    private function kg($value): ?string
    {
        return ($value !== null && $value !== '') ? number_format((int) $value, 0, ',', '.') . ' kg' : null;
    }

    private function colour(array $v): ?string
    {
        $first = $v['eerste_kleur'] ?? null;
        $second = $v['tweede_kleur'] ?? null;
        if ($first && $second && strtolower($second) !== 'niet geregistreerd') {
            return "{$first} / {$second}";
        }
        return $first;
    }

    private function power(?string $kw): ?string
    {
        if (!$kw) {
            return null;
        }
        $kwVal = (float) str_replace(',', '.', $kw);
        if ($kwVal <= 0) {
            return null;
        }
        $pk = round($kwVal * 1.36);
        return rtrim(rtrim(number_format($kwVal, 1, ',', ''), '0'), ',') . ' kW (' . $pk . ' pk)';
    }

    private function date(?string $rdwDate): ?string
    {
        if (!$rdwDate || strlen($rdwDate) < 8) {
            return null;
        }
        return substr($rdwDate, 6, 2) . '-' . substr($rdwDate, 4, 2) . '-' . substr($rdwDate, 0, 4);
    }

    private function year(?string $rdwDate): ?string
    {
        return ($rdwDate && strlen($rdwDate) >= 4) ? substr($rdwDate, 0, 4) : null;
    }

    private function isFutureDate(?string $rdwDate): bool
    {
        if (!$rdwDate || strlen($rdwDate) < 8) {
            return false;
        }
        try {
            return Carbon::createFromFormat('Ymd', substr($rdwDate, 0, 8))->endOfDay()->isFuture();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
