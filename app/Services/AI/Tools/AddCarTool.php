<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Services\AI\AgentContext;
use App\Services\RdwService;

class AddCarTool implements AgentTool
{
    private const STATUSES = ['draft', 'active', 'reserved', 'sold'];

    public function __construct(private RdwService $rdw) {}

    public function name(): string
    {
        return 'voeg_auto_toe';
    }

    public function description(): string
    {
        return "Voeg een nieuwe auto toe aan de voorraad op basis van een kenteken. De RDW-gegevens (merk, model, bouwjaar, brandstof, APK) worden automatisch ingevuld. Optioneel geef je prijs, kilometerstand, titel, beschrijving en status mee. Gebruik status 'active' om de auto direct te tonen, anders blijft hij 'draft' (concept).";
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'kenteken' => ['type' => 'string', 'description' => 'Het kenteken van de auto.'],
                'prijs_euro' => ['type' => 'number', 'description' => 'Vraagprijs in hele euros.'],
                'kilometerstand' => ['type' => 'integer', 'description' => 'Kilometerstand.'],
                'titel' => ['type' => 'string', 'description' => 'Optionele advertentietitel.'],
                'beschrijving' => ['type' => 'string', 'description' => 'Optionele omschrijving.'],
                'status' => [
                    'type' => 'string',
                    'enum' => self::STATUSES,
                    'description' => 'Status (standaard draft/concept).',
                ],
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

        $exists = Car::where('company_id', $context->companyId)->where('kenteken', $kenteken)->exists();
        if ($exists) {
            return ToolResult::error("Er staat al een auto met kenteken {$kenteken} in de voorraad.");
        }

        $rdwData = $this->rdw->fetchByKenteken($kenteken);
        if (! $rdwData) {
            return ToolResult::error("Kenteken {$kenteken} niet gevonden bij de RDW.");
        }

        $attributes = $this->rdw->mapToCarAttributes($rdwData);
        $attributes['company_id'] = $context->companyId;
        $attributes['kenteken'] = $kenteken;

        $status = $input['status'] ?? 'draft';
        $attributes['status'] = in_array($status, self::STATUSES, true) ? $status : 'draft';

        if (isset($input['prijs_euro'])) {
            $attributes['prijs'] = (int) round(((float) $input['prijs_euro']) * 100);
        }
        if (isset($input['kilometerstand'])) {
            $attributes['kilometerstand'] = (int) $input['kilometerstand'];
        }
        if (! empty($input['titel'])) {
            $attributes['titel'] = (string) $input['titel'];
        }
        if (! empty($input['beschrijving'])) {
            $attributes['beschrijving'] = (string) $input['beschrijving'];
        }

        $car = Car::create($attributes);

        return ToolResult::ok(
            [
                'ok' => true,
                'auto_id' => $car->id,
                'kenteken' => $car->kenteken,
                'titel' => $car->display_title,
                'status' => $car->status,
            ],
            summary: "Auto toegevoegd: {$car->display_title} ({$car->kenteken})",
            undo: ['type' => 'created', 'model' => Car::class, 'id' => $car->id],
            subjectType: Car::class,
            subjectId: $car->id,
        );
    }
}
