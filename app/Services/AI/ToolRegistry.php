<?php

namespace App\Services\AI;

use App\Services\AI\Tools\AddCarTool;
use App\Services\AI\Tools\AgentTool;
use App\Services\AI\Tools\DeleteCarTool;
use App\Services\AI\Tools\GetCarTool;
use App\Services\AI\Tools\InventoryStatsTool;
use App\Services\AI\Tools\RdwLookupTool;
use App\Services\AI\Tools\SearchInventoryTool;
use App\Services\AI\Tools\ToolResult;
use App\Services\AI\Tools\UpdateCarTool;

class ToolRegistry
{
    /** @var array<string,AgentTool> */
    private array $tools = [];

    public function __construct()
    {
        $classes = [
            // Read / research
            SearchInventoryTool::class,
            GetCarTool::class,
            InventoryStatsTool::class,
            RdwLookupTool::class,
            // Write (logged + reversible)
            AddCarTool::class,
            UpdateCarTool::class,
            DeleteCarTool::class,
        ];

        foreach ($classes as $class) {
            /** @var AgentTool $tool */
            $tool = app($class);
            $this->tools[$tool->name()] = $tool;
        }
    }

    /**
     * Tool definitions in the shape the Anthropic SDK expects (camelCase keys
     * that map to the wire `input_schema`).
     *
     * @return list<array<string,mixed>>
     */
    public function definitions(): array
    {
        $defs = [];
        foreach ($this->tools as $tool) {
            $defs[] = [
                'name' => $tool->name(),
                'description' => $tool->description(),
                'inputSchema' => $tool->schema(),
            ];
        }

        return $defs;
    }

    /**
     * @param array<string,mixed> $input
     */
    public function dispatch(string $name, array $input, AgentContext $context): ToolResult
    {
        $tool = $this->tools[$name] ?? null;

        if (! $tool) {
            return ToolResult::error("Onbekende tool: {$name}");
        }

        try {
            return $tool->handle($input, $context);
        } catch (\Throwable $e) {
            report($e);

            return ToolResult::error('Uitvoeren mislukt: ' . $e->getMessage());
        }
    }
}
