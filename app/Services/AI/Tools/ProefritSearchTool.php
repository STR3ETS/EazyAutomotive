<?php

namespace App\Services\AI\Tools;

use App\Models\ProefritAanvraag;
use App\Services\AI\AgentContext;

class ProefritSearchTool implements AgentTool
{
    public function name(): string
    {
        return 'zoek_proefritten';
    }

    public function description(): string
    {
        return 'Toon proefrit-aanvragen die via de website zijn binnengekomen, optioneel gefilterd op status.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'status' => ['type' => 'string', 'description' => 'Filter op status (bijv. nieuw).'],
                'limiet' => ['type' => 'integer', 'description' => 'Max aantal (standaard 20).'],
            ],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $q = ProefritAanvraag::where('company_id', $context->companyId)->with('car')->latest();

        if (! empty($input['status'])) {
            $q->where('status', $input['status']);
        }

        $limit = max(1, min(50, (int) ($input['limiet'] ?? 20)));
        $aanvragen = $q->take($limit)->get();

        return ToolResult::ok([
            'aantal' => $aanvragen->count(),
            'proefritten' => $aanvragen->map(fn (ProefritAanvraag $p) => [
                'id' => $p->id,
                'naam' => $p->naam,
                'email' => $p->email,
                'telefoon' => $p->telefoon,
                'auto' => $p->car?->display_title,
                'gewenste_datum' => optional($p->gewenste_datum)?->format('d-m-Y'),
                'status' => $p->status,
                'binnengekomen' => $p->created_at->format('d-m-Y'),
            ])->all(),
        ]);
    }
}
