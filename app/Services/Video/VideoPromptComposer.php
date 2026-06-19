<?php

namespace App\Services\Video;

use Anthropic\Client;
use App\Models\Car;

/**
 * Turns a dealer's short idea ("orbit rond de auto") into a rich, directed
 * cinematic prompt for Higgsfield DoP, weaving in the car's details. This is
 * the main quality lever: DoP responds far better to a detailed camera/lighting
 * description than to a one-liner. Uses the cheap Claude model; falls back to a
 * strong template when AI is not configured or the call fails.
 */
class VideoPromptComposer
{
    public function compose(Car $car, string $idea): string
    {
        $idea = trim($idea);

        if (empty(config('ai.api_key'))) {
            return $this->fallback($car, $idea);
        }

        try {
            $client = new Client(apiKey: (string) config('ai.api_key'));

            $message = $client->messages->create(
                maxTokens: 400,
                model: (string) config('ai.models.cheap', 'claude-haiku-4-5'),
                system: $this->system(),
                messages: [[
                    'role' => 'user',
                    'content' => $this->userMessage($car, $idea),
                ]],
            );

            $text = '';
            foreach ($message->content as $block) {
                if ($block->type === 'text') {
                    $text .= $block->text;
                }
            }
            $text = trim($text);

            return $text !== '' ? $text : $this->fallback($car, $idea);
        } catch (\Throwable $e) {
            report($e);

            return $this->fallback($car, $idea);
        }
    }

    private function system(): string
    {
        return <<<PROMPT
You are a cinematographer writing prompts for Higgsfield DoP, an image-to-video model that animates a single still photo of a car into a short, high-end cinematic clip.

Write ONE vivid English prompt (max ~70 words) that turns the dealer's idea into a premium automotive commercial shot. Focus on:
- Camera movement (slow orbit, dolly-in, crane up, FPV sweep, parallax push) - this is what the model actually animates.
- Lighting and mood (golden hour, studio rim light, neon reflections, moody shadows).
- Cinematic finish (shallow depth of field, glossy reflections on the paint, smooth motion, photorealistic, premium feel).

Do not re-describe the car's shape or invent a different car; the photo already defines it. Do not add text, logos, extra people, or spinning wheels. Output only the prompt itself: no preamble, no quotes, no explanation.
PROMPT;
    }

    private function userMessage(Car $car, string $idea): string
    {
        $desc = trim("{$car->merk} {$car->handelsbenaming}");

        $bits = array_filter([
            $desc !== '' ? "Car: {$desc}" : null,
            $car->bouwjaar ? "Year: {$car->bouwjaar}" : null,
            $car->eerste_kleur ? "Colour: {$car->eerste_kleur}" : null,
            $car->inrichting ? "Body: {$car->inrichting}" : null,
        ]);

        $context = implode('. ', $bits);
        $idea = $idea !== '' ? $idea : 'a slow cinematic reveal of the car';

        return ($context !== '' ? $context . ".\n" : '') . "Dealer's idea: {$idea}\n\nWrite the cinematic video prompt.";
    }

    private function fallback(Car $car, string $idea): string
    {
        $desc = trim("{$car->merk} {$car->handelsbenaming}") ?: 'car';
        $idea = $idea !== '' ? $idea : 'slow cinematic reveal';

        return "Cinematic automotive commercial of a {$desc}: {$idea}. Smooth professional camera movement, dramatic lighting with glossy reflections on the paint, shallow depth of field, premium and dynamic, photorealistic, high detail.";
    }
}
