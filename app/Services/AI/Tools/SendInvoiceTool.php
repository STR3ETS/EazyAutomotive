<?php

namespace App\Services\AI\Tools;

use App\Models\Invoice;
use App\Services\AI\AgentContext;

class SendInvoiceTool implements AgentTool
{
    public function name(): string
    {
        return 'verstuur_factuur';
    }

    public function description(): string
    {
        return 'Maak een concept-factuur definitief: de factuur krijgt het volgende factuurnummer en de status verzonden, met een vervaldatum. Alleen voor concept-facturen met minstens een regel.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'factuur_id' => ['type' => 'integer', 'description' => 'Id van de concept-factuur (gebruik zoek_facturen).'],
            ],
            'required' => ['factuur_id'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $invoice = Invoice::where('company_id', $context->companyId)->find((int) ($input['factuur_id'] ?? 0));
        if (! $invoice) {
            return ToolResult::error('Factuur niet gevonden.');
        }
        if (! $invoice->isConcept()) {
            return ToolResult::error('Deze factuur is al definitief (nummer ' . $invoice->number . ').');
        }
        if ($invoice->lines()->count() === 0) {
            return ToolResult::error('Voeg eerst minstens een factuurregel toe.');
        }

        // Momentopname voor terugdraaien: alles wat definitief maken wijzigt.
        $before = [
            'status' => $invoice->status,
            'number' => $invoice->number,
            'sequence' => $invoice->sequence,
            'year' => $invoice->year,
            'sent_at' => $invoice->sent_at,
            'due_date' => $invoice->due_date,
            'bill_to_name' => $invoice->bill_to_name,
            'bill_to_address' => $invoice->bill_to_address,
        ];

        $invoice->recalculate();
        $invoice->assignNumber();
        $customer = $invoice->customer;
        $invoice->fill([
            'status' => 'verzonden',
            'sent_at' => now(),
            'due_date' => $invoice->due_date ?: now()->addDays((int) ($invoice->company->invoice_payment_terms ?? 14)),
            'bill_to_name' => $customer?->label,
            'bill_to_address' => $customer?->address_block,
        ])->save();

        return ToolResult::ok(
            [
                'ok' => true,
                'nummer' => $invoice->number,
                'status' => $invoice->status,
                'vervaldatum' => $invoice->due_date?->format('d-m-Y'),
                'totaal_euro' => round((int) $invoice->total / 100, 2),
                'url' => route('invoices.show', $invoice->id),
            ],
            summary: "Factuur {$invoice->number} definitief gemaakt (" . Invoice::eur((int) $invoice->total) . ')',
            undo: ['type' => 'updated', 'model' => Invoice::class, 'id' => $invoice->id, 'before' => $before],
            subjectType: Invoice::class,
            subjectId: $invoice->id,
        );
    }
}
