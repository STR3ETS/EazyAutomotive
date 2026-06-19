<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Models\CarView;
use App\Services\AI\AgentContext;

class InventoryStatsTool implements AgentTool
{
    public function name(): string
    {
        return 'voorraad_statistieken';
    }

    public function description(): string
    {
        return 'Geef een overzicht van de voorraad en kijkcijfers van dit bedrijf: aantallen per status en het aantal weergaven van vandaag, deze week en deze maand.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'toelichting' => [
                    'type' => 'string',
                    'description' => 'Optioneel: waar je de cijfers voor nodig hebt (alleen voor je eigen context).',
                ],
            ],
            'required' => [],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $companyId = $context->companyId;

        return ToolResult::ok([
            'totaal' => Car::where('company_id', $companyId)->count(),
            'actief' => Car::where('company_id', $companyId)->where('status', 'active')->count(),
            'gereserveerd' => Car::where('company_id', $companyId)->where('status', 'reserved')->count(),
            'verkocht' => Car::where('company_id', $companyId)->where('status', 'sold')->count(),
            'concept' => Car::where('company_id', $companyId)->where('status', 'draft')->count(),
            'weergaven_vandaag' => CarView::where('company_id', $companyId)->whereDate('created_at', today())->count(),
            'weergaven_week' => CarView::where('company_id', $companyId)->where('created_at', '>=', now()->subWeek())->count(),
            'weergaven_maand' => CarView::where('company_id', $companyId)->where('created_at', '>=', now()->subMonth())->count(),
        ]);
    }
}
