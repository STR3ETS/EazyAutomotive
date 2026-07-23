<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Services\AI\AgentContext;
use App\Services\AI\CarCopyService;

class GenerateCopyTool implements AgentTool
{
    public function __construct(private CarCopyService $copy) {}

    public function name(): string
    {
        return 'genereer_advertentietekst';
    }

    public function description(): string
    {
        return 'Schrijf en plaats een verkoopadvertentie (titel + beschrijving + opgeschoonde opties) voor een auto in de voorraad. Gebruikt de bekende gegevens van de auto. Optioneel geef je accenten mee die benadrukt moeten worden.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'auto_id' => ['type' => 'integer', 'description' => 'Het id van de auto in de voorraad.'],
                'accent' => ['type' => 'string', 'description' => 'Optionele punten om te benadrukken (alleen echte feiten).'],
            ],
            'required' => ['auto_id'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $car = Car::where('company_id', $context->companyId)->with('company')->find((int) ($input['auto_id'] ?? 0));
        if (! $car) {
            return ToolResult::error('Auto niet gevonden.');
        }

        $facts = array_filter([
            'merk' => $car->merk,
            'model' => $car->handelsbenaming,
            'bouwjaar' => $car->bouwjaar,
            'brandstof' => $car->brandstof_omschrijving,
            'kleur' => $car->eerste_kleur,
            'kilometerstand' => $car->kilometerstand,
            'vermogen' => $car->vermogen,
            'cilinderinhoud' => $car->cilinderinhoud,
            'aantal_deuren' => $car->aantal_deuren,
            'aantal_zitplaatsen' => $car->aantal_zitplaatsen,
            'apk' => optional($car->vervaldatum_apk)?->format('d-m-Y'),
            'prijs' => $car->prijs ? (int) round($car->prijs / 100) : null,
            'opties' => $car->extra_opties,
            'accent' => $input['accent'] ?? null,
            'bedrijf' => $car->company?->name,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);

        $result = $this->copy->generate($facts);

        $before = ['titel' => $car->titel, 'beschrijving' => $car->beschrijving, 'extra_opties' => $car->extra_opties];

        $car->titel = $result['titel'];
        $car->beschrijving = $result['beschrijving'];
        if (! empty($result['opties'])) {
            $car->extra_opties = $result['opties'];
        }
        $car->save();

        return ToolResult::ok(
            ['ok' => true, 'auto_id' => $car->id, 'titel' => $car->titel],
            summary: "Advertentietekst geschreven voor {$car->display_title}",
            undo: ['type' => 'updated', 'model' => Car::class, 'id' => $car->id, 'before' => $before],
            subjectType: Car::class,
            subjectId: $car->id,
        );
    }
}
