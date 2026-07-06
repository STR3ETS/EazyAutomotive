<?php

namespace App\Services\Valuation\Sources;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Pulls market listings from a live JSON feed (a licensed feed or partner
 * endpoint). The feed must return a top-level array, or {"data": [...]} /
 * {"listings": [...]}, of objects with merk/model/bouwjaar/brandstof/
 * kilometerstand/prijs (euros)/id. Prices are normalized to cents.
 */
class HttpMarketSource implements MarketSource
{
    public function __construct(private string $url, private ?string $token = null) {}

    public function name(): string
    {
        return 'http';
    }

    public function listings(): iterable
    {
        $request = Http::acceptJson()->timeout(60);
        if ($this->token) {
            $request = $request->withToken($this->token);
        }

        $response = $request->get($this->url);
        if (! $response->successful()) {
            throw new RuntimeException("Marktfeed gaf HTTP {$response->status()}.");
        }

        $data = $response->json();
        $rows = $data['data'] ?? $data['listings'] ?? (is_array($data) ? $data : []);

        foreach ($rows as $r) {
            if (! is_array($r)) {
                continue;
            }
            $merk = $r['merk'] ?? $r['make'] ?? null;
            $prijs = $r['prijs'] ?? $r['price'] ?? null;
            if (! $merk || $prijs === null || $prijs === '') {
                continue;
            }

            $prijsCents = $this->toCents($prijs);
            if ($prijsCents <= 0) {
                continue;
            }

            yield [
                'merk' => strtoupper(trim((string) $merk)),
                'model' => ($r['model'] ?? null) ?: null,
                'bouwjaar' => isset($r['bouwjaar']) ? (int) $r['bouwjaar'] : (isset($r['year']) ? (int) $r['year'] : null),
                'brandstof' => ($r['brandstof'] ?? $r['fuel'] ?? null) ?: null,
                'kilometerstand' => isset($r['kilometerstand']) ? (int) $r['kilometerstand'] : (isset($r['mileage']) ? (int) $r['mileage'] : null),
                'prijs' => $prijsCents,
                'external_id' => (string) ($r['id'] ?? $r['external_id'] ?? substr(md5(json_encode($r)), 0, 20)),
            ];
        }
    }

    private function toCents(mixed $prijs): int
    {
        if (is_numeric($prijs)) {
            return (int) round(((float) $prijs) * 100);
        }

        $clean = preg_replace('/[^0-9,.]/', '', (string) $prijs);

        return (int) round(((float) str_replace(['.', ','], ['', '.'], (string) $clean)) * 100);
    }
}
