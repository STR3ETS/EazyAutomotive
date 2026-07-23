<?php

namespace App\Services\AI;

use App\Services\AI\Tools\AddCarTool;
use App\Services\AI\Tools\AddCustomerTool;
use App\Services\AI\Tools\AddExpenseTool;
use App\Services\AI\Tools\AgentTool;
use App\Services\AI\Tools\ApplyThemeTool;
use App\Services\AI\Tools\CreateContractTool;
use App\Services\AI\Tools\CreateInvoiceTool;
use App\Services\AI\Tools\CustomersSearchTool;
use App\Services\AI\Tools\DeleteCarTool;
use App\Services\AI\Tools\GenerateCopyTool;
use App\Services\AI\Tools\GetCarTool;
use App\Services\AI\Tools\ImportKentekensTool;
use App\Services\AI\Tools\InventoryStatsTool;
use App\Services\AI\Tools\InvoicesSearchTool;
use App\Services\AI\Tools\LeadsSearchTool;
use App\Services\AI\Tools\ProefritSearchTool;
use App\Services\AI\Tools\PublishCarTool;
use App\Services\AI\Tools\RdwLookupTool;
use App\Services\AI\Tools\RegisterPaymentTool;
use App\Services\AI\Tools\SearchInventoryTool;
use App\Services\AI\Tools\SendInvoiceTool;
use App\Services\AI\Tools\ToolResult;
use App\Services\AI\Tools\UnpublishCarTool;
use App\Services\AI\Tools\UpdateCarTool;
use App\Services\AI\Tools\UpdateLeadTool;
use App\Services\AI\Tools\ValuationTool;
use App\Services\AI\Tools\VrijwaarTool;

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
            ValuationTool::class,
            LeadsSearchTool::class,
            CustomersSearchTool::class,
            ProefritSearchTool::class,
            InvoicesSearchTool::class,
            // Write (logged + reversible)
            AddCarTool::class,
            UpdateCarTool::class,
            DeleteCarTool::class,
            GenerateCopyTool::class,
            UpdateLeadTool::class,
            AddCustomerTool::class,
            AddExpenseTool::class,
            CreateContractTool::class,
            ApplyThemeTool::class,
            ImportKentekensTool::class,
            PublishCarTool::class,
            UnpublishCarTool::class,
            VrijwaarTool::class,
            CreateInvoiceTool::class,
            SendInvoiceTool::class,
            RegisterPaymentTool::class,
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

        // Rol-grens: de assistent mag alleen handelen binnen de gebieden waar de
        // ingelogde gebruiker ook bij mag (net als in de interface).
        $area = \App\Support\Roles::areaForTool($name);
        if ($area !== null && ! $context->allowsArea($area)) {
            $rol = \App\Support\Roles::label($context->role);
            $gebied = \App\Support\Roles::AREAS[$area] ?? $area;

            return ToolResult::error("Geen toegang: deze actie valt onder {$gebied} en jouw rol ({$rol}) heeft daar geen rechten voor. Vraag een beheerder om toegang.");
        }

        try {
            return $tool->handle($input, $context);
        } catch (\Throwable $e) {
            report($e);

            return ToolResult::error('Uitvoeren mislukt: ' . $e->getMessage());
        }
    }
}
