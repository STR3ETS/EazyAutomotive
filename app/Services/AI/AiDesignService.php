<?php

namespace App\Services\AI;

use Anthropic\Client;
use Illuminate\Support\Facades\Http;

/**
 * Generates one coherent, universal widget design (a subset of embed_settings)
 * from a free text prompt and, optionally, the brand colours and font scraped
 * from a URL. The design spans the whole widget: the inventory card and the
 * detail page share the same brand colour, font and feel (the proefrit widget
 * inherits the colour/font automatically). The model is forced to call the
 * apply_design tool so it always returns a structured object; every value is
 * re-validated server-side before it leaves this service so the live preview
 * (and the eventual save) can trust it.
 */
class AiDesignService
{
    /** Supported Google fonts that map 1:1 to the font_family option cards. */
    private const FONTS = ['system', 'Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins'];

    /**
     * @return array{settings:array<string,mixed>, brand:array<string,mixed>}
     */
    public function generate(string $prompt, ?string $url): array
    {
        $brand = ($url && trim($url) !== '') ? $this->extractBrand(trim($url)) : [];

        $client = new Client(apiKey: (string) config('ai.api_key'));

        $message = $client->messages->create(
            maxTokens: 2000,
            messages: [[
                'role' => 'user',
                'content' => $this->userPrompt($prompt, $brand),
            ]],
            model: (string) config('ai.models.brain'),
            system: $this->systemPrompt(),
            tools: [$this->tool()],
            toolChoice: ['type' => 'tool', 'name' => 'apply_design'],
        );

        $raw = [];
        foreach ($message->content as $block) {
            if ($block->type === 'tool_use' && $block->name === 'apply_design') {
                $raw = (array) $block->input;
                break;
            }
        }

        return [
            'settings' => $this->sanitize($raw),
            'brand' => $brand,
        ];
    }

    // ── Prompt building ──────────────────────────────────────────────

    private function systemPrompt(): string
    {
        return <<<PROMPT
Je bent een senior UI-ontwerper voor EazyAutomotive, een platform voor autohandelaren. Je ontwerpt het complete uiterlijk van een embeddable autowidget: de voorraadkaart (een auto-tegel in een grid) en de detailpagina van een auto.

Regels:
- Roep altijd de tool apply_design aan en geef alleen velden die in het schema staan. Verzin geen andere velden.
- Ontwerp de kaart en de detailpagina als één samenhangend geheel: dezelfde merkkleur, hetzelfde lettertype en dezelfde sfeer. Zet detail_custom op true.
- Lever een modern, professioneel ontwerp dat past bij de omschrijving en, als die gegeven zijn, bij de merkkleuren.
- Let op leesbaarheid: tekstkleuren moeten ruim voldoende contrast hebben met hun achtergrond (streef naar WCAG AA).
- Geef kleuren als hex in het formaat #RRGGBB.
PROMPT;
    }

    /**
     * @param array<string,mixed> $brand
     */
    private function userPrompt(string $prompt, array $brand): string
    {
        $lines = ['Gewenste stijl / omschrijving: ' . $prompt];

        if (! empty($brand['colors'])) {
            $lines[] = 'Merkkleuren van de website (meest voorkomend eerst): ' . implode(', ', $brand['colors']);
        }
        if (! empty($brand['fonts'])) {
            $lines[] = 'Lettertype(s) op de website: ' . implode(', ', $brand['fonts']);
        }

        $lines[] = 'Roep nu apply_design aan met een volledig, samenhangend en toegankelijk ontwerp.';

        return implode("\n", $lines);
    }

    private function tool(): array
    {
        $properties = [];
        foreach ($this->fields() as $key => $spec) {
            $properties[$key] = $this->schemaFor($spec);
        }

        return [
            'name' => 'apply_design',
            'description' => 'Pas het gegenereerde ontwerp toe op de hele widget (kaart en detailpagina). Geef de velden die je wilt instellen.',
            'inputSchema' => [
                'type' => 'object',
                'properties' => $properties,
                'required' => [],
            ],
        ];
    }

    /**
     * @param array<int,mixed> $spec
     * @return array<string,mixed>
     */
    private function schemaFor(array $spec): array
    {
        return match ($spec[0]) {
            'color' => ['type' => 'string', 'description' => 'Hex-kleur zoals #1A2B3C', 'pattern' => '^#[0-9A-Fa-f]{6}$'],
            'int' => ['type' => 'integer', 'minimum' => $spec[1], 'maximum' => $spec[2]],
            'enum' => ['type' => 'string', 'enum' => $spec[1]],
            'bool' => ['type' => 'boolean'],
            'text' => ['type' => 'string', 'maxLength' => $spec[1]],
            default => ['type' => 'string'],
        };
    }

    // ── Output validation ────────────────────────────────────────────

    /**
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    private function sanitize(array $input): array
    {
        $out = [];
        foreach ($this->fields() as $key => $spec) {
            if (! array_key_exists($key, $input)) {
                continue;
            }
            $val = $input[$key];

            switch ($spec[0]) {
                case 'color':
                    if (is_string($val) && preg_match('/^#[0-9A-Fa-f]{6}$/', $val)) {
                        $out[$key] = strtoupper($val);
                    }
                    break;
                case 'int':
                    if (is_numeric($val)) {
                        $out[$key] = max($spec[1], min($spec[2], (int) round((float) $val)));
                    }
                    break;
                case 'enum':
                    if (in_array($val, $spec[1], true)) {
                        $out[$key] = $val;
                    }
                    break;
                case 'bool':
                    $out[$key] = is_bool($val) ? $val : filter_var($val, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'text':
                    if (is_string($val)) {
                        $out[$key] = mb_substr(trim($val), 0, $spec[1]);
                    }
                    break;
            }
        }

        return $out;
    }

    /**
     * Single source of truth: maps an embed_settings key to a spec tuple used to
     * build both the tool schema and the server-side validation. One universal
     * design covering the card and the detail page so the whole widget stays
     * consistent. The card's column count is left out on purpose: it is a layout
     * choice the dealer makes, not part of the visual brand identity, and it has
     * no effect on the single-card preview.
     *
     * @return array<string, array<int,mixed>>
     */
    private function fields(): array
    {
        return [
            // Card / inventory visual identity
            'image_position' => ['enum', ['top', 'bottom']],
            'image_height' => ['int', 100, 350],
            'primary_color' => ['color'],
            'card_bg_color' => ['color'],
            'card_border_radius' => ['int', 0, 30],
            'card_padding' => ['int', 0, 40],
            'card_border_color' => ['color'],
            'card_border_width' => ['int', 0, 4],
            'card_shadow' => ['enum', ['none', 'sm', 'md', 'lg']],
            'hover_effect' => ['enum', ['lift', 'shadow', 'scale', 'glow', 'none']],
            'font_family' => ['enum', self::FONTS],
            'title_size' => ['int', 12, 28],
            'title_color' => ['color'],
            'price_size' => ['int', 12, 36],
            'currency' => ['enum', ['EUR', 'USD', 'GBP', 'none']],
            'label_style' => ['enum', ['badge', 'outline', 'icon-text', 'pill']],
            'label_bg_color' => ['color'],
            'label_text_color' => ['color'],
            'label_radius' => ['int', 0, 20],

            // Detail page (kept consistent with the card)
            'detail_custom' => ['bool'],
            'detail_bg_color' => ['color'],
            'detail_border_color' => ['color'],
            'detail_border_radius' => ['int', 0, 30],
            'detail_border_width' => ['int', 0, 4],
            'detail_padding' => ['int', 0, 60],
            'detail_title_size' => ['int', 14, 36],
            'detail_title_color' => ['color'],
            'detail_subtitle_color' => ['color'],
            'detail_price_color' => ['color'],
            'detail_price_size' => ['int', 14, 42],
            'detail_desc_color' => ['color'],
            'detail_desc_size' => ['int', 10, 20],
            'detail_gallery_height' => ['int', 150, 500],
            'detail_overlay_opacity' => ['int', 20, 90],
            'detail_shadow' => ['enum', ['none', 'sm', 'md', 'lg']],
            'detail_spec_columns' => ['int', 1, 3],
            'detail_spec_bg_color' => ['color'],
            'detail_spec_label_color' => ['color'],
            'detail_spec_value_color' => ['color'],
            'detail_spec_radius' => ['int', 0, 16],
            'detail_spec_gap' => ['int', 0, 16],
            'detail_badge_style' => ['enum', ['pill', 'badge', 'outline']],
            'detail_badge_bg_color' => ['color'],
            'detail_badge_text_color' => ['color'],
            'detail_badge_radius' => ['int', 0, 20],
        ];
    }

    // ── Brand extraction from a URL ──────────────────────────────────

    /**
     * @return array<string,mixed>
     */
    private function extractBrand(string $url): array
    {
        if (! $this->urlIsSafe($url)) {
            return [];
        }

        try {
            $resp = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; EazyAutomotiveBot/1.0; +https://eazyautomotive.nl)',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $resp->successful()) {
                return [];
            }

            $html = mb_substr((string) $resp->body(), 0, 400000);
        } catch (\Throwable $e) {
            return [];
        }

        return array_filter([
            'colors' => $this->extractColors($html),
            'fonts' => $this->extractFonts($html),
        ]);
    }

    /**
     * Rejects non-http(s) URLs and any host that resolves to a private or
     * reserved IP range (basic SSRF guard for server-side fetching).
     */
    private function urlIsSafe(string $url): bool
    {
        $parts = parse_url($url);
        if (! $parts || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return false;
        }

        $host = $parts['host'] ?? '';
        if ($host === '') {
            return false;
        }

        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (@gethostbynamel($host) ?: []);
        if ($ips === []) {
            return false;
        }

        foreach ($ips as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<string>
     */
    private function extractColors(string $html): array
    {
        $counts = [];

        // theme-color meta is a strong brand signal.
        if (preg_match_all('/<meta[^>]+name=["\']theme-color["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
            foreach ($m[1] as $c) {
                $hex = $this->normHex($c);
                if ($hex) {
                    $counts[$hex] = ($counts[$hex] ?? 0) + 50;
                }
            }
        }

        if (preg_match_all('/#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})\b/', $html, $m)) {
            foreach ($m[0] as $c) {
                $hex = $this->normHex($c);
                if ($hex) {
                    $counts[$hex] = ($counts[$hex] ?? 0) + 1;
                }
            }
        }

        arsort($counts);

        $ranked = [];
        foreach (array_keys($counts) as $hex) {
            if ($this->isGreyish($hex)) {
                continue;
            }
            $ranked[] = $hex;
            if (count($ranked) >= 6) {
                break;
            }
        }

        return $ranked;
    }

    /**
     * @return list<string>
     */
    private function extractFonts(string $html): array
    {
        $found = [];

        if (preg_match_all('/fonts\.googleapis\.com\/css2?\?[^"\'> ]*family=([^"\'&> ]+)/i', $html, $m)) {
            foreach ($m[1] as $fam) {
                $name = ucwords(str_replace('+', ' ', urldecode(explode(':', $fam)[0])));
                foreach (self::FONTS as $supported) {
                    if (strcasecmp($supported, $name) === 0) {
                        $found[] = $supported;
                    }
                }
            }
        }

        if (preg_match_all('/font-family\s*:\s*([^;{}"\']+)/i', $html, $m)) {
            foreach ($m[1] as $decl) {
                foreach (self::FONTS as $supported) {
                    if ($supported !== 'system' && stripos($decl, $supported) !== false) {
                        $found[] = $supported;
                    }
                }
            }
        }

        return array_values(array_unique($found));
    }

    private function normHex(string $c): ?string
    {
        $c = ltrim(trim($c), '#');
        if (preg_match('/^[0-9a-fA-F]{3}$/', $c)) {
            $c = $c[0] . $c[0] . $c[1] . $c[1] . $c[2] . $c[2];
        }
        if (! preg_match('/^[0-9a-fA-F]{6}$/', $c)) {
            return null;
        }

        return '#' . strtoupper($c);
    }

    private function isGreyish(string $hex): bool
    {
        $r = hexdec(substr($hex, 1, 2));
        $g = hexdec(substr($hex, 3, 2));
        $b = hexdec(substr($hex, 5, 2));
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        // Low saturation (greys, near-white, near-black) is rarely a brand accent.
        return ($max - $min) < 18;
    }
}
