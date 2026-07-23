<?php

namespace App\Services\AI\Tools;

use App\Models\Car;
use App\Services\AI\AgentContext;
use App\Services\RdwService;

class ImportKentekensTool implements AgentTool
{
    private const MAX = 40;

    public function __construct(private RdwService $rdw) {}

    public function name(): string
    {
        return 'importeer_kentekens';
    }

    public function description(): string
    {
        return 'Importeer meerdere auto\'s tegelijk op basis van een lijst kentekens. De RDW-gegevens worden per kenteken automatisch opgehaald. Auto\'s komen standaard als concept binnen. Slaat dubbele en onbekende kentekens over.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'kentekens' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Lijst met kentekens.'],
                'status' => ['type' => 'string', 'enum' => ['draft', 'active'], 'description' => 'Concept (standaard) of direct actief.'],
            ],
            'required' => ['kentekens'],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $kentekens = is_array($input['kentekens'] ?? null) ? $input['kentekens'] : [];
        if ($kentekens === []) {
            return ToolResult::error('Geef een lijst met kentekens op.');
        }

        $status = ($input['status'] ?? 'draft') === 'active' ? 'active' : 'draft';
        $created = [];
        $duplicates = [];
        $notfound = [];
        $seen = [];
        $count = 0;

        foreach ($kentekens as $raw) {
            if ($count >= self::MAX) {
                break;
            }
            $kenteken = $this->rdw->normalizeKenteken((string) $raw);
            if ($kenteken === '' || isset($seen[$kenteken])) {
                continue;
            }
            $seen[$kenteken] = true;
            $count++;

            if (Car::where('company_id', $context->companyId)->where('kenteken', $kenteken)->exists()) {
                $duplicates[] = $kenteken;
                continue;
            }
            $rdwData = $this->rdw->fetchByKenteken($kenteken);
            if (! $rdwData) {
                $notfound[] = $kenteken;
                continue;
            }

            $attrs = $this->rdw->mapToCarAttributes($rdwData);
            $attrs['company_id'] = $context->companyId;
            $attrs['kenteken'] = $kenteken;
            $attrs['status'] = $status;
            Car::create($attrs);
            $created[] = $kenteken;
        }

        return ToolResult::ok([
            'toegevoegd' => count($created),
            'kentekens_toegevoegd' => $created,
            'al_aanwezig' => $duplicates,
            'niet_gevonden' => $notfound,
        ]);
    }
}
