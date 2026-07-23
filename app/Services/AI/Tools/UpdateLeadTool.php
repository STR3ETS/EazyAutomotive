<?php

namespace App\Services\AI\Tools;

use App\Models\Lead;
use App\Services\AI\AgentContext;

class UpdateLeadTool implements AgentTool
{
    public function name(): string
    {
        return 'wijzig_lead';
    }

    public function description(): string
    {
        return 'Werk een lead bij: zet de status (nieuw, contact, afspraak, gewonnen, verloren) en/of voeg een interne notitie toe. Gebruik zoek_leads om het lead-id te vinden.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'lead_id' => ['type' => 'integer', 'description' => 'Het id van de lead.'],
                'status' => ['type' => 'string', 'enum' => array_keys(Lead::STATUSES), 'description' => 'Nieuwe status.'],
                'notitie' => ['type' => 'string', 'description' => 'Interne notitie (wordt toegevoegd).'],
            ],
            'required' => ['lead_id'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $lead = Lead::where('company_id', $context->companyId)->find((int) ($input['lead_id'] ?? 0));
        if (! $lead) {
            return ToolResult::error('Lead niet gevonden.');
        }

        $before = ['status' => $lead->status, 'notes' => $lead->notes];

        if (! empty($input['status'])) {
            $lead->status = $input['status'];
        }
        if (! empty($input['notitie'])) {
            $lead->notes = trim(($lead->notes ? $lead->notes . "\n" : '') . $input['notitie']);
        }
        $lead->save();

        return ToolResult::ok(
            ['ok' => true, 'lead_id' => $lead->id, 'status' => $lead->status],
            summary: "Lead bijgewerkt: {$lead->naam} (status: {$lead->status})",
            undo: ['type' => 'updated', 'model' => Lead::class, 'id' => $lead->id, 'before' => $before],
            subjectType: Lead::class,
            subjectId: $lead->id,
        );
    }
}
