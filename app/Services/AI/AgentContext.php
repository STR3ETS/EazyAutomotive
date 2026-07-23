<?php

namespace App\Services\AI;

use App\Support\Roles;

/**
 * Carries the authenticated scope for an agent run. Every tool uses this to
 * stay strictly within one company, so the agent can never touch other tenants.
 * The role limits which functional areas the agent may act in, mirroring what
 * the same user can reach in the UI. A null role means no restriction (system).
 */
class AgentContext
{
    public function __construct(
        public readonly int $companyId,
        public readonly int $userId,
        public readonly ?int $conversationId = null,
        public readonly ?string $role = null,
    ) {}

    /** May the acting user reach this functional area? Null role = unrestricted. */
    public function allowsArea(string $area): bool
    {
        return $this->role === null || Roles::roleHasArea($this->role, $area);
    }
}
