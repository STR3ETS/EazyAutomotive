<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Services\AI\AgentContext;

class SearchInventoryTool implements AgentTool
{
    public function name(): string
    {
        return 'zoek_voorraad';
    }

    public function description(): string
    {
        return "Doorzoek de voorraad auto's van dit bedrijf. Filter op een zoekterm (merk, model of kenteken) en/of status. Gebruik dit om auto's te vinden voordat je ze bekijkt of wijzigt.";
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'zoekterm' => [
                    'type' => 'string',
                    'description' => 'Zoekt in merk, handelsbenaming en kenteken.',
                ],
                'status' => [
                    'type' => 'string',
                    'enum' => ['draft', 'active', 'reserved', 'sold'],
                    'description' => 'Filter op status (draft=concept, active=actief, reserved=gereserveerd, sold=verkocht).',
                ],
                'limiet' => [
                    'type' => 'integer',
                    'description' => 'Maximaal aantal resultaten (standaard 20, max 50).',
                ],
            ],
            'required' => [],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $limit = max(1, min((int) ($input['limiet'] ?? 20), 50));

        $cars = Car::query()
            ->where('company_id', $context->companyId)
            ->when($input['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($input['zoekterm'] ?? null, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('merk', 'like', "%{$s}%")
                    ->orWhere('handelsbenaming', 'like', "%{$s}%")
                    ->orWhere('kenteken', 'like', "%{$s}%");
            }))
            ->latest()
            ->limit($limit)
            ->get();

        $rows = $cars->map(fn (Car $car) => [
            'id' => $car->id,
            'kenteken' => $car->kenteken,
            'titel' => $car->display_title,
            'prijs_euro' => $car->prijs ? (int) round($car->prijs / 100) : null,
            'status' => $car->status,
            'kilometerstand' => $car->kilometerstand,
            'bouwjaar' => $car->bouwjaar,
            'weergaven' => $car->view_count,
        ])->all();

        return ToolResult::ok(['aantal' => count($rows), 'autos' => $rows]);
    }
}
