<?php

namespace App\Services\Video;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * fal.ai client for Seedance 2.0 reference-to-video. One submit with up to nine
 * image URLs produces a single cinematic montage with native audio. The queue
 * API is asynchronous: submit returns a request id, then we poll status and
 * fetch the result. See https://fal.ai/models/bytedance/seedance-2.0
 */
class FalVideoService
{
    public function isConfigured(): bool
    {
        return filled(config('services.fal.key'));
    }

    /** The model label stored on the video row (kept short for the 40-char column). */
    public function modelLabel(): string
    {
        return 'seedance-2.0';
    }

    /**
     * Submit a reference-to-video job.
     *
     * @param  array<int, string>  $imageUrls  Publicly reachable image URLs (max 9).
     * @return array{status: string, request_id: ?string, result_url: ?string}
     */
    public function generate(array $imageUrls, string $prompt, array $opts = []): array
    {
        $body = array_filter([
            'prompt' => $prompt,
            'image_urls' => array_values(array_slice($imageUrls, 0, 9)),
            'resolution' => $opts['resolution'] ?? config('services.fal.resolution', '720p'),
            'duration' => $opts['duration'] ?? config('services.fal.duration', 'auto'),
            'aspect_ratio' => $opts['aspect_ratio'] ?? config('services.fal.aspect_ratio', '16:9'),
            'generate_audio' => $opts['generate_audio'] ?? true,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);

        $response = $this->client()->post($this->base() . '/' . $this->model(), $body);

        if (! $response->successful()) {
            throw new \RuntimeException($this->errorMessage($response));
        }

        // The queue path differs from the submit path (fal drops the sub-model
        // segments), so we keep the canonical response_url fal hands back.
        return [
            'status' => 'in_progress',
            'request_id' => $response->json('request_id'),
            'result_url' => $response->json('response_url'),
        ];
    }

    /**
     * Poll a request using the response_url fal returned at submit time. The status
     * lives at "{resultUrl}/status"; the finished payload at "{resultUrl}".
     *
     * @return array{status: string, video_url: ?string, thumbnail_url: ?string, error: ?string}
     */
    public function status(string $resultUrl): array
    {
        $resultUrl = rtrim($resultUrl, '/');

        $status = $this->client()->get($resultUrl . '/status');
        if (! $status->successful()) {
            throw new \RuntimeException($this->errorMessage($status));
        }

        $state = strtoupper((string) ($status->json('status') ?? ''));

        if ($state !== 'COMPLETED') {
            // IN_QUEUE or IN_PROGRESS: still working.
            return ['status' => 'in_progress', 'video_url' => null, 'thumbnail_url' => null, 'error' => null];
        }

        $result = $this->client()->get($resultUrl);
        if (! $result->successful()) {
            return ['status' => 'failed', 'video_url' => null, 'thumbnail_url' => null, 'error' => $this->errorMessage($result)];
        }

        $videoUrl = $result->json('video.url');
        if (! $videoUrl) {
            return ['status' => 'failed', 'video_url' => null, 'thumbnail_url' => null, 'error' => 'Geen video-URL ontvangen van fal.'];
        }

        return [
            'status' => 'completed',
            'video_url' => $videoUrl,
            'thumbnail_url' => $result->json('video.thumbnail_url'),
            'error' => null,
        ];
    }

    private function client()
    {
        return Http::withHeaders([
            'Authorization' => 'Key ' . config('services.fal.key'),
            'Accept' => 'application/json',
        ])->timeout(30);
    }

    private function base(): string
    {
        return rtrim((string) config('services.fal.base_url', 'https://queue.fal.run'), '/');
    }

    private function model(): string
    {
        return trim((string) config('services.fal.model', 'bytedance/seedance-2.0/reference-to-video'), '/');
    }

    /** Build a friendly message from a fal error response (FastAPI-style detail). */
    private function errorMessage(Response $response): string
    {
        $body = $response->json();

        if (is_array($body)) {
            $detail = $body['detail'] ?? null;
            if (is_string($detail) && $detail !== '') {
                return 'fal: ' . $detail;
            }
            if (is_array($detail)) {
                $first = $detail[0] ?? null;
                if (is_array($first) && ! empty($first['msg'])) {
                    return 'fal: ' . $first['msg'];
                }
            }
            if (! empty($body['message']) && is_string($body['message'])) {
                return 'fal: ' . $body['message'];
            }
        }

        if ($response->status() === 401 || $response->status() === 403) {
            return 'fal: ongeldige of ontbrekende API-key (controleer FAL_KEY).';
        }

        return 'fal (HTTP ' . $response->status() . ')';
    }
}
