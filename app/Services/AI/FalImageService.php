<?php

namespace App\Services\AI;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Text-to-image generation via fal.ai (FLUX). Used for the brand-book AI logo
 * generator. The queue API is async: submit, poll, then read the result.
 */
class FalImageService
{
    public function isConfigured(): bool
    {
        return filled(config('services.fal.key'));
    }

    /**
     * Generate one or more images for a prompt. Returns the image URLs.
     *
     * @return array<int, string>
     */
    public function generate(string $prompt, int $count = 4, string $size = 'square_hd'): array
    {
        $count = max(1, min(4, $count));
        $base = rtrim((string) config('services.fal.base_url', 'https://queue.fal.run'), '/');

        $submit = $this->client()->post($base . '/fal-ai/flux/schnell', [
            'prompt' => $prompt,
            'image_size' => $size,
            'num_images' => $count,
            'num_inference_steps' => 4,
        ]);
        if (! $submit->successful()) {
            throw new \RuntimeException($this->errorMessage($submit));
        }

        $resultUrl = rtrim((string) $submit->json('response_url'), '/');
        if ($resultUrl === '') {
            throw new \RuntimeException('fal: generatie kon niet starten.');
        }

        for ($i = 0; $i < 40; $i++) {
            $status = $this->client()->get($resultUrl . '/status');
            if (! $status->successful()) {
                throw new \RuntimeException($this->errorMessage($status));
            }
            $state = strtoupper((string) $status->json('status'));

            if ($state === 'COMPLETED') {
                $res = $this->client()->get($resultUrl);
                $urls = [];
                foreach ((array) $res->json('images', []) as $img) {
                    if (! empty($img['url'])) {
                        $urls[] = $img['url'];
                    }
                }
                if ($urls === []) {
                    throw new \RuntimeException('fal: geen afbeeldingen ontvangen.');
                }

                return $urls;
            }
            if (! in_array($state, ['IN_QUEUE', 'IN_PROGRESS'], true)) {
                throw new \RuntimeException('fal: generatie mislukte.');
            }
            sleep(2);
        }

        throw new \RuntimeException('fal: generatie duurde te lang.');
    }

    private function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Key ' . config('services.fal.key'),
            'Accept' => 'application/json',
        ])->timeout(40);
    }

    private function errorMessage(Response $response): string
    {
        $body = $response->json();
        if (is_array($body)) {
            $detail = $body['detail'] ?? null;
            if (is_string($detail) && $detail !== '') {
                return 'fal: ' . $detail;
            }
            if (is_array($detail) && ! empty($detail[0]['msg'])) {
                return 'fal: ' . $detail[0]['msg'];
            }
        }
        if (in_array($response->status(), [401, 403], true)) {
            return 'fal: ongeldige of ontbrekende API-key (FAL_KEY).';
        }

        return 'fal (HTTP ' . $response->status() . ')';
    }
}
