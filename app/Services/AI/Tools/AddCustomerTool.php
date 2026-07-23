<?php

namespace App\Services\AI\Tools;

use App\Models\Customer;
use App\Services\AI\AgentContext;

class AddCustomerTool implements AgentTool
{
    public function name(): string
    {
        return 'voeg_klant_toe';
    }

    public function description(): string
    {
        return 'Voeg een nieuwe klant toe aan de administratie (particulier of zakelijk). Handig voordat je een factuur of koopovereenkomst maakt.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'naam' => ['type' => 'string', 'description' => 'Naam van de contactpersoon of klant.'],
                'type' => ['type' => 'string', 'enum' => ['particulier', 'zakelijk'], 'description' => 'Particulier of zakelijk (standaard particulier).'],
                'bedrijfsnaam' => ['type' => 'string', 'description' => 'Bedrijfsnaam (bij zakelijk).'],
                'email' => ['type' => 'string', 'description' => 'E-mailadres.'],
                'telefoon' => ['type' => 'string', 'description' => 'Telefoonnummer.'],
                'adres' => ['type' => 'string', 'description' => 'Straat en huisnummer.'],
                'postcode' => ['type' => 'string'],
                'plaats' => ['type' => 'string'],
                'kvk_nummer' => ['type' => 'string'],
                'btw_nummer' => ['type' => 'string'],
            ],
            'required' => ['naam'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $naam = trim((string) ($input['naam'] ?? ''));
        if ($naam === '') {
            return ToolResult::error('Geef een naam op.');
        }

        $customer = Customer::create([
            'company_id' => $context->companyId,
            'type' => ($input['type'] ?? 'particulier') === 'zakelijk' ? 'zakelijk' : 'particulier',
            'naam' => $naam,
            'bedrijfsnaam' => $input['bedrijfsnaam'] ?? null,
            'email' => $input['email'] ?? null,
            'telefoon' => $input['telefoon'] ?? null,
            'adres' => $input['adres'] ?? null,
            'postcode' => $input['postcode'] ?? null,
            'plaats' => $input['plaats'] ?? null,
            'kvk_nummer' => $input['kvk_nummer'] ?? null,
            'btw_nummer' => $input['btw_nummer'] ?? null,
        ]);

        return ToolResult::ok(
            ['ok' => true, 'klant_id' => $customer->id, 'naam' => $customer->label],
            summary: "Klant toegevoegd: {$customer->label}",
            undo: ['type' => 'created', 'model' => Customer::class, 'id' => $customer->id],
            subjectType: Customer::class,
            subjectId: $customer->id,
        );
    }
}
