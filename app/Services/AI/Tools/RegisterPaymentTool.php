<?php

namespace App\Services\AI\Tools;

use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Services\AI\AgentContext;

class RegisterPaymentTool implements AgentTool
{
    public function name(): string
    {
        return 'registreer_betaling';
    }

    public function description(): string
    {
        return 'Registreer een (deel)betaling op een definitieve factuur. Zonder bedrag wordt het volledige openstaande bedrag geboekt. De factuurstatus wordt automatisch bijgewerkt naar deels betaald of betaald. Bedragen in hele euros.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'factuur_id' => ['type' => 'integer', 'description' => 'Id van de factuur (gebruik zoek_facturen).'],
                'bedrag_euro' => ['type' => 'number', 'description' => 'Betaald bedrag in hele euros (standaard het volledige openstaande bedrag).'],
                'datum' => ['type' => 'string', 'description' => 'Betaaldatum JJJJ-MM-DD (standaard vandaag).'],
                'methode' => ['type' => 'string', 'enum' => ['bank', 'contant', 'pin'], 'description' => 'Betaalwijze.'],
                'notitie' => ['type' => 'string'],
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
        if ($invoice->isConcept()) {
            return ToolResult::error('Maak de factuur eerst definitief met verstuur_factuur.');
        }

        $outstanding = (int) $invoice->outstanding;
        $amount = isset($input['bedrag_euro'])
            ? (int) round(((float) str_replace(',', '.', (string) $input['bedrag_euro'])) * 100)
            : $outstanding;

        if ($amount <= 0) {
            return ToolResult::error('Er staat niets meer open op deze factuur.');
        }

        $date = ! empty($input['datum']) ? \Illuminate\Support\Carbon::parse($input['datum']) : now();

        $payment = $invoice->payments()->create([
            'company_id' => $invoice->company_id,
            'date' => $date,
            'amount' => $amount,
            'method' => in_array($input['methode'] ?? null, ['bank', 'contant', 'pin'], true) ? $input['methode'] : null,
            'note' => $input['notitie'] ?? null,
        ]);

        $invoice->syncPaymentStatus();

        return ToolResult::ok(
            [
                'ok' => true,
                'nummer' => $invoice->number,
                'status' => $invoice->status,
                'betaald_euro' => round((int) $invoice->amount_paid / 100, 2),
                'openstaand_euro' => round((int) $invoice->outstanding / 100, 2),
            ],
            summary: 'Betaling ' . Invoice::eur($amount) . " geregistreerd op factuur {$invoice->number} (status: {$invoice->status})",
            undo: ['type' => 'created', 'model' => InvoicePayment::class, 'id' => $payment->id],
            subjectType: Invoice::class,
            subjectId: $invoice->id,
        );
    }
}
