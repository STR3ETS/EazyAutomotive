<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Models\PlatformConnection;
use App\Services\AI\AgentContext;
use App\Services\Publishing\PublishingService;

class PublishCarTool implements AgentTool
{
    public function __construct(private PublishingService $publishing) {}

    public function name(): string
    {
        return 'publiceer_auto';
    }

    public function description(): string
    {
        return 'Publiceer een auto naar alle gekoppelde verkoopplatforms (bijv. Marktplaats, AutoTrack). Vereist dat er platforms zijn gekoppeld op de Publiceren-pagina.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'auto_id' => ['type' => 'integer', 'description' => 'Id van de auto in de voorraad.'],
            ],
            'required' => ['auto_id'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $car = Car::where('company_id', $context->companyId)->find((int) ($input['auto_id'] ?? 0));
        if (! $car) {
            return ToolResult::error('Auto niet gevonden.');
        }

        $connections = PlatformConnection::where('company_id', $context->companyId)->connected()->get();
        if ($connections->isEmpty()) {
            return ToolResult::error('Er zijn nog geen verkoopplatforms gekoppeld. Koppel eerst een platform op de Publiceren-pagina.');
        }

        $resultaat = [];
        foreach ($connections as $connection) {
            $publication = $this->publishing->publish($car, $connection);
            $resultaat[$connection->platform] = $publication->status;
        }

        return ToolResult::ok([
            'ok' => true,
            'auto' => $car->display_title,
            'platforms' => $resultaat,
        ]);
    }
}
