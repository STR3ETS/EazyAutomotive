<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Services\AI\AgentContext;

class UpdateCarTool implements AgentTool
{
    private const STATUSES = ['draft', 'active', 'reserved', 'sold'];

    public function name(): string
    {
        return 'wijzig_auto';
    }

    public function description(): string
    {
        return 'Wijzig velden van een bestaande auto: prijs, status, titel, beschrijving, kilometerstand, uitgelicht (is_featured), kleur, merk, model of bouwjaar. Geef alleen de velden mee die je wilt aanpassen.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'auto_id' => ['type' => 'integer', 'description' => 'Het ID van de auto die je wijzigt.'],
                'prijs_euro' => ['type' => 'number', 'description' => 'Nieuwe vraagprijs in hele euros.'],
                'status' => ['type' => 'string', 'enum' => self::STATUSES, 'description' => 'Nieuwe status.'],
                'titel' => ['type' => 'string'],
                'beschrijving' => ['type' => 'string'],
                'kilometerstand' => ['type' => 'integer'],
                'is_featured' => ['type' => 'boolean', 'description' => 'Auto uitlichten op de website.'],
                'eerste_kleur' => ['type' => 'string'],
                'merk' => ['type' => 'string'],
                'handelsbenaming' => ['type' => 'string', 'description' => 'Model/handelsbenaming.'],
                'bouwjaar' => ['type' => 'integer'],
            ],
            'required' => ['auto_id'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        if (empty($input['auto_id'])) {
            return ToolResult::error('Geef het auto_id op van de auto die je wilt wijzigen.');
        }

        $car = Car::where('company_id', $context->companyId)->find((int) $input['auto_id']);
        if (! $car) {
            return ToolResult::error('Auto niet gevonden in de voorraad van dit bedrijf.');
        }

        $changes = [];

        if (array_key_exists('prijs_euro', $input)) {
            $changes['prijs'] = (int) round(((float) $input['prijs_euro']) * 100);
        }
        if (array_key_exists('status', $input)) {
            if (! in_array($input['status'], self::STATUSES, true)) {
                return ToolResult::error('Ongeldige status. Kies uit: ' . implode(', ', self::STATUSES) . '.');
            }
            $changes['status'] = $input['status'];
        }
        foreach (['titel', 'beschrijving', 'eerste_kleur', 'merk', 'handelsbenaming'] as $field) {
            if (array_key_exists($field, $input)) {
                $changes[$field] = (string) $input[$field];
            }
        }
        foreach (['kilometerstand', 'bouwjaar'] as $field) {
            if (array_key_exists($field, $input)) {
                $changes[$field] = (int) $input[$field];
            }
        }
        if (array_key_exists('is_featured', $input)) {
            $changes['is_featured'] = (bool) $input['is_featured'];
        }

        if ($changes === []) {
            return ToolResult::error('Geen velden om te wijzigen opgegeven.');
        }

        // Capture original values so the action can be undone exactly.
        $before = [];
        foreach (array_keys($changes) as $field) {
            $before[$field] = $car->getOriginal($field);
        }

        $car->update($changes);

        return ToolResult::ok(
            ['ok' => true, 'auto_id' => $car->id, 'gewijzigd' => array_keys($changes)],
            summary: "Auto bijgewerkt: {$car->display_title} (" . implode(', ', array_keys($changes)) . ')',
            undo: ['type' => 'updated', 'model' => Car::class, 'id' => $car->id, 'before' => $before],
            subjectType: Car::class,
            subjectId: $car->id,
        );
    }
}
