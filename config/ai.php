<?php

return [
    // Anthropic API key (already present in .env as ANTHROPIC_API_KEY).
    'api_key' => env('ANTHROPIC_API_KEY'),

    'models' => [
        // The reasoning/acting brain.
        'brain' => env('AI_MODEL_BRAIN', 'claude-opus-4-8'),
        // Cheaper model for simple, high-volume tasks (summaries, classification).
        'cheap' => env('AI_MODEL_CHEAP', 'claude-haiku-4-5'),
    ],

    // Max tokens per model response.
    'max_tokens' => (int) env('AI_MAX_TOKENS', 4096),

    // Safety cap on how many tool-call rounds the agent may take in one turn.
    'max_tool_iterations' => (int) env('AI_MAX_TOOL_ITERATIONS', 12),
];
