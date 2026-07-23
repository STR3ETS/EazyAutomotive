<?php

namespace App\Services\AI;

use Anthropic\Client;
use App\Models\AiActivity;

/**
 * Runs the autonomous tool-calling loop: ask Claude, execute any tool calls it
 * makes (scoped to the company), feed the results back, and repeat until it has
 * a final answer. Every mutating tool call is logged to the activity feed so the
 * owner can see and undo what the colleague did.
 */
class AiAgent
{
    public function __construct(private ToolRegistry $registry) {}

    /**
     * @param  list<array{role:string,content:mixed}>  $history  Prior turns (user/assistant text).
     * @return array{reply:string, activities:list<AiActivity>}
     */
    public function run(AgentContext $context, array $history, string $companyName): array
    {
        $client = new Client(apiKey: (string) config('ai.api_key'));

        $messages = $history;
        $tools = $this->registry->definitions();
        $system = $this->systemPrompt($companyName);

        $reply = '';
        /** @var list<AiActivity> $activities */
        $activities = [];
        $maxIterations = (int) config('ai.max_tool_iterations', 12);
        $maxTokens = (int) config('ai.max_tokens', 4096);
        $model = (string) config('ai.models.brain');

        for ($i = 0; $i < $maxIterations; $i++) {
            $message = $client->messages->create(
                maxTokens: $maxTokens,
                messages: $messages,
                model: $model,
                system: $system,
                tools: $tools,
            );

            $assistantContent = [];
            $toolUses = [];
            $turnText = '';

            foreach ($message->content as $block) {
                if ($block->type === 'text') {
                    $assistantContent[] = ['type' => 'text', 'text' => $block->text];
                    $turnText .= $block->text;
                } elseif ($block->type === 'tool_use') {
                    $assistantContent[] = [
                        'type' => 'tool_use',
                        'id' => $block->id,
                        'name' => $block->name,
                        'input' => $block->input,
                    ];
                    $toolUses[] = $block;
                }
            }

            if ($turnText !== '') {
                $reply = $turnText;
            }

            if ($assistantContent === []) {
                break;
            }

            $messages[] = ['role' => 'assistant', 'content' => $assistantContent];

            if ($message->stopReason !== 'tool_use' || $toolUses === []) {
                break;
            }

            $resultBlocks = [];
            foreach ($toolUses as $toolUse) {
                $result = $this->registry->dispatch($toolUse->name, (array) $toolUse->input, $context);

                if (! $result->isError && $result->summary !== null) {
                    $activities[] = AiActivity::create([
                        'company_id' => $context->companyId,
                        'user_id' => $context->userId,
                        'ai_conversation_id' => $context->conversationId,
                        'tool' => $toolUse->name,
                        'summary' => $result->summary,
                        'subject_type' => $result->subjectType,
                        'subject_id' => $result->subjectId,
                        'undo_data' => $result->undo,
                    ]);
                }

                $resultBlocks[] = [
                    'type' => 'tool_result',
                    'toolUseID' => $toolUse->id,
                    'content' => $result->content,
                    'isError' => $result->isError,
                ];
            }

            $messages[] = ['role' => 'user', 'content' => $resultBlocks];
        }

        return ['reply' => $reply, 'activities' => $activities];
    }

    private function systemPrompt(string $companyName): string
    {
        $today = now()->toDateString();

        return <<<PROMPT
Je bent de AI-collega van EazyAutomotive, een platform voor autohandelaren. Je werkt voor het bedrijf "{$companyName}". Vandaag is het {$today}.

Je bent een proactieve, autonome collega: je voert acties zelf uit met de beschikbare tools, zonder eerst om bevestiging te vragen. Alles wat je wijzigt wordt automatisch gelogd en is door de eigenaar met één klik terug te draaien, dus handel doortastend maar zorgvuldig.

Je kunt vrijwel alles wat een medewerker in het platform ook kan, via je tools:
- Voorraad: auto's zoeken, bekijken, toevoegen (op kenteken, RDW vult aan), wijzigen, verwijderen en statistieken opvragen.
- Advertenties: verkoopteksten schrijven en direct op een auto plaatsen.
- Taxatie: de marktwaarde van een kenteken bepalen op basis van live marktdata.
- CRM: leads zoeken en bijwerken (status, notitie), en klanten zoeken en toevoegen.
- Verkoop: koop-/verkoopovereenkomsten opstellen.
- Boekhouding: kosten inboeken (BTW wordt uitgesplitst).
- Widgets: de huisstijl van de website-widgets aanpassen (thema of hoofdkleur).

Heb je een id nodig (auto, lead, klant), gebruik dan eerst een zoek-tool. Keten tools aan elkaar om een taak volledig af te ronden (bijv. eerst klant toevoegen, dan de koopovereenkomst maken).

Werkwijze:
- Antwoord altijd in het Nederlands, kort en concreet. Geen overbodige inleidingen.
- Gebruik tools om echte gegevens op te halen of wijzigingen door te voeren. Verzin nooit gegevens of resultaten.
- Bedragen toon en ontvang je in hele euro's (niet in centen).
- Je werkt uitsluitend binnen dit bedrijf; je kunt niets van andere bedrijven zien of wijzigen.
- Voer een gevraagde taak volledig uit voordat je antwoordt. Meld daarna kort wat je hebt gedaan.
- Bij twijfel over een onomkeerbaar ogende actie: kies de meest logische interpretatie, voer uit, en benoem wat je gedaan hebt zodat de gebruiker het kan terugdraaien.
PROMPT;
    }
}
