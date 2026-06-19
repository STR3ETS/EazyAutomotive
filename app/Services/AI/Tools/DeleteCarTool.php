<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Services\AI\AgentContext;

class DeleteCarTool implements AgentTool
{
    public function name(): string
    {
        return 'verwijder_auto';
    }

    public function description(): string
    {
        return 'Verwijder een auto uit de voorraad. Dit is een zachte verwijdering die volledig terug te draaien is.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'auto_id' => ['type' => 'integer', 'description' => 'Het ID van de auto die je verwijdert.'],
            ],
            'required' => ['auto_id'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        if (empty($input['auto_id'])) {
            return ToolResult::error('Geef het auto_id op van de auto die je wilt verwijderen.');
        }

        $car = Car::where('company_id', $context->companyId)->find((int) $input['auto_id']);
        if (! $car) {
            return ToolResult::error('Auto niet gevonden in de voorraad van dit bedrijf.');
        }

        $title = $car->display_title;
        $kenteken = $car->kenteken;
        $car->delete();

        return ToolResult::ok(
            ['ok' => true, 'auto_id' => $car->id],
            summary: "Auto verwijderd: {$title} ({$kenteken})",
            undo: ['type' => 'deleted', 'model' => Car::class, 'id' => $car->id],
            subjectType: Car::class,
            subjectId: $car->id,
        );
    }
}
