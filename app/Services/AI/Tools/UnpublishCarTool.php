<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Services\AI\AgentContext;
use App\Services\Publishing\PublishingService;

class UnpublishCarTool implements AgentTool
{
    public function __construct(private PublishingService $publishing) {}

    public function name(): string
    {
        return 'depubliceer_auto';
    }

    public function description(): string
    {
        return 'Haal een auto weer offline van de gekoppelde verkoopplatforms.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'auto_id' => ['type' => 'integer', 'description' => 'Id van de auto.'],
            ],
            'required' => ['auto_id'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $car = Car::where('company_id', $context->companyId)->with('activePublications')->find((int) ($input['auto_id'] ?? 0));
        if (! $car) {
            return ToolResult::error('Auto niet gevonden.');
        }

        $platforms = [];
        foreach ($car->activePublications as $publication) {
            $this->publishing->unpublish($publication);
            $platforms[] = $publication->platform;
        }

        if ($platforms === []) {
            return ToolResult::error('Deze auto staat nergens gepubliceerd.');
        }

        return ToolResult::ok([
            'ok' => true,
            'auto' => $car->display_title,
            'offline_gehaald' => $platforms,
        ]);
    }
}
