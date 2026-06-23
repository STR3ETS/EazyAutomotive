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
You write a prompt for an image-to-video model that animates a SINGLE still photo of a parked car into a short clip. The biggest failure modes are: the car appears to drive (while the wheels do not turn and there is no driver), and the car body warps or morphs. Your prompt must prevent both.

Hard rules:
- The car is PARKED and completely stationary. Engine off. The car does not move or drive. The wheels do not turn. No driver, no people.
- ONLY the camera moves, and only subtly: a slow gentle push-in, a slow slight pan, or a small parallax. Never a 360 orbit, never fast or large camera motion (those make the model invent and warp the unseen sides of the car).
- Keep the car's exact shape, proportions and badges. No morphing, warping, melting, stretching or distortion.
- Realistic daylight and soft reflections on the paint are good. Keep it photorealistic.

Use the dealer's idea only for the mood/lighting, not for car motion. Output ONE short English prompt (max ~45 words). No preamble, no quotes.
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

        return "A {$desc} parked and completely still. The car does not move and the wheels do not turn; there is no driver. Only the camera slowly pushes in a little. Keep the exact car shape, no warping or distortion. Soft daylight, glossy reflections, photorealistic, smooth subtle motion.";
    }
}
