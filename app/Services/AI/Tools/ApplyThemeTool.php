<?php

namespace App\Services\AI\Tools;

use App\Http\Controllers\WidgetController;
use App\Models\Company;
use App\Services\AI\AgentContext;

class ApplyThemeTool implements AgentTool
{
    public function name(): string
    {
        return 'pas_huisstijl_toe';
    }

    public function description(): string
    {
        $ids = implode(', ', array_column(WidgetController::THEMES, 'id'));

        return "Pas de huisstijl van de widgets aan. Kies een kant-en-klaar thema ({$ids}) of geef zelf een hoofdkleur op. Dit geldt voor alle widgets (voorraad, formulieren, taxatie). Tekstkleuren worden automatisch leesbaar gemaakt.";
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'thema' => ['type' => 'string', 'enum' => array_column(WidgetController::THEMES, 'id'), 'description' => 'Een kant-en-klaar thema.'],
                'hoofdkleur' => ['type' => 'string', 'description' => 'Eigen hoofdkleur als hex, bijv. #2563eb.'],
            ],
        ];
    }

    public function handle(array $input, AgentContext $context): ToolResult
    {
        $company = Company::find($context->companyId);
        if (! $company) {
            return ToolResult::error('Bedrijf niet gevonden.');
        }

        $before = $company->embed_settings ?? [];
        $changes = [];
        $wat = '';

        if (! empty($input['thema'])) {
            $theme = collect(WidgetController::THEMES)->firstWhere('id', $input['thema']);
            if (! $theme) {
                return ToolResult::error('Onbekend thema.');
            }
            foreach (['card_layout', 'primary_color', 'card_bg_color', 'card_border_radius', 'card_shadow', 'hover_effect', 'label_style', 'font_family'] as $k) {
                $changes[$k] = $theme[$k];
            }
            $wat = "Thema '{$theme['naam']}'";
        }

        if (! empty($input['hoofdkleur']) && preg_match('/^#[0-9A-Fa-f]{6}$/', $input['hoofdkleur'])) {
            $changes['primary_color'] = $input['hoofdkleur'];
            $wat = $wat ? $wat . " met kleur {$input['hoofdkleur']}" : "Hoofdkleur {$input['hoofdkleur']}";
        }

        if ($changes === []) {
            return ToolResult::error('Geef een thema of een geldige hoofdkleur (#RRGGBB) op.');
        }

        // Leesbare tekstkleuren afleiden uit de (nieuwe of huidige) achtergrond.
        $bg = $changes['card_bg_color'] ?? ($before['card_bg_color'] ?? '#ffffff');
        $light = $this->isLight($bg);
        $changes['title_color'] = $light ? '#111827' : '#f8fafc';
        $changes['label_bg_color'] = $light ? '#f3f4f6' : '#1f2a37';
        $changes['label_text_color'] = $light ? '#4b5563' : '#d1d5db';
        $changes['card_border_color'] = $light ? '#e5e7eb' : '#243040';
        $changes['detail_title_color'] = $changes['title_color'];
        $changes['detail_desc_color'] = $light ? '#6b7280' : '#cbd5e1';

        $company->update(['embed_settings' => array_merge($before, $changes)]);

        return ToolResult::ok(
            ['ok' => true, 'toegepast' => $wat],
            summary: "Huisstijl aangepast: {$wat}",
            undo: ['type' => 'updated', 'model' => Company::class, 'id' => $company->id, 'before' => ['embed_settings' => $before]],
            subjectType: Company::class,
            subjectId: $company->id,
        );
    }

    private function isLight(string $hex): bool
    {
        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $hex)) {
            return true;
        }
        $r = hexdec(substr($hex, 1, 2));
        $g = hexdec(substr($hex, 3, 2));
        $b = hexdec(substr($hex, 5, 2));

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 150;
    }
}
