<?php

namespace App\Services\Video;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Thin client for the official Higgsfield API (platform.higgsfield.ai). Powers
 * the per-car promo video: image-to-video via the cinematic "DoP" model.
 *
 * Auth is "Authorization: Key KEY_ID:KEY_SECRET". Generation is asynchronous:
 * generate() returns a request_id, and status() polls /requests/{id}/status
 * until the video is ready.
 *
 * The exact request body and response shapes are kept in one place
 * (buildPayload / extract*) and parsed defensively, so the integration is easy
 * to adjust against a specific account if Higgsfield tweaks field names.
 */
class HiggsfieldService
{
    public function isConfigured(): bool
    {
        return ! empty(config('services.higgsfield.credentials'));
    }

    /**
     * Kick off an image-to-video generation. Returns the Higgsfield request id
     * and the initial status.
     *
     * @return array{request_id:string, status:string}
     *
     * @throws \RuntimeException on a transport or API error.
     */
    public function generate(string $imageUrl, string $prompt, ?string $motionId = null): array
    {
        [$endpoint, $payload] = $this->buildRequest($imageUrl, $prompt, $motionId);

        $resp = $this->client()->post($endpoint, $payload);

        if (! $resp->successful()) {
            throw new \RuntimeException($this->errorMessage($resp));
        }

        $data = $resp->json() ?? [];
        $requestId = $this->extractRequestId($data);

        if (! $requestId) {
            throw new \RuntimeException('Higgsfield gaf geen request-id terug.');
        }

        return [
            'request_id' => $requestId,
            'status' => $this->normalizeStatus($data['status'] ?? 'queued'),
        ];
    }

    /**
     * Kick off a DoP "first-last-frame" generation: the model creates the motion
     * between two real photos (e.g. front -> side), so both ends are real frames
     * and only the in-between is generated. Used to build a walk-around montage
     * from consecutive photo pairs.
     *
     * @return array{request_id:string, status:string}
     *
     * @throws \RuntimeException on a transport or API error.
     */
    public function generateTransition(string $firstUrl, string $lastUrl, string $prompt): array
    {
        $variant = (string) config('services.higgsfield.flf_variant', 'turbo');

        $body = [
            'prompt' => $prompt,
            'image_url' => $firstUrl,
            'end_image_url' => $lastUrl,
            'motions' => [],
        ];

        if (config('services.higgsfield.enhance_prompt', true)) {
            $body['enhance_prompt'] = true;
        }

        $resp = $this->client()->post('/higgsfield-ai/dop/' . $variant . '/first-last-frame', $body);

        if (! $resp->successful()) {
            throw new \RuntimeException($this->errorMessage($resp));
        }

        $data = $resp->json() ?? [];
        $requestId = $this->extractRequestId($data);

        if (! $requestId) {
            throw new \RuntimeException('Higgsfield gaf geen request-id terug.');
        }

        return [
            'request_id' => $requestId,
            'status' => $this->normalizeStatus($data['status'] ?? 'queued'),
        ];
    }

    /**
     * Poll the status of a generation.
     *
     * @return array{status:string, video_url:?string, thumbnail_url:?string, error:?string}
     *
     * @throws \RuntimeException on a transport or API error.
     */
    public function status(string $requestId): array
    {
        $resp = $this->client()->get('/requests/' . urlencode($requestId) . '/status');

        if (! $resp->successful()) {
            throw new \RuntimeException($this->errorMessage($resp));
        }

        $data = $resp->json() ?? [];
        $status = $this->normalizeStatus($data['status'] ?? ($data['jobs'][0]['status'] ?? 'in_progress'));

        return [
            'status' => $status,
            'video_url' => $this->extractVideoUrl($data),
            'thumbnail_url' => $this->extractThumbnailUrl($data),
            'error' => $status === 'failed'
                ? (is_string($data['error'] ?? null) ? $data['error'] : ($data['message'] ?? 'De generatie is mislukt.'))
                : null,
        ];
    }

    // ── API contract (isolated, defensive) ───────────────────────────

    /**
     * Builds the right endpoint and payload for the configured model. The API
     * exposes one image2video endpoint per provider, each with its own image
     * field: DoP uses `input_images` (an array, max 1), while Seedance and Kling
     * use a single `input_image`. All generation params are wrapped under `params`.
     *
     * @return array{0:string, 1:array<string,mixed>}
     */
    private function buildRequest(string $imageUrl, string $prompt, ?string $motionId): array
    {
        $model = (string) config('services.higgsfield.model', 'seedance_lite');
        $provider = $this->providerFor($model);
        $image = ['type' => 'image_url', 'image_url' => $imageUrl];

        if ($provider === 'dop') {
            $params = [
                'model' => $model,
                'prompt' => $prompt,
                'input_images' => [$image],
            ];

            if (config('services.higgsfield.enhance_prompt', true)) {
                $params['enhance_prompt'] = true;
            }
            if ($motionId) {
                $params['motion_id'] = $motionId;
            }

            return ['/v1/image2video/dop', ['params' => $params]];
        }

        // Seedance / Kling: a single input_image.
        return ['/v1/image2video/' . $provider, ['params' => [
            'model' => $model,
            'prompt' => $prompt,
            'input_image' => $image,
        ]]];
    }

    private function providerFor(string $model): string
    {
        if (str_starts_with($model, 'dop')) {
            return 'dop';
        }
        if (str_starts_with($model, 'kling')) {
            return 'kling';
        }

        return 'seedance';
    }

    /**
     * @param array<string,mixed> $d
     */
    private function extractRequestId(array $d): ?string
    {
        foreach (['request_id', 'generation_id', 'id'] as $key) {
            if (! empty($d[$key]) && is_string($d[$key])) {
                return $d[$key];
            }
        }

        if (! empty($d['jobs'][0]['id']) && is_string($d['jobs'][0]['id'])) {
            return $d['jobs'][0]['id'];
        }

        if (! empty($d['data']) && is_array($d['data'])) {
            return $this->extractRequestId($d['data']);
        }

        return null;
    }

    /**
     * @param array<string,mixed> $d
     */
    private function extractVideoUrl(array $d): ?string
    {
        return $this->firstUrl([
            $d['jobs'][0]['results']['raw']['url'] ?? null,
            $d['results']['raw']['url'] ?? null,
            $d['video']['url'] ?? null,
            $d['result']['url'] ?? null,
            is_string($d['output'] ?? null) ? $d['output'] : null,
            is_string($d['video_url'] ?? null) ? $d['video_url'] : null,
        ]);
    }

    /**
     * @param array<string,mixed> $d
     */
    private function extractThumbnailUrl(array $d): ?string
    {
        return $this->firstUrl([
            $d['jobs'][0]['results']['min']['url'] ?? null,
            $d['results']['min']['url'] ?? null,
            $d['thumbnail']['url'] ?? null,
            is_string($d['thumbnail_url'] ?? null) ? $d['thumbnail_url'] : null,
        ]);
    }

    /**
     * @param array<int,mixed> $candidates
     */
    private function firstUrl(array $candidates): ?string
    {
        foreach ($candidates as $c) {
            if (is_string($c) && str_starts_with($c, 'http')) {
                return $c;
            }
        }

        return null;
    }

    private function normalizeStatus(?string $status): string
    {
        $s = strtolower((string) $status);

        return match (true) {
            in_array($s, ['completed', 'complete', 'success', 'succeeded', 'done'], true) => 'completed',
            in_array($s, ['failed', 'error', 'nsfw', 'canceled', 'cancelled', 'rejected'], true) => 'failed',
            in_array($s, ['in_progress', 'in-progress', 'processing', 'running', 'started'], true) => 'in_progress',
            default => 'queued',
        };
    }

    private function client()
    {
        $scheme = trim((string) config('services.higgsfield.auth_scheme', 'Key'));

        return Http::baseUrl(rtrim((string) config('services.higgsfield.base_url', 'https://platform.higgsfield.ai'), '/'))
            ->withHeaders([
                'Authorization' => trim($scheme . ' ' . (string) config('services.higgsfield.credentials')),
                'Accept' => 'application/json',
            ])
            ->timeout(30);
    }

    private function errorMessage(Response $resp): string
    {
        $status = $resp->status();
        $body = $resp->json();
        $detail = is_array($body)
            ? ($body['error'] ?? $body['message'] ?? ($body['detail'] ?? null))
            : null;

        // FastAPI validation errors arrive as a list of {loc, msg}; summarise them.
        if (is_array($detail)) {
            $parts = [];
            foreach ($detail as $item) {
                if (is_array($item) && isset($item['msg'])) {
                    $loc = isset($item['loc']) && is_array($item['loc']) ? implode('.', $item['loc']) : '';
                    $parts[] = trim(($loc ? $loc . ': ' : '') . $item['msg']);
                }
            }
            $detail = $parts ? implode('; ', $parts) : json_encode($detail);
        }

        // Friendly hints for the common account-side cases.
        if ($status === 403 && is_string($detail) && stripos($detail, 'credit') !== false) {
            return 'Je Higgsfield-account heeft te weinig credits voor videogeneratie. Vul je credits aan in je Higgsfield-dashboard.';
        }

        if (in_array($status, [401, 403], true) && (! $detail || stripos((string) $detail, 'credential') !== false)) {
            return 'Higgsfield accepteert de API-sleutel niet. Controleer HIGGSFIELD_CREDENTIALS (formaat KEY_ID:KEY_SECRET).';
        }

        return 'Higgsfield (HTTP ' . $status . '): ' . ($detail ?: 'onbekende fout');
    }
}
