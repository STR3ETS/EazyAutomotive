<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Models\Customer;
use App\Models\Koopovereenkomst;
use App\Services\AI\AgentContext;

class CreateContractTool implements AgentTool
{
    public function name(): string
    {
        return 'maak_koopovereenkomst';
    }

    public function description(): string
    {
        return 'Maak een koop-/verkoopovereenkomst voor een auto uit de voorraad aan een koper. Geef een bestaand klant-id op, of de naam van de koper. Prijzen in hele euros. Levert een genummerd, printbaar contract.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'auto_id' => ['type' => 'integer', 'description' => 'Id van de auto uit de voorraad.'],
                'klant_id' => ['type' => 'integer', 'description' => 'Id van een bestaande klant (gebruik zoek_klanten).'],
                'koper_naam' => ['type' => 'string', 'description' => 'Naam van de koper (als er geen klant-id is).'],
                'verkoopprijs_euro' => ['type' => 'number', 'description' => 'Verkoopprijs in hele euros (standaard de vraagprijs van de auto).'],
                'btw_type' => ['type' => 'string', 'enum' => ['marge', 'btw'], 'description' => 'Margeregeling of BTW-auto (standaard marge).'],
                'inruil_omschrijving' => ['type' => 'string'],
                'inruil_bedrag_euro' => ['type' => 'number'],
                'garantie' => ['type' => 'string', 'description' => 'Bijv. 6 maanden BOVAG-garantie.'],
                'bijzonderheden' => ['type' => 'string'],
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

        $customer = ! empty($input['klant_id'])
            ? Customer::where('company_id', $context->companyId)->find($input['klant_id'])
            : null;

        $koperNaam = trim((string) ($input['koper_naam'] ?? ($customer?->naam ?? $customer?->bedrijfsnaam ?? '')));
        if ($koperNaam === '') {
            return ToolResult::error('Geef een klant-id of een koper-naam op.');
        }

        $prijs = isset($input['verkoopprijs_euro'])
            ? (int) round(((float) $input['verkoopprijs_euro']) * 100)
            : (int) $car->prijs;

        $overeenkomst = new Koopovereenkomst([
            'btw_type' => ($input['btw_type'] ?? 'marge') === 'btw' ? 'btw' : 'marge',
            'verkoopprijs' => $prijs,
            'inruil_omschrijving' => $input['inruil_omschrijving'] ?? null,
            'inruil_bedrag' => isset($input['inruil_bedrag_euro']) ? (int) round(((float) $input['inruil_bedrag_euro']) * 100) : null,
            'garantie' => $input['garantie'] ?? null,
            'bijzonderheden' => $input['bijzonderheden'] ?? null,
            'status' => 'definitief',
            'koper' => [
                'naam' => $koperNaam,
                'bedrijfsnaam' => $customer?->bedrijfsnaam,
                'adres' => $customer?->adres,
                'postcode' => $customer?->postcode,
                'plaats' => $customer?->plaats,
                'email' => $customer?->email,
                'telefoon' => $customer?->telefoon,
                'kvk' => $customer?->kvk_nummer,
                'btw' => $customer?->btw_nummer,
            ],
            'voertuig' => [
                'kenteken' => $car->kenteken,
                'merk' => $car->merk,
                'model' => $car->handelsbenaming,
                'bouwjaar' => $car->bouwjaar,
                'kilometerstand' => $car->kilometerstand,
                'kleur' => $car->eerste_kleur,
                'brandstof' => $car->brandstof_omschrijving,
                'chassisnummer' => null,
            ],
        ]);
        $overeenkomst->company_id = $context->companyId;
        $overeenkomst->car_id = $car->id;
        $overeenkomst->customer_id = $customer?->id;
        $overeenkomst->assignNumber();
        $overeenkomst->save();

        return ToolResult::ok(
            ['ok' => true, 'nummer' => $overeenkomst->nummer, 'te_betalen_euro' => round($overeenkomst->teBetalen() / 100, 2), 'print_url' => route('koopovereenkomsten.print', $overeenkomst)],
            summary: "Koopovereenkomst {$overeenkomst->nummer} gemaakt voor {$car->display_title} aan {$koperNaam}",
            undo: ['type' => 'created', 'model' => Koopovereenkomst::class, 'id' => $overeenkomst->id],
            subjectType: Koopovereenkomst::class,
            subjectId: $overeenkomst->id,
        );
    }
}
