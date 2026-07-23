<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Models\Customer;
use App\Models\Invoice;
use App\Services\AI\AgentContext;

class CreateInvoiceTool implements AgentTool
{
    public function name(): string
    {
        return 'maak_factuur';
    }

    public function description(): string
    {
        return 'Maak een factuur voor een klant met een of meer regels. Bedragen in hele euros. Standaard komt de factuur als concept in de administratie; zet direct_versturen op true om hem meteen definitief te maken (krijgt dan een factuurnummer en status verzonden). Geef een bestaand klant-id op (gebruik zoek_klanten) of een koper_naam. Kies btw-regeling marge (auto in de handel, standaard) of btw. Koppel optioneel een auto met auto_id; zonder regels wordt dan automatisch een regel op de vraagprijs gemaakt.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'klant_id' => ['type' => 'integer', 'description' => 'Id van een bestaande klant.'],
                'koper_naam' => ['type' => 'string', 'description' => 'Naam op de factuur als er geen klant-id is.'],
                'auto_id' => ['type' => 'integer', 'description' => 'Optioneel: koppel een auto uit de voorraad. Zonder regels wordt hieruit een factuurregel gemaakt.'],
                'btw_regeling' => ['type' => 'string', 'enum' => ['marge', 'btw'], 'description' => 'Margeregeling (standaard) of BTW-factuur.'],
                'prijzen_incl_btw' => ['type' => 'boolean', 'description' => 'Alleen bij btw: zijn de opgegeven prijzen inclusief BTW (standaard ja).'],
                'datum' => ['type' => 'string', 'description' => 'Factuurdatum JJJJ-MM-DD (standaard vandaag).'],
                'notities' => ['type' => 'string'],
                'direct_versturen' => ['type' => 'boolean', 'description' => 'Meteen definitief maken (factuurnummer + status verzonden) in plaats van concept.'],
                'regels' => [
                    'type' => 'array',
                    'description' => 'De factuurregels. Laat leeg als je alleen een auto koppelt.',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'omschrijving' => ['type' => 'string'],
                            'aantal' => ['type' => 'number', 'description' => 'Standaard 1.'],
                            'prijs_euro' => ['type' => 'number', 'description' => 'Stukprijs in hele euros.'],
                            'btw_tarief' => ['type' => 'integer', 'enum' => [0, 9, 21], 'description' => 'Alleen bij btw-regeling (standaard 21).'],
                            'inkoop_euro' => ['type' => 'number', 'description' => 'Alleen bij marge: inkoopprijs, voor de margeberekening.'],
                        ],
                        'required' => ['omschrijving', 'prijs_euro'],
                    ],
                ],
            ],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $companyId = $context->companyId;

        $customer = ! empty($input['klant_id'])
            ? Customer::where('company_id', $companyId)->find($input['klant_id'])
            : null;

        if (! empty($input['klant_id']) && ! $customer) {
            return ToolResult::error('Klant niet gevonden.');
        }

        $koperNaam = trim((string) ($input['koper_naam'] ?? ($customer?->label ?? '')));
        if (! $customer && $koperNaam === '') {
            return ToolResult::error('Geef een klant-id of een koper-naam op.');
        }

        $car = ! empty($input['auto_id'])
            ? Car::where('company_id', $companyId)->find($input['auto_id'])
            : null;
        if (! empty($input['auto_id']) && ! $car) {
            return ToolResult::error('Auto niet gevonden.');
        }

        $scheme = ($input['btw_regeling'] ?? 'marge') === 'btw' ? 'btw' : 'marge';

        // Bouw de regels. Zonder expliciete regels maar met een auto: een regel op de vraagprijs.
        $regels = is_array($input['regels'] ?? null) ? $input['regels'] : [];
        if ($regels === [] && $car) {
            $regels = [[
                'omschrijving' => trim($car->display_title . ($car->kenteken ? ' (' . $car->kenteken . ')' : '')),
                'aantal' => 1,
                'prijs_euro' => round((int) $car->prijs / 100, 2),
                'auto_id' => $car->id,
            ]];
        }

        $regels = array_values(array_filter($regels, fn ($r) => is_array($r) && trim((string) ($r['omschrijving'] ?? '')) !== ''));
        if ($regels === []) {
            return ToolResult::error('Geef minstens een factuurregel op (of koppel een auto met een vraagprijs).');
        }

        $date = ! empty($input['datum']) ? \Illuminate\Support\Carbon::parse($input['datum']) : now();

        $invoice = Invoice::create([
            'company_id' => $companyId,
            'customer_id' => $customer?->id,
            'car_id' => $car?->id,
            'status' => 'concept',
            'vat_scheme' => $scheme,
            'prices_include_vat' => $scheme === 'btw' ? (bool) ($input['prijzen_incl_btw'] ?? true) : false,
            'date' => $date,
            'notes' => $input['notities'] ?? null,
        ]);

        $position = 0;
        foreach ($regels as $r) {
            $lineCarId = ! empty($r['auto_id'])
                ? Car::where('company_id', $companyId)->where('id', $r['auto_id'])->value('id')
                : null;

            $invoice->lines()->create([
                'car_id' => $lineCarId,
                'description' => (string) $r['omschrijving'],
                'quantity' => isset($r['aantal']) ? (float) $r['aantal'] : 1,
                'unit_price' => $this->toCents($r['prijs_euro'] ?? 0),
                'vat_rate' => in_array((int) ($r['btw_tarief'] ?? 21), [0, 9, 21], true) ? (int) ($r['btw_tarief'] ?? 21) : 21,
                'purchase_price' => isset($r['inkoop_euro']) && $r['inkoop_euro'] !== '' && $r['inkoop_euro'] !== null
                    ? $this->toCents($r['inkoop_euro'])
                    : null,
                'position' => $position++,
            ]);
        }

        $invoice->load('lines');
        $invoice->recalculate();

        $verstuurd = false;
        if (! empty($input['direct_versturen'])) {
            $invoice->assignNumber();
            $invoice->fill([
                'status' => 'verzonden',
                'sent_at' => now(),
                'due_date' => now()->addDays((int) ($invoice->company->invoice_payment_terms ?? 14)),
                'bill_to_name' => $customer?->label ?: $koperNaam,
                'bill_to_address' => $customer?->address_block,
            ])->save();
            $verstuurd = true;
        }

        $naam = $customer?->label ?: $koperNaam;
        $summary = $verstuurd
            ? "Factuur {$invoice->number} gemaakt en verstuurd aan {$naam} (" . Invoice::eur((int) $invoice->total) . ')'
            : "Concept-factuur gemaakt voor {$naam} (" . Invoice::eur((int) $invoice->total) . ')';

        return ToolResult::ok(
            [
                'ok' => true,
                'id' => $invoice->id,
                'nummer' => $invoice->number ?: 'concept',
                'status' => $invoice->status,
                'subtotaal_euro' => round((int) $invoice->subtotal / 100, 2),
                'btw_euro' => round((int) $invoice->vat_amount / 100, 2),
                'totaal_euro' => round((int) $invoice->total / 100, 2),
                'url' => route('invoices.show', $invoice->id),
            ],
            summary: $summary,
            undo: ['type' => 'created', 'model' => Invoice::class, 'id' => $invoice->id],
            subjectType: Invoice::class,
            subjectId: $invoice->id,
        );
    }

    private function toCents($value): int
    {
        return (int) round(((float) str_replace(',', '.', (string) $value)) * 100);
    }
}
