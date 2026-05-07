<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RdwService
{
    private const BASE_URL = 'https://opendata.rdw.nl/resource';
    private const VOERTUIGEN_ENDPOINT = '/m9d7-ebf2.json';
    private const BRANDSTOF_ENDPOINT = '/8ys7-d773.json';

    /**
     * Fetch vehicle data by kenteken.
     * Returns merged data from voertuigen + brandstof endpoints.
     */
    public function fetchByKenteken(string $kenteken): ?array
    {
        $kenteken = $this->normalizeKenteken($kenteken);

        return Cache::remember("rdw_{$kenteken}", 86400, function () use ($kenteken) {
            $voertuig = $this->fetchVoertuig($kenteken);

            if (!$voertuig) {
                return null;
            }

            $brandstof = $this->fetchBrandstof($kenteken);
            $voertuig['brandstof_omschrijving'] = $brandstof['brandstof_omschrijving'] ?? null;
            $voertuig['uitlaatemissieniveau'] = $brandstof['emissiecode_omschrijving'] ?? null;

            return $voertuig;
        });
    }

    private function fetchVoertuig(string $kenteken): ?array
    {
        $response = Http::timeout(10)->get(self::BASE_URL . self::VOERTUIGEN_ENDPOINT, [
            'kenteken' => $kenteken,
        ]);

        if ($response->failed() || empty($response->json())) {
            return null;
        }

        return $response->json()[0];
    }

    private function fetchBrandstof(string $kenteken): ?array
    {
        $response = Http::timeout(10)->get(self::BASE_URL . self::BRANDSTOF_ENDPOINT, [
            'kenteken' => $kenteken,
        ]);

        if ($response->failed() || empty($response->json())) {
            return null;
        }

        return $response->json()[0];
    }

    /**
     * Normalize kenteken: remove dashes/spaces, uppercase.
     */
    public function normalizeKenteken(string $kenteken): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $kenteken));
    }

    /**
     * Map raw RDW data to our Car model fields.
     */
    public function mapToCarAttributes(array $rdwData): array
    {
        return [
            'kenteken' => $rdwData['kenteken'] ?? null,
            'merk' => $rdwData['merk'] ?? null,
            'handelsbenaming' => $rdwData['handelsbenaming'] ?? null,
            'voertuigsoort' => $rdwData['voertuigsoort'] ?? null,
            'inrichting' => $rdwData['inrichting'] ?? null,
            'eerste_kleur' => $rdwData['eerste_kleur'] ?? null,
            'tweede_kleur' => $rdwData['tweede_kleur'] ?? null,
            'aantal_cilinders' => isset($rdwData['aantal_cilinders']) ? (int) $rdwData['aantal_cilinders'] : null,
            'cilinderinhoud' => isset($rdwData['cilinderinhoud']) ? (int) $rdwData['cilinderinhoud'] : null,
            'massa_rijklaar' => isset($rdwData['massa_rijklaar']) ? (int) $rdwData['massa_rijklaar'] : null,
            'massa_ledig_voertuig' => isset($rdwData['massa_ledig_voertuig']) ? (int) $rdwData['massa_ledig_voertuig'] : null,
            'toegestane_maximum_massa_voertuig' => isset($rdwData['toegestane_maximum_massa_voertuig']) ? (int) $rdwData['toegestane_maximum_massa_voertuig'] : null,
            'aantal_zitplaatsen' => isset($rdwData['aantal_zitplaatsen']) ? (int) $rdwData['aantal_zitplaatsen'] : null,
            'aantal_deuren' => isset($rdwData['aantal_deuren']) ? (int) $rdwData['aantal_deuren'] : null,
            'aantal_wielen' => isset($rdwData['aantal_wielen']) ? (int) $rdwData['aantal_wielen'] : null,
            'wielbasis' => isset($rdwData['wielbasis']) ? (int) $rdwData['wielbasis'] : null,
            'datum_eerste_toelating' => $this->parseRdwDate($rdwData['datum_eerste_toelating'] ?? null),
            'datum_eerste_tenaamstelling_in_nederland' => $this->parseRdwDate($rdwData['datum_eerste_tenaamstelling_in_nederland'] ?? null),
            'vervaldatum_apk' => $this->parseRdwDate($rdwData['vervaldatum_apk'] ?? null),
            'catalogusprijs' => isset($rdwData['catalogusprijs']) ? (int) $rdwData['catalogusprijs'] : null,
            'europese_voertuigcategorie' => $rdwData['europese_voertuigcategorie'] ?? null,
            'typegoedkeuringsnummer' => $rdwData['typegoedkeuringsnummer'] ?? null,
            'zuinigheidsclassificatie' => $rdwData['zuinigheidsclassificatie'] ?? null,
            'bruto_bpm' => isset($rdwData['bruto_bpm']) ? (int) $rdwData['bruto_bpm'] : null,
            'brandstof_omschrijving' => $rdwData['brandstof_omschrijving'] ?? null,
            'uitlaatemissieniveau' => $rdwData['uitlaatemissieniveau'] ?? null,
            'wam_verzekerd' => ($rdwData['wam_verzekerd'] ?? '') === 'Ja',
            'export_indicator' => ($rdwData['export_indicator'] ?? '') === 'Ja',
            'tellerstandoordeel' => $rdwData['tellerstandoordeel'] ?? null,
            'rdw_raw_data' => $rdwData,
            'bouwjaar' => $this->extractYear($rdwData['datum_eerste_toelating'] ?? null),
        ];
    }

    private function parseRdwDate(?string $dateStr): ?string
    {
        if (!$dateStr || strlen($dateStr) < 8) {
            return null;
        }

        // RDW format: YYYYMMDD
        return substr($dateStr, 0, 4) . '-' . substr($dateStr, 4, 2) . '-' . substr($dateStr, 6, 2);
    }

    private function extractYear(?string $dateStr): ?int
    {
        if (!$dateStr || strlen($dateStr) < 4) {
            return null;
        }

        return (int) substr($dateStr, 0, 4);
    }
}
