<?php

namespace App\Services\AI;

use Anthropic\Client;

/**
 * Generates the sales-facing copy for a car listing (titel, beschrijving and a
 * cleaned-up option list) from the facts already on the form: the RDW data plus
 * whatever the dealer typed (price, mileage, options, a free-text accent note).
 *
 * The model is forced to call schrijf_advertentie so it always returns a
 * structured object. Two things are enforced, not merely requested:
 *  - the prompt bans AI-tells and em-dashes, and
 *  - sanitize() strips every kind of long dash server-side as a hard guarantee,
 *    because the no-em-dash rule must hold even if the model slips.
 *
 * The model is told to use ONLY the given facts: no invented history, owners,
 * warranty or options. Whatever leaves this service is safe for the dealer to
 * accept as-is, but they still press save (which re-validates) to persist it.
 */
class CarCopyService
{
    /** Fields we accept as grounding facts, with a human label for the prompt. */
    private const FACT_LABELS = [
        'merk' => 'Merk',
        'model' => 'Model',
        'bouwjaar' => 'Bouwjaar',
        'brandstof' => 'Brandstof',
        'transmissie' => 'Transmissie',
        'carrosserie' => 'Carrosserie',
        'kleur' => 'Kleur',
        'kilometerstand' => 'Kilometerstand',
        'vermogen' => 'Vermogen (pk)',
        'cilinderinhoud' => 'Cilinderinhoud (cc)',
        'aantal_deuren' => 'Aantal deuren',
        'aantal_zitplaatsen' => 'Aantal zitplaatsen',
        'apk' => 'APK geldig tot',
        'prijs' => 'Vraagprijs (euro)',
    ];

    /**
     * @param array<string,mixed> $facts  Raw car facts (any subset of FACT_LABELS keys),
     *                                     plus optional 'opties' (string|array),
     *                                     'accent' (free text) and 'bedrijf' (dealer name).
     * @return array{titel:string, beschrijving:string, opties:list<string>}
     */
    public function generate(array $facts): array
    {
        $client = new Client(apiKey: (string) config('ai.api_key'));

        $message = $client->messages->create(
            maxTokens: 1500,
            messages: [[
                'role' => 'user',
                'content' => $this->userPrompt($facts),
            ]],
            model: (string) config('ai.models.brain'),
            system: $this->systemPrompt(),
            tools: [$this->tool()],
            toolChoice: ['type' => 'tool', 'name' => 'schrijf_advertentie'],
        );

        $raw = [];
        foreach ($message->content as $block) {
            if ($block->type === 'tool_use' && $block->name === 'schrijf_advertentie') {
                $raw = (array) $block->input;
                break;
            }
        }

        return $this->sanitize($raw);
    }

    // ── Prompt building ──────────────────────────────────────────────

    private function systemPrompt(): string
    {
        return <<<PROMPT
Je bent een ervaren Nederlandse autoverkoper en SEO-copywriter. Je schrijft de advertentietekst voor een occasion die een autohandelaar online zet.

Werkwijze:
- Roep altijd de tool schrijf_advertentie aan en lever titel, beschrijving en opties.
- Schrijf in vloeiend, natuurlijk Nederlands, zoals een ervaren verkoper het zelf zou opschrijven. Een koper moet niet kunnen merken dat een computer dit schreef.

Feiten en eerlijkheid:
- Gebruik uitsluitend de feiten die je krijgt. Verzin niets. Dus geen onderhoudshistorie, geen aantal eigenaren, geen dealeronderhoud, geen garantie, geen schadevrij, geen "pas geleverd" en geen opties die niet zijn opgegeven.
- Twijfel je of iets klopt, laat het dan weg.

SEO:
- Verwerk merk, model, bouwjaar, brandstof en transmissie op een natuurlijke manier in de titel en in de eerste zinnen, want daar zoeken kopers op.
- Noem de belangrijkste opties met hun gangbare zoekterm (bijvoorbeeld navigatie, achteruitrijcamera, stoelverwarming, adaptive cruise control).
- Schrijf zoals een koper zou zoeken, maar prop geen trefwoorden vol en herhaal niet onnodig. Leesbaarheid gaat voor.

Structuur en toon:
- Begin met een pakkende, concrete openingszin. Daarna een of twee korte alinea's, eventueel gevolgd door een korte opsomming van de sterkste pluspunten.
- Houd het compleet maar scanbaar. Menselijk, enthousiast maar zakelijk en eerlijk. Geen loze superlatieven en geen overdrijving.
- Noem de vraagprijs niet in de beschrijving, die staat al apart op de pagina.
- Sluit niet af met een generieke verkoopzin of een oproep om contact op te nemen, tenzij dat echt natuurlijk past.

Harde stijlregels:
- Gebruik NOOIT lange streepjes (em-dash of en-dash) als leesteken. Gebruik alleen gewone komma's, punten en gewone koppelstreepjes.
- Vermijd AI-taal en clichés. Gebruik onder andere NIET: "of je nu", "naadloos", "in de wereld van", "maak kennis met", "niet alleen ... maar ook", "een ware", "het beste van twee werelden", "wanneer het aankomt op", "boordevol", "tot in de puntjes", "een lust voor het oog", "betrouwbare metgezel", "perfecte combinatie", "geen wonder dat". Schrijf in plaats daarvan concreet en specifiek over deze auto.
- Geen tekst in HOOFDLETTERS en geen overdreven uitroeptekens.

Titel:
- Kort en krachtig, met merk en model plus het sterkste verkoopargument of een kenmerkende eigenschap. Maximaal ongeveer 70 tekens.

Opties:
- Lever een opgeschoonde lijst van de opgegeven opties: juiste spelling en hoofdletters, ontdubbeld, logisch geordend met de aantrekkelijkste opties eerst. Voeg geen opties toe die niet zijn opgegeven.
PROMPT;
    }

    /**
     * @param array<string,mixed> $facts
     */
    private function userPrompt(array $facts): string
    {
        $lines = ['Gegevens van deze auto:'];

        foreach (self::FACT_LABELS as $key => $label) {
            $value = $facts[$key] ?? null;
            if ($value === null || $value === '' || $value === 0 || $value === '0') {
                continue;
            }
            $lines[] = "- {$label}: " . $this->formatFact($key, $value);
        }

        $opties = $this->normalizeOptions($facts['opties'] ?? null);
        if ($opties !== []) {
            $lines[] = '- Door de handelaar opgegeven opties: ' . implode(', ', $opties);
        }

        $accent = isset($facts['accent']) ? trim((string) $facts['accent']) : '';
        if ($accent !== '') {
            $lines[] = '';
            $lines[] = 'Extra punten die de handelaar wil benadrukken (alleen gebruiken als ze hier staan): ' . $accent;
        }

        if (! empty($facts['bedrijf'])) {
            $lines[] = '';
            $lines[] = 'Verkopende handelaar: ' . trim((string) $facts['bedrijf']) . '. Noem de naam hooguit één keer en alleen als het natuurlijk past.';
        }

        $lines[] = '';
        $lines[] = 'Schrijf nu de advertentie. Roep schrijf_advertentie aan.';

        return implode("\n", $lines);
    }

    private function formatFact(string $key, mixed $value): string
    {
        return match ($key) {
            'kilometerstand' => number_format((int) $value, 0, ',', '.') . ' km',
            'prijs' => '€ ' . number_format((float) $value, 0, ',', '.'),
            'cilinderinhoud' => number_format((int) $value, 0, ',', '.') . ' cc',
            default => trim((string) $value),
        };
    }

    private function tool(): array
    {
        return [
            'name' => 'schrijf_advertentie',
            'description' => 'Lever de verkoopadvertentie voor deze auto: een pakkende titel, een complete maar leesbare beschrijving en een opgeschoonde lijst opties.',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'titel' => [
                        'type' => 'string',
                        'description' => 'Korte, pakkende titel met merk en model. Max ongeveer 70 tekens.',
                    ],
                    'beschrijving' => [
                        'type' => 'string',
                        'description' => 'Complete, menselijke en SEO-vriendelijke verkooptekst. Geen em-streepjes, geen AI-clichés.',
                    ],
                    'opties' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                        'description' => 'Opgeschoonde, ontdubbelde lijst van de opgegeven opties. Verzin geen nieuwe opties.',
                    ],
                ],
                'required' => ['titel', 'beschrijving'],
            ],
        ];
    }

    // ── Output validation ────────────────────────────────────────────

    /**
     * @param array<string,mixed> $input
     * @return array{titel:string, beschrijving:string, opties:list<string>}
     */
    private function sanitize(array $input): array
    {
        $titel = $this->collapseSpaces($this->stripLongDashes(trim((string) ($input['titel'] ?? ''))));
        $titel = mb_substr($titel, 0, 120);

        $beschrijving = $this->stripLongDashes(trim((string) ($input['beschrijving'] ?? '')));
        // Keep paragraph breaks, but never more than one blank line and no trailing spaces.
        $beschrijving = preg_replace('/[ \t]+/', ' ', $beschrijving);
        $beschrijving = preg_replace('/[ \t]*\n/', "\n", $beschrijving);
        $beschrijving = preg_replace('/\n{3,}/', "\n\n", $beschrijving);
        $beschrijving = mb_substr(trim((string) $beschrijving), 0, 10000);

        return [
            'titel' => $titel,
            'beschrijving' => $beschrijving,
            'opties' => $this->normalizeOptions($input['opties'] ?? null, applyDashStrip: true),
        ];
    }

    /**
     * Hard guarantee for the no-em-dash rule. A dash between two digits becomes a
     * normal hyphen (keeps ranges like 100-150 readable); every other long dash,
     * which the model would only use as punctuation, becomes a comma.
     */
    private function stripLongDashes(string $text): string
    {
        // Em dash, en dash, horizontal bar, figure dash, minus sign.
        $dashes = '\x{2012}\x{2013}\x{2014}\x{2015}\x{2212}';

        $text = preg_replace('/(?<=\d)\s*[' . $dashes . ']\s*(?=\d)/u', '-', $text);
        $text = preg_replace('/\s*[' . $dashes . ']\s*/u', ', ', $text);

        // Tidy up artefacts the replacement can leave behind.
        $text = preg_replace('/\s+,/u', ',', $text);
        $text = preg_replace('/(,\s*){2,}/u', ', ', $text);
        $text = preg_replace('/,\s*\./u', '.', $text);

        return $text;
    }

    private function collapseSpaces(string $text): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    /**
     * Accepts a comma string or an array and returns a clean, deduped list.
     *
     * @return list<string>
     */
    private function normalizeOptions(mixed $opties, bool $applyDashStrip = false): array
    {
        if (is_string($opties)) {
            $opties = explode(',', $opties);
        }
        if (! is_array($opties)) {
            return [];
        }

        $out = [];
        $seen = [];
        foreach ($opties as $option) {
            if (! is_string($option) && ! is_numeric($option)) {
                continue;
            }
            $clean = $this->collapseSpaces((string) $option);
            if ($applyDashStrip) {
                $clean = $this->collapseSpaces($this->stripLongDashes($clean));
            }
            $clean = trim($clean, " .,;");
            if ($clean === '') {
                continue;
            }
            $clean = mb_substr($clean, 0, 60);
            $key = mb_strtolower($clean);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $clean;
            if (count($out) >= 40) {
                break;
            }
        }

        return $out;
    }
}
