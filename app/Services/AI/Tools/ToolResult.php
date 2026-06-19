<?php

namespace App\Services\AI\Tools;

class ToolResult
{
    /**
     * @param array<string,mixed>|null $undo
     */
    public function __construct(
        public string $content,
        public bool $isError = false,
        public ?string $summary = null,
        public ?array $undo = null,
        public ?string $subjectType = null,
        public ?int $subjectId = null,
    ) {}

    /**
     * Successful result. Pass a `summary` (+ optional `undo`) only for mutating
     * actions — that's the signal to log the action to the activity feed.
     *
     * @param array<string,mixed>|string $data
     * @param array<string,mixed>|null   $undo
     */
    public static function ok(
        array|string $data,
        ?string $summary = null,
        ?array $undo = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
    ): self {
        return new self(
            content: is_array($data) ? (json_encode($data, JSON_UNESCAPED_UNICODE) ?: '{}') : $data,
            summary: $summary,
            undo: $undo,
            subjectType: $subjectType,
            subjectId: $subjectId,
        );
    }

    public static function error(string $message): self
    {
        return new self(
            content: json_encode(['error' => $message], JSON_UNESCAPED_UNICODE) ?: '{"error":"onbekend"}',
            isError: true,
        );
    }
}
