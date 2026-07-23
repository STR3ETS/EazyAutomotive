<?php

namespace App\Services\AI\Tools;

use App\Models\Lead;
use App\Services\AI\AgentContext;

class LeadsSearchTool implements AgentTool
{
    public function name(): string
    {
        return 'zoek_leads';
    }

    public function description(): string
    {
        return 'Zoek en toon leads (aanvragen) uit de CRM: contact, inruil, financiering en proefrit. Filter optioneel op status (nieuw, contact, afspraak, gewonnen, verloren), type of een zoekterm (naam/e-mail).';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'status' => ['type' => 'string', 'enum' => array_keys(Lead::STATUSES), 'description' => 'Filter op status.'],
                'type' => ['type' => 'string', 'enum' => array_keys(Lead::TYPES), 'description' => 'Filter op type lead.'],
                'zoekterm' => ['type' => 'string', 'description' => 'Zoek in naam of e-mail.'],
                'alleen_open' => ['type' => 'boolean', 'description' => 'Alleen openstaande leads (niet gewonnen/verloren).'],
                'limiet' => ['type' => 'integer', 'description' => 'Max aantal (standaard 20).'],
            ],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $q = Lead::where('company_id', $context->companyId)->with('car')->latest();

        if (! empty($input['status'])) {
            $q->where('status', $input['status']);
        }
        if (! empty($input['type'])) {
            $q->where('type', $input['type']);
        }
        if (! empty($input['alleen_open'])) {
            $q->whereNotIn('status', ['gewonnen', 'verloren']);
        }
        if (! empty($input['zoekterm'])) {
            $s = (string) $input['zoekterm'];
            $q->where(fn ($w) => $w->where('naam', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        $limit = max(1, min(50, (int) ($input['limiet'] ?? 20)));
        $leads = $q->take($limit)->get();

        return ToolResult::ok([
            'aantal' => $leads->count(),
            'leads' => $leads->map(fn (Lead $l) => [
                'id' => $l->id,
                'naam' => $l->naam,
                'type' => $l->type,
                'status' => $l->status,
                'email' => $l->email,
                'telefoon' => $l->telefoon,
                'auto' => $l->car?->display_title,
                'binnengekomen' => $l->created_at->format('d-m-Y'),
            ])->all(),
        ]);
    }
}
