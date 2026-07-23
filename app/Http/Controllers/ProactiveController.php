<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Services\AI\CarCopyService;
use App\Services\AI\SuggestionEngine;
use Illuminate\Http\Request;

/**
 * Voert de proactieve suggesties van de AI-collega uit (of verbergt ze). Altijd
 * strikt binnen het eigen bedrijf.
 */
class ProactiveController extends Controller
{
    public function __construct(private SuggestionEngine $engine) {}

    /** Elke uitvoerbare suggestie hoort bij een gebied (rol-grens). */
    private const ACTION_AREAS = [
        'activeer_concepten' => 'voorraad',
        'schrijf_teksten' => 'voorraad',
    ];

    public function act(Request $request)
    {
        $data = $request->validate(['key' => 'required|string|max:50']);
        $user = $request->user();
        $companyId = $user->company_id;

        // Rol-grens: alleen uitvoeren als de gebruiker bij dit gebied mag.
        $area = self::ACTION_AREAS[$data['key']] ?? null;
        if ($area !== null && ! $user->hasArea($area)) {
            return response()->json(['error' => 'Je rol heeft geen toegang tot deze actie.'], 403);
        }

        $message = match ($data['key']) {
            'activeer_concepten' => $this->activateDrafts($companyId),
            'schrijf_teksten' => $this->writeDescriptions($companyId),
            default => null,
        };

        if ($message === null) {
            return response()->json(['error' => 'Deze suggestie kan ik niet automatisch uitvoeren.'], 422);
        }

        return response()->json(['ok' => true, 'message' => $message]);
    }

    public function dismiss(Request $request)
    {
        $data = $request->validate(['key' => 'required|string|max:50']);
        $this->engine->dismiss($request->user()->id, $data['key']);

        return response()->json(['ok' => true]);
    }

    private function activateDrafts(int $companyId): string
    {
        $count = Car::where('company_id', $companyId)->where('status', 'draft')->update(['status' => 'active']);

        return $count > 0
            ? "{$count} auto's geactiveerd en online gezet."
            : 'Er stonden geen concept-auto\'s meer.';
    }

    private function writeDescriptions(int $companyId): string
    {
        @set_time_limit(300);

        $cars = Car::where('company_id', $companyId)
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('beschrijving')->orWhere('beschrijving', ''))
            ->with('company')
            ->take(10)
            ->get();

        $copy = app(CarCopyService::class);
        $done = 0;

        foreach ($cars as $car) {
            try {
                $facts = array_filter([
                    'merk' => $car->merk,
                    'model' => $car->handelsbenaming,
                    'bouwjaar' => $car->bouwjaar,
                    'brandstof' => $car->brandstof_omschrijving,
                    'kleur' => $car->eerste_kleur,
                    'kilometerstand' => $car->kilometerstand,
                    'vermogen' => $car->vermogen,
                    'cilinderinhoud' => $car->cilinderinhoud,
                    'apk' => optional($car->vervaldatum_apk)?->format('d-m-Y'),
                    'prijs' => $car->prijs ? (int) round($car->prijs / 100) : null,
                    'opties' => $car->extra_opties,
                    'bedrijf' => $car->company?->name,
                ], fn ($v) => $v !== null && $v !== '' && $v !== []);

                $result = $copy->generate($facts);
                $car->titel = $car->titel ?: $result['titel'];
                $car->beschrijving = $result['beschrijving'];
                if (! empty($result['opties'])) {
                    $car->extra_opties = $result['opties'];
                }
                $car->save();
                $done++;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return $done > 0
            ? "Advertentietekst geschreven voor {$done} auto's."
            : 'Ik kon geen teksten genereren, probeer het later opnieuw.';
    }
}
