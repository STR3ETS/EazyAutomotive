<?php

namespace App\Services\AI;

/**
 * Carries the authenticated scope for an agent run. Every tool uses this to
 * stay strictly within one company — the agent can never touch other tenants.
 */
class AgentContext
{
    public function __construct(
        public readonly int $companyId,
        public readonly int $userId,
        public readonly ?int $conversationId = null,
    ) {}
}
