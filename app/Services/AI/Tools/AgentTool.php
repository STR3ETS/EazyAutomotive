<?php

namespace App\Services\AI\Tools;

use App\Services\AI\AgentContext;

/**
 * A capability the AI colleague can use. Each tool is a thin wrapper over the
 * same services/models the UI uses, scoped to the current company.
 */
interface AgentTool
{
    /** Snake_case name the model calls (Dutch). */
    public function name(): string;

    /** Detailed description so the model knows when to use it. */
    public function description(): string;

    /**
     * The full JSON schema object for the tool input, e.g.
     * ['type' => 'object', 'properties' => [...], 'required' => [...]].
     * Always include at least one property so it serializes as a JSON object.
     *
     * @return array<string,mixed>
     */
    public function schema(): array;

    /**
     * Execute the tool. Mutating tools return a ToolResult with a `summary`
     * (and optional `undo` data) so the run gets logged and is reversible.
     *
     * @param array<string,mixed> $input
     */
    public function handle(array $input, AgentContext $context): ToolResult;
}
