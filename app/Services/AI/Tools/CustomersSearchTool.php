<?php

namespace App\Services\AI\Tools;

use App\Models\Customer;
use App\Services\AI\AgentContext;

class CustomersSearchTool implements AgentTool
{
    public function name(): string
    {
        return 'zoek_klanten';
    }

    public function description(): string
    {
        return 'Zoek klanten in de administratie op naam, e-mail of plaats. Handig om een klant-id te vinden voor een factuur of koopovereenkomst.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'zoekterm' => ['type' => 'string', 'description' => 'Zoek in naam, bedrijfsnaam, e-mail of plaats.'],
                'limiet' => ['type' => 'integer', 'description' => 'Max aantal (standaard 20).'],
            ],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $q = Customer::where('company_id', $context->companyId)->latest();

        if (! empty($input['zoekterm'])) {
            $s = (string) $input['zoekterm'];
            $q->where(fn ($w) => $w->where('naam', 'like', "%{$s}%")
                ->orWhere('bedrijfsnaam', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('plaats', 'like', "%{$s}%"));
        }

        $limit = max(1, min(50, (int) ($input['limiet'] ?? 20)));
        $klanten = $q->take($limit)->get();

        return ToolResult::ok([
            'aantal' => $klanten->count(),
            'klanten' => $klanten->map(fn (Customer $c) => [
                'id' => $c->id,
                'naam' => $c->label,
                'email' => $c->email,
                'telefoon' => $c->telefoon,
                'plaats' => $c->plaats,
            ])->all(),
        ]);
    }
}
