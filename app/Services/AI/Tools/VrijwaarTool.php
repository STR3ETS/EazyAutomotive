<?php

namespace App\Services\AI\Tools;

use App\Models\BedrijfsvoorraadMutatie;
use App\Models\Car;
use App\Services\AI\AgentContext;
use App\Services\Rdw\Orv\OrvClient;
use Illuminate\Support\Carbon;

class VrijwaarTool implements AgentTool
{
    public function __construct(private OrvClient $orv) {}

    public function name(): string
    {
        return 'vrijwaar_auto';
    }

    public function description(): string
    {
        return 'Neem een voertuig op in de bedrijfsvoorraad (vrijwaring): de vorige eigenaar wordt gevrijwaard. Vereist het kenteken en de tenaamstellingscode (de 4 tot 12 cijfers van de kentekencard). Levert een vrijwaringsbewijs. Registermutatie, dus voer alleen uit als de gebruiker de code geeft.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'kenteken' => ['type' => 'string', 'description' => 'Kenteken van de in te kopen auto.'],
                'tenaamstellingscode' => ['type' => 'string', 'description' => 'De meldcode van de kentekencard (4 tot 12 cijfers).'],
            ],
            'required' => ['kenteken', 'tenaamstellingscode'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $kenteken = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) ($input['kenteken'] ?? '')));
        $code = preg_replace('/\D/', '', (string) ($input['tenaamstellingscode'] ?? ''));

        if ($kenteken === '') {
            return ToolResult::error('Geef een geldig kenteken op.');
        }
        if (! preg_match('/^\d{4,12}$/', $code)) {
            return ToolResult::error('Geef een geldige tenaamstellingscode (4 tot 12 cijfers).');
        }

        $result = $this->orv->vrijwaar($kenteken, $code);
        $carId = Car::where('company_id', $context->companyId)->where('kenteken', $kenteken)->value('id');

        BedrijfsvoorraadMutatie::create([
            'company_id' => $context->companyId,
            'user_id' => $context->userId,
            'car_id' => $carId,
            'type' => 'vrijwaring',
            'kenteken' => $kenteken,
            'status' => $result->geslaagd ? 'geslaagd' : 'mislukt',
            'mode' => $this->orv->mode(),
            'vrijwaringsbewijs' => $result->vrijwaringsbewijs,
            'bewijs_datum' => $result->datum ? Carbon::parse($result->datum) : null,
            'referentie' => $result->referentie,
            'foutmelding' => $result->foutmelding,
        ]);

        if (! $result->geslaagd) {
            return ToolResult::error('Vrijwaren mislukt: ' . ($result->foutmelding ?: 'onbekende fout bij de RDW.'));
        }

        return ToolResult::ok([
            'ok' => true,
            'kenteken' => $kenteken,
            'vrijwaringsbewijs' => $result->vrijwaringsbewijs,
            'modus' => $this->orv->mode(),
        ]);
    }
}
