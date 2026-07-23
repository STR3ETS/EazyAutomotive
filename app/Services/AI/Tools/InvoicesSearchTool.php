<?php

namespace App\Services\AI\Tools;

use App\Models\Invoice;
use App\Services\AI\AgentContext;

class InvoicesSearchTool implements AgentTool
{
    public function name(): string
    {
        return 'zoek_facturen';
    }

    public function description(): string
    {
        return 'Zoek en toon facturen: nummer, klant, bedragen (totaal, betaald, openstaand) en status. Filter optioneel op status (concept, verzonden, deels_betaald, betaald, vervallen, geannuleerd), een klant-id, alleen openstaande facturen, of een zoekterm (nummer of klantnaam).';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'status' => ['type' => 'string', 'enum' => array_keys(Invoice::STATUSES), 'description' => 'Filter op status.'],
                'klant_id' => ['type' => 'integer', 'description' => 'Alleen facturen van deze klant.'],
                'alleen_openstaand' => ['type' => 'boolean', 'description' => 'Alleen facturen met een openstaand bedrag (verzonden, deels betaald of vervallen).'],
                'zoekterm' => ['type' => 'string', 'description' => 'Zoek in factuurnummer of klantnaam.'],
                'limiet' => ['type' => 'integer', 'description' => 'Max aantal (standaard 20).'],
            ],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $q = Invoice::where('company_id', $context->companyId)->with('customer')->orderByDesc('id');

        if (! empty($input['status'])) {
            $q->where('status', $input['status']);
        }
        if (! empty($input['klant_id'])) {
            $q->where('customer_id', (int) $input['klant_id']);
        }
        if (! empty($input['alleen_openstaand'])) {
            $q->whereIn('status', ['verzonden', 'deels_betaald', 'vervallen']);
        }
        if (! empty($input['zoekterm'])) {
            $s = (string) $input['zoekterm'];
            $q->where(function ($w) use ($s) {
                $w->where('number', 'like', "%{$s}%")
                    ->orWhere('bill_to_name', 'like', "%{$s}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('naam', 'like', "%{$s}%")->orWhere('bedrijfsnaam', 'like', "%{$s}%"));
            });
        }

        $limit = max(1, min(50, (int) ($input['limiet'] ?? 20)));
        $invoices = $q->take($limit)->get();

        return ToolResult::ok([
            'aantal' => $invoices->count(),
            'facturen' => $invoices->map(fn (Invoice $f) => [
                'id' => $f->id,
                'nummer' => $f->number ?: 'concept',
                'klant' => $f->bill_to_name ?: ($f->customer?->label ?? '-'),
                'datum' => $f->date?->format('d-m-Y'),
                'status' => $f->status,
                'totaal_euro' => round((int) $f->total / 100, 2),
                'betaald_euro' => round((int) $f->amount_paid / 100, 2),
                'openstaand_euro' => round((int) $f->outstanding / 100, 2),
            ])->all(),
        ]);
    }
}
