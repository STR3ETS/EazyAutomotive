<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public lead intake for the embeddable contact / interest form. The company is
 * resolved from the api_key by the embed.api middleware ($request->embed_company).
 * Every submission lands in the dealer's CRM.
 */
class LeadEmbedController extends Controller
{
    /** Widget bootstrap: dealer name, theme color/font and the car list. */
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
            'card_shadow' => $s['card_shadow'] ?? 'none',
            'card_bg_color' => $s['card_bg_color'] ?? '#ffffff',
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

    public function store(Request $request): JsonResponse
    {
        $company = $request->get('embed_company');
        if (! $company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Honeypot: silently accept bots without storing.
        if ($request->filled('website')) {
            return response()->json(['ok' => true, 'message' => 'Bedankt voor je bericht.']);
        }

        $validated = $request->validate([
            'naam' => 'required|string|max:150',
            'email' => 'nullable|email|max:190',
            'telefoon' => 'nullable|string|max:40',
            'bericht' => 'nullable|string|max:2000',
            'type' => 'nullable|in:contact,inruil,financiering,overig',
            'car_id' => 'nullable|integer',
        ]);

        if (empty($validated['email']) && empty($validated['telefoon'])) {
            return response()->json(['ok' => false, 'message' => 'Vul een e-mail of telefoonnummer in.'], 422);
        }

        $carId = ! empty($validated['car_id'])
            ? $company->cars()->where('id', $validated['car_id'])->value('id')
            : null;

        $company->leads()->create([
            'car_id' => $carId,
            'type' => $validated['type'] ?? 'contact',
            'naam' => $validated['naam'],
            'email' => $validated['email'] ?? null,
            'telefoon' => $validated['telefoon'] ?? null,
            'bericht' => $validated['bericht'] ?? null,
            'status' => 'nieuw',
            'source' => 'widget',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['ok' => true, 'message' => 'Bedankt! We nemen snel contact met je op.']);
    }
}
