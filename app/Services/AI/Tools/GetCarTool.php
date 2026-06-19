<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Services\AI\AgentContext;

class GetCarTool implements AgentTool
{
    public function name(): string
    {
        return 'bekijk_auto';
    }

    public function description(): string
    {
        return 'Haal de volledige details van één auto op, via het auto-id of het kenteken.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'auto_id' => [
                    'type' => 'integer',
                    'description' => 'Het ID van de auto.',
                ],
                'kenteken' => [
                    'type' => 'string',
                    'description' => 'Het kenteken (alternatief voor auto_id).',
                ],
            ],
            'required' => [],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $query = Car::query()->where('company_id', $context->companyId)->with('images');

        if (! empty($input['auto_id'])) {
            $car = $query->find((int) $input['auto_id']);
        } elseif (! empty($input['kenteken'])) {
            $kenteken = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $input['kenteken']));
            $car = $query->where('kenteken', $kenteken)->first();
        } else {
            return ToolResult::error('Geef een auto_id of kenteken op.');
        }

        if (! $car) {
            return ToolResult::error('Auto niet gevonden in de voorraad van dit bedrijf.');
        }

        return ToolResult::ok([
            'id' => $car->id,
            'kenteken' => $car->kenteken,
            'titel' => $car->display_title,
            'merk' => $car->merk,
            'model' => $car->handelsbenaming,
            'bouwjaar' => $car->bouwjaar,
            'prijs_euro' => $car->prijs ? (int) round($car->prijs / 100) : null,
            'prijs_type' => $car->prijs_type,
            'kilometerstand' => $car->kilometerstand,
            'status' => $car->status,
            'is_featured' => (bool) $car->is_featured,
            'kleur' => $car->eerste_kleur,
            'brandstof' => $car->brandstof_omschrijving,
            'apk_vervaldatum' => optional($car->vervaldatum_apk)->toDateString(),
            'beschrijving' => $car->beschrijving,
            'aantal_fotos' => $car->images->count(),
            'weergaven' => $car->view_count,
        ]);
    }
}
