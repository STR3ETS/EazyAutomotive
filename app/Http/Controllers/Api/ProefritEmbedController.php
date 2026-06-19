<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProefritAanvraag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * Public endpoints for the embeddable proefrit (test-drive) widget. The company
 * is resolved from the api_key by the embed.api middleware ($request->embed_company).
 */
class ProefritEmbedController extends Controller
{
    /** Widget bootstrap: dealer name, theme color and the car list for the dropdown. */
    public function config(Request $request): JsonResponse
    {
        $company = $request->get('embed_company');
        if (!$company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $s = $company->embed_settings ?? [];

        return response()->json([
            'company' => [
                'name' => $company->name,
                'logo' => $company->logo_path ? asset('storage/' . $company->logo_path) : null,
            ],
            'primary_color' => $s['primary_color'] ?? '#0F9B9F',
            'font_family' => $s['font_family'] ?? null,
            'radius' => isset($s['card_border_radius']) ? (int) $s['card_border_radius'] : null,
            'proefrit' => [
                'titel' => $s['proefrit_titel'] ?? 'Plan een proefrit',
                'intro' => $s['proefrit_intro'] ?? null,
                'knop' => $s['proefrit_knop'] ?? 'Proefrit aanvragen',
                'toon_datum' => (bool) ($s['proefrit_toon_datum'] ?? true),
                'toon_bericht' => (bool) ($s['proefrit_toon_bericht'] ?? true),
                'auto_verplicht' => (bool) ($s['proefrit_auto_verplicht'] ?? false),
                'privacy' => (bool) ($s['proefrit_privacy'] ?? false),
                'privacy_tekst' => $s['proefrit_privacy_tekst'] ?? 'Ik ga akkoord met de verwerking van mijn gegevens.',
            ],
            'cars' => $company->cars()
                ->active()
                ->orderBy('merk')
                ->get()
                ->map(fn ($car) => [
                    'id' => $car->id,
                    'titel' => trim($car->display_title . ' - ' . $car->formatted_price),
                ])
                ->values(),
        ]);
    }

    /** Store a test-drive request as a lead and notify the dealer. */
    public function store(Request $request): JsonResponse
    {
        $company = $request->get('embed_company');
        if (!$company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $settings = $company->embed_settings ?? [];

        // Honeypot: silently accept bots without storing.
        if ($request->filled('website')) {
            return response()->json(['ok' => true, 'message' => 'Bedankt voor je aanvraag.']);
        }

        // Enforce the privacy consent server-side when the dealer enabled it.
        if (($settings['proefrit_privacy'] ?? false) && !$request->boolean('privacy')) {
            return response()->json(['ok' => false, 'message' => 'Ga akkoord met de privacyvoorwaarden om door te gaan.'], 422);
        }

        $validated = $request->validate([
            'naam' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'telefoon' => 'required|string|max:30',
            'car_id' => 'nullable|integer',
            'gewenste_datum' => 'nullable|date|after_or_equal:today',
            'bericht' => 'nullable|string|max:1000',
        ]);

        // Only accept a car that belongs to this company.
        $carId = !empty($validated['car_id'])
            ? $company->cars()->where('id', $validated['car_id'])->value('id')
            : null;

        if (($settings['proefrit_auto_verplicht'] ?? false) && !$carId) {
            return response()->json(['ok' => false, 'message' => 'Kies een auto voor de proefrit.'], 422);
        }

        $aanvraag = ProefritAanvraag::create([
            'company_id' => $company->id,
            'car_id' => $carId,
            'naam' => $validated['naam'],
            'email' => $validated['email'],
            'telefoon' => $validated['telefoon'],
            'gewenste_datum' => $validated['gewenste_datum'] ?? null,
            'bericht' => $validated['bericht'] ?? null,
            'status' => 'nieuw',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->notifyDealer($company, $aanvraag);

        return response()->json([
            'ok' => true,
            'message' => $settings['proefrit_bedankt'] ?? 'Bedankt! Je proefrit-aanvraag is verstuurd. We nemen snel contact met je op.',
        ]);
    }

    private function notifyDealer($company, ProefritAanvraag $aanvraag): void
    {
        if (!$company->email) {
            return;
        }

        $auto = $aanvraag->car_id ? optional($aanvraag->car)->display_title : 'Algemeen (geen specifieke auto)';
        $datum = $aanvraag->gewenste_datum ? $aanvraag->gewenste_datum->format('d-m-Y') : 'Geen voorkeur';

        $body = "Nieuwe proefrit-aanvraag\n\n"
            . "Naam: {$aanvraag->naam}\n"
            . "E-mail: {$aanvraag->email}\n"
            . "Telefoon: {$aanvraag->telefoon}\n"
            . "Auto: {$auto}\n"
            . "Gewenste datum: {$datum}\n"
            . "Bericht: " . ($aanvraag->bericht ?: '-') . "\n";

        try {
            Mail::raw($body, function ($mail) use ($company) {
                $mail->to($company->email)->subject('Nieuwe proefrit-aanvraag via je website');
            });
        } catch (\Throwable $e) {
            // Non-blocking: the lead is already saved.
        }
    }
}
