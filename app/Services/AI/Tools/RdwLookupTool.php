<?php

namespace App\Services\AI\Tools;

use App\Services\AI\AgentContext;
use App\Services\RdwService;

class RdwLookupTool implements AgentTool
{
    public function __construct(private RdwService $rdw) {}

    public function name(): string
    {
        return 'rdw_opzoeken';
    }

    public function description(): string
    {
        return 'Zoek officiële voertuiggegevens op bij de RDW via een kenteken (merk, model, bouwjaar, brandstof, APK-vervaldatum, gewicht enz.). Dit leest alleen gegevens en voegt niets toe aan de voorraad.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'kenteken' => [
                    'type' => 'string',
                    'description' => 'Het kenteken om op te zoeken.',
                ],
            ],
            'required' => ['kenteken'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $kenteken = trim((string) ($input['kenteken'] ?? ''));

        if ($kenteken === '') {
            return ToolResult::error('Geef een kenteken op.');
        }

        $data = $this->rdw->fetchByKenteken($kenteken);

        if (! $data) {
            return ToolResult::error('Kenteken niet gevonden bij de RDW.');
        }

        $mapped = $this->rdw->mapToCarAttributes($data);
        unset($mapped['rdw_raw_data']);

        return ToolResult::ok($mapped);
    }
}
