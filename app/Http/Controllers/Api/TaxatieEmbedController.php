<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RdwService;
use App\Services\Valuation\ValuationEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Publieke inruil-taxatiewidget. Een bezoeker vult op de dealersite een kenteken
 * (en kilometerstand) in en krijgt direct een inruilindicatie op basis van de
 * live marktdata, en wordt daarna als inruil-lead in de CRM van de dealer gezet.
 *
 * De marktwaarde uit de ValuationEngine is een VRAAGprijs (retail). Voor een
 * inruilindicatie wordt die met een factor (config valuation.inruil_factor)
 * omlaag gebracht, want een dealer koopt onder de verkoopprijs in.
 */
class TaxatieEmbedController extends Controller
{
    public function __construct(private RdwService $rdw, private ValuationEngine $engine) {}

    /** Widget bootstrap: naam + thema. */
    public function config(Request $request): JsonResponse
    {
        $company = $request->get('embed_company');
        if (! $company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $s = $company->embed_settings ?? [];

        return response()->json([
            'company' => ['name' => $company->name],
            'primary_color' => $s['primary_color'] ?? '#0F9B9F',
            'font_family' => $s['font_family'] ?? null,
            'radius' => isset($s['card_border_radius']) ? (int) $s['card_border_radius'] : null,
        ]);
    }

    /** Kenteken (+ km) -> voertuig herkennen en inruilindicatie berekenen. */
    public function estimate(Request $request): JsonResponse
    {
        $company = $request->get('embed_company');
        if (! $company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'kenteken' => 'required|string|max:12',
            'kilometerstand' => 'nullable|integer|min:0|max:2000000',
        ]);

        $result = $this->taxeer($data['kenteken'], $data['kilometerstand'] ?? null);
        if (! $result) {
            return response()->json([
                'ok' => false,
                'message' => 'We konden dit kenteken niet vinden bij de RDW. Controleer of het klopt.',
            ]);
        }

        return response()->json(['ok' => true] + $result);
    }

    /** Contactgegevens + de auto -> inruil-lead in de CRM. */
    public function lead(Request $request): JsonResponse
    {
        $company = $request->get('embed_company');
        if (! $company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Honeypot.
        if ($request->filled('website')) {
            return response()->json(['ok' => true, 'message' => 'Bedankt!']);
        }

        $data = $request->validate([
            'kenteken' => 'required|string|max:12',
            'kilometerstand' => 'nullable|integer|min:0|max:2000000',
            'naam' => 'required|string|max:150',
            'email' => 'nullable|email|max:190',
            'telefoon' => 'nullable|string|max:40',
            'bericht' => 'nullable|string|max:2000',
        ]);

        if (empty($data['email']) && empty($data['telefoon'])) {
            return response()->json(['ok' => false, 'message' => 'Vul een e-mail of telefoonnummer in.'], 422);
        }

        // Server-side opnieuw berekenen, zodat de opgeslagen taxatie betrouwbaar is
        // en niet door de client gemanipuleerd kan worden.
        $result = $this->taxeer($data['kenteken'], $data['kilometerstand'] ?? null);
        $kenteken = $this->rdw->normalizeKenteken($data['kenteken']);

        $company->leads()->create([
            'type' => 'inruil',
            'naam' => $data['naam'],
            'email' => $data['email'] ?? null,
            'telefoon' => $data['telefoon'] ?? null,
            'bericht' => $data['bericht'] ?? null,
            'status' => 'nieuw',
            'source' => 'taxatie-widget',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data' => [
                'inruil' => [
                    'kenteken' => $kenteken,
                    'kilometerstand' => $data['kilometerstand'] ?? null,
                    'voertuig' => $result['voertuig'] ?? null,
                    'taxatie' => $result['taxatie'] ?? null,
                ],
            ],
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Bedankt! We nemen snel contact met je op over je inruil.',
        ]);
    }

    /**
     * RDW-lookup + waardebepaling -> voertuig + inruilindicatie.
     *
     * @return array{voertuig: array, taxatie: array}|null
     */
    private function taxeer(string $kenteken, ?int $km): ?array
    {
        $rdw = $this->rdw->fetchByKenteken($kenteken);
        if (! $rdw) {
            return null;
        }

        $attrs = $this->rdw->mapToCarAttributes($rdw);

        $est = $this->engine->estimate([
            'merk' => $attrs['merk'] ?? null,
            'model' => $attrs['handelsbenaming'] ?? null,
            'bouwjaar' => $attrs['bouwjaar'] ?? null,
            'brandstof' => $attrs['brandstof_omschrijving'] ?? null,
            'catalogusprijs' => $attrs['catalogusprijs'] ?? null,
            'cilinderinhoud' => $attrs['cilinderinhoud'] ?? null,
        ], $km);

        $voertuig = [
            'kenteken' => $attrs['kenteken'] ?? $this->rdw->normalizeKenteken($kenteken),
            'merk' => $attrs['merk'] ?? null,
            'model' => RdwService::friendlyModel($attrs['handelsbenaming'] ?? '') ?: null,
            'bouwjaar' => $attrs['bouwjaar'] ?? null,
            'brandstof' => $attrs['brandstof_omschrijving'] ?? null,
        ];

        $taxatie = ['beschikbaar' => false];
        if ($est['beschikbaar'] ?? false) {
            $factor = (float) config('valuation.inruil_factor', 0.85);
            $mid = (int) $est['midden'];
            $taxatie = [
                'beschikbaar' => true,
                'onder' => $this->rond($mid * ($factor - 0.06)),
                'midden' => $this->rond($mid * $factor),
                'boven' => $this->rond($mid * ($factor + 0.04)),
                'vertrouwen' => $est['vertrouwen'] ?? 'laag',
                'bron' => $est['bron'] ?? null,
            ];
        }

        return ['voertuig' => $voertuig, 'taxatie' => $taxatie];
    }

    private function rond(float $euro): int
    {
        return (int) max(0, round($euro / 100) * 100);
    }
}
