<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Models\Expense;
use App\Services\AI\AgentContext;

class AddExpenseTool implements AgentTool
{
    public function name(): string
    {
        return 'voeg_kosten_toe';
    }

    public function description(): string
    {
        return 'Boek een kostenpost in de boekhouding (bedrag in hele euros, inclusief BTW). De BTW wordt automatisch uitgesplitst. Optioneel koppel je de kosten aan een auto.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'omschrijving' => ['type' => 'string', 'description' => 'Wat is de uitgave.'],
                'bedrag_incl_euro' => ['type' => 'number', 'description' => 'Totaalbedrag inclusief BTW, in hele euros.'],
                'categorie' => ['type' => 'string', 'enum' => array_keys(Expense::CATEGORIES), 'description' => 'Kostencategorie.'],
                'btw_percentage' => ['type' => 'integer', 'enum' => [0, 9, 21], 'description' => 'BTW-tarief (standaard 21).'],
                'datum' => ['type' => 'string', 'description' => 'Datum (JJJJ-MM-DD, standaard vandaag).'],
                'leverancier' => ['type' => 'string', 'description' => 'Naam van de leverancier.'],
                'auto_id' => ['type' => 'integer', 'description' => 'Optioneel: koppel aan een auto.'],
            ],
            'required' => ['omschrijving', 'bedrag_incl_euro'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $incl = (int) round(((float) ($input['bedrag_incl_euro'] ?? 0)) * 100);
        if ($incl <= 0) {
            return ToolResult::error('Geef een bedrag groter dan nul op.');
        }

        $vatRate = (int) ($input['btw_percentage'] ?? 21);
        if (! in_array($vatRate, [0, 9, 21], true)) {
            $vatRate = 21;
        }
        $excl = (int) round($incl / (1 + $vatRate / 100));

        $carId = null;
        if (! empty($input['auto_id'])) {
            $carId = Car::where('company_id', $context->companyId)->where('id', $input['auto_id'])->value('id');
        }

        $category = $input['categorie'] ?? 'overig';
        if (! array_key_exists($category, Expense::CATEGORIES)) {
            $category = 'overig';
        }

        $expense = Expense::create([
            'company_id' => $context->companyId,
            'car_id' => $carId,
            'date' => ! empty($input['datum']) ? $input['datum'] : now()->toDateString(),
            'supplier' => $input['leverancier'] ?? null,
            'description' => (string) $input['omschrijving'],
            'category' => $category,
            'amount_excl' => $excl,
            'vat_rate' => $vatRate,
            'vat_amount' => $incl - $excl,
            'amount_incl' => $incl,
        ]);

        return ToolResult::ok(
            ['ok' => true, 'kosten_id' => $expense->id, 'bedrag_incl_euro' => round($incl / 100, 2)],
            summary: 'Kosten geboekt: ' . mb_strimwidth($expense->description, 0, 40, '...') . ' (EUR ' . number_format($incl / 100, 0, ',', '.') . ')',
            undo: ['type' => 'created', 'model' => Expense::class, 'id' => $expense->id],
            subjectType: Expense::class,
            subjectId: $expense->id,
        );
    }
}
