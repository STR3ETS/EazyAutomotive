<?php

namespace App\Http\Controllers;

use App\Services\AI\CarCopyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarCopyController extends Controller
{
    public function __construct(private CarCopyService $service) {}

    /**
     * Generate sales copy (titel, beschrijving, opties) from the facts currently
     * on the add/edit form. Stateless on purpose: it uses the live form values
     * (including anything the dealer just typed) rather than a saved Car, so the
     * one endpoint serves both the create and the edit screens. Returns JSON; the
     * dealer reviews the result and presses save (which re-validates) to persist.
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'merk' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'bouwjaar' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'brandstof' => 'nullable|string|max:50',
            'transmissie' => 'nullable|string|max:50',
            'carrosserie' => 'nullable|string|max:50',
            'kleur' => 'nullable|string|max:50',
            'kilometerstand' => 'nullable|integer|min:0',
            'vermogen' => 'nullable|integer|min:0',
            'cilinderinhoud' => 'nullable|integer|min:0',
            'aantal_deuren' => 'nullable|integer|min:0',
            'aantal_zitplaatsen' => 'nullable|integer|min:0',
            'apk' => 'nullable|string|max:20',
            'prijs' => 'nullable|numeric|min:0',
            'opties' => 'nullable|string|max:2000',
            'accent' => 'nullable|string|max:1000',
        ]);

        if (empty(config('ai.api_key'))) {
            return response()->json([
                'error' => 'De AI is nog niet geconfigureerd (ANTHROPIC_API_KEY ontbreekt).',
            ], 422);
        }

        if (empty($validated['merk']) && empty($validated['model'])) {
            return response()->json([
                'error' => 'Zoek eerst een kenteken op of vul minimaal merk en model in.',
            ], 422);
        }

        $facts = $validated;
        $facts['bedrijf'] = $request->user()->company?->name;

        try {
            $result = $this->service->generate($facts);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'Genereren mislukt: ' . $e->getMessage()], 500);
        }

        if (trim($result['beschrijving']) === '' && trim($result['titel']) === '') {
            return response()->json([
                'error' => 'De AI kon geen tekst maken. Probeer het opnieuw of vul meer gegevens in.',
            ], 422);
        }

        return response()->json($result);
    }
}
