<?php

namespace App\Services\AI\Tools;

use App\Services\AI\AgentContext;
use App\Services\RdwService;
use App\Services\Valuation\ValuationEngine;

class ValuationTool implements AgentTool
{
    public function __construct(private RdwService $rdw, private ValuationEngine $engine) {}

    public function name(): string
    {
        return 'taxeer_kenteken';
    }

    public function description(): string
    {
        return 'Bepaal de indicatieve marktwaarde van een auto op basis van kenteken (en optioneel kilometerstand). Gebruikt live vergelijkbare advertenties van de markt. Geeft een prijsrange (onder/midden/boven) met betrouwbaarheid.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'kenteken' => ['type' => 'string', 'description' => 'Kenteken van de auto.'],
                'kilometerstand' => ['type' => 'integer', 'description' => 'Kilometerstand voor een nauwkeurigere schatting.'],
            ],
            'required' => ['kenteken'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $kenteken = $this->rdw->normalizeKenteken((string) ($input['kenteken'] ?? ''));
        if ($kenteken === '') {
            return ToolResult::error('Geef een geldig kenteken op.');
        }

        $rdwData = $this->rdw->fetchByKenteken($kenteken);
        if (! $rdwData) {
            return ToolResult::error("Kenteken {$kenteken} niet gevonden bij de RDW.");
        }

        $attrs = $this->rdw->mapToCarAttributes($rdwData);
        $km = isset($input['kilometerstand']) ? (int) $input['kilometerstand'] : null;

        $est = $this->engine->estimate([
            'merk' => $attrs['merk'] ?? null,
            'model' => $attrs['handelsbenaming'] ?? null,
            'bouwjaar' => $attrs['bouwjaar'] ?? null,
            'brandstof' => $attrs['brandstof_omschrijving'] ?? null,
            'catalogusprijs' => $attrs['catalogusprijs'] ?? null,
            'cilinderinhoud' => $attrs['cilinderinhoud'] ?? null,
        ], $km);

        return ToolResult::ok([
            'voertuig' => trim(($attrs['merk'] ?? '') . ' ' . RdwService::friendlyModel($attrs['handelsbenaming'] ?? '')),
            'bouwjaar' => $attrs['bouwjaar'] ?? null,
            'kilometerstand' => $km,
            'taxatie' => $est['beschikbaar'] ?? false ? [
                'onder_euro' => $est['onder'],
                'midden_euro' => $est['midden'],
                'boven_euro' => $est['boven'],
                'bron' => $est['bron'],
                'betrouwbaarheid' => $est['vertrouwen'],
                'aantal_advertenties' => $est['aantal'],
            ] : ['beschikbaar' => false, 'toelichting' => $est['toelichting'] ?? null],
        ]);
    }
}
