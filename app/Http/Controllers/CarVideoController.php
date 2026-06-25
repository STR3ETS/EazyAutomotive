<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarImage;
use App\Models\CarReel;
use App\Models\CarVideo;
use App\Services\Video\FalVideoService;
use App\Services\Video\HiggsfieldService;
use App\Services\Video\VideoPromptComposer;
use App\Services\Video\VideoStitcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CarVideoController extends Controller
{
    public function __construct(
        private FalVideoService $fal,
        private HiggsfieldService $higgsfield,
        private VideoPromptComposer $composer,
    ) {}

    /** Kick off an AI promo-video generation for a car, from its photo. */
    public function store(Request $request, Car $car)
    {
        abort_unless($car->company_id === $request->user()->company_id, 403);

        $validated = $request->validate([
            'prompt' => 'required|string|max:1000',
            'duration' => 'nullable|in:5,8,10,15', // model max is 15 seconds per render
            'car_image_ids' => 'nullable|array',
            'car_image_ids.*' => 'integer',
            'image_url' => 'nullable|url|max:1024', // local-dev override (the API cannot reach localhost photos)
        ]);

        // Preferred path: fal.ai Seedance 2.0 builds one cinematic montage with
        // native audio from all selected photos in a single call.
        if ($this->fal->isConfigured()) {
            return $this->storeFal($request, $car, $validated);
        }

        if (! $this->higgsfield->isConfigured()) {
            return back()->with('error', 'Videogeneratie is nog niet geconfigureerd (FAL_KEY of HIGGSFIELD_CREDENTIALS ontbreekt).');
        }

        // DoP accepts one photo per generation, so we generate one clip per chosen
        // photo. Resolve the source photos: a manual URL (local testing), the
        // selected car photos, or the primary/first photo. Car photos are resolved
        // through the relation so only this car's images can be used.
        $imageUrls = [];

        if (! empty($validated['image_url'])) {
            $imageUrls = [$validated['image_url']];
        } elseif (! empty($validated['car_image_ids'])) {
            $imageUrls = $car->images()->whereKey($validated['car_image_ids'])->get()
                ->map(fn ($img) => $img->url)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if ($imageUrls === []) {
            $fallback = optional($car->primaryImage)->url ?? optional($car->images()->first())->url;
            $imageUrls = $fallback ? [$fallback] : [];
        }

        if ($imageUrls === []) {
            return back()->with('error', 'Deze auto heeft nog geen foto. Voeg eerst een foto toe of geef een afbeeldings-URL op.');
        }

        $flfVariant = (string) config('services.higgsfield.flf_variant', 'turbo');
        $isManualSingle = ! empty($validated['image_url']);

        // With 2+ photos: build a walk-around montage using first-last-frame clips
        // between consecutive photos. Both ends of each clip are real frames, so it
        // warps far less than animating a single still.
        if (! $isManualSingle && count($imageUrls) >= 2) {
            $prompt = 'Smooth cinematic camera movement around the ' . $car->display_title
                . ', as if slowly walking around the car. The car stays parked and still, '
                . 'premium automotive commercial look, photorealistic, no distortion or warping.';

            $created = 0;
            $lastError = null;
            $count = count($imageUrls);

            for ($i = 0; $i < $count - 1; $i++) {
                try {
                    $result = $this->higgsfield->generateTransition($imageUrls[$i], $imageUrls[$i + 1], $prompt);
                } catch (\Throwable $e) {
                    report($e);
                    $lastError = $e->getMessage();

                    continue;
                }

                $car->videos()->create([
                    'company_id' => $car->company_id,
                    'status' => $result['status'],
                    'prompt' => $prompt,
                    'model' => 'dop-' . $flfVariant . '-flf',
                    'source_image_url' => $imageUrls[$i],
                    'request_id' => $result['request_id'],
                ]);
                $created++;
            }

            if ($created === 0) {
                return back()->with('error', 'Genereren mislukt: ' . ($lastError ?? 'onbekende fout'));
            }

            $message = "Er worden {$created} overgangsclips gemaakt tussen je foto's (de camera loopt om de auto). "
                . 'Zodra ze klaar zijn, klik je op "Combineer tot een reel" voor een vloeiende video.';

            if ($lastError !== null) {
                $message .= ' Let op: een of meer paren konden niet worden verwerkt.';
            }

            return back()->with('success', $message);
        }

        // One photo (or a manual test URL): a single-image clip.
        $cinematicPrompt = $this->composer->compose($car, $validated['prompt']);
        $model = (string) config('services.higgsfield.model', 'dop-turbo');

        try {
            $result = $this->higgsfield->generate($imageUrls[0], $cinematicPrompt);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Genereren mislukt: ' . $e->getMessage());
        }

        $car->videos()->create([
            'company_id' => $car->company_id,
            'status' => $result['status'],
            'prompt' => $validated['prompt'],
            'model' => $model,
            'source_image_url' => $imageUrls[0],
            'request_id' => $result['request_id'],
        ]);

        return back()->with('success', 'De video wordt gegenereerd. Dit kan een paar minuten duren; de status ververst automatisch.');
    }

    /**
     * fal.ai path: one Seedance 2.0 call turns up to nine photos into a single
     * cinematic montage with native audio. No per-photo clips, no stitching.
     */
    private function storeFal(Request $request, Car $car, array $validated)
    {
        // Collect the source photos as raw bytes. fal's network often cannot reach
        // a dealer's server, so we upload the bytes to fal storage first and feed
        // the model fal-hosted URLs instead of our own.
        $sources = [];

        if (! empty($validated['image_url'])) {
            $resp = Http::timeout(30)->get($validated['image_url']);
            if ($resp->successful()) {
                $sources[] = [
                    'url' => $validated['image_url'],
                    'bytes' => $resp->body(),
                    'name' => 'image.jpg',
                    'type' => $resp->header('Content-Type') ?: 'image/jpeg',
                ];
            }
        } else {
            $images = ! empty($validated['car_image_ids'])
                ? $car->images()->whereKey($validated['car_image_ids'])->get()
                : $car->images()->get();

            foreach ($images as $img) {
                $bytes = $this->readImageBytes($img);
                if ($bytes !== null) {
                    $sources[] = [
                        'url' => $img->url,
                        'bytes' => $bytes,
                        'name' => basename($img->path),
                        'type' => $this->contentTypeFor($img->path),
                    ];
                }
            }
        }

        if ($sources === []) {
            return back()->with('error', 'Deze auto heeft nog geen leesbare foto. Voeg eerst een foto toe of geef een afbeeldings-URL op.');
        }

        $sources = array_slice($sources, 0, 9);

        try {
            @set_time_limit(180);
            $falUrls = array_map(
                fn ($s) => $this->fal->uploadImage($s['bytes'], $s['name'], $s['type']),
                $sources
            );
            $opts = empty($validated['duration']) ? [] : ['duration' => $validated['duration']];
            $result = $this->fal->generate($falUrls, $this->buildFalPrompt($car, $validated['prompt']), $opts);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Genereren mislukt: ' . $e->getMessage());
        }

        $car->videos()->create([
            'company_id' => $car->company_id,
            'status' => $result['status'],
            'prompt' => $validated['prompt'],
            'model' => $this->fal->modelLabel(),
            'source_image_url' => $sources[0]['url'],
            'request_id' => $result['request_id'],
            'result_url' => $result['result_url'],
        ]);

        $count = count($sources);

        return back()->with('success', "Je cinematische video wordt gemaakt van {$count} foto('s), inclusief muziek. Dit kan een paar minuten duren; de status ververst automatisch.");
    }

    /** Read a car image's raw bytes from the public disk (null if unreadable). */
    private function readImageBytes(CarImage $img): ?string
    {
        try {
            $disk = Storage::disk('public');

            return $disk->exists($img->path) ? $disk->get($img->path) : null;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    private function contentTypeFor(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => 'image/jpeg',
        };
    }

    /** Compose a strong cinematic prompt for Seedance, augmented with the user's idea. */
    private function buildFalPrompt(Car $car, string $idea): string
    {
        $prompt = 'Cinematic ' . $car->display_title . ' commercial. '
            . 'Open on a dramatic exterior reveal at golden hour, camera dolly-in along the glossy paint. '
            . 'Cut to a smooth sweep across the interior and the glowing dashboard, then a tight detail shot of the alloy wheel, '
            . 'ending on a hero wide shot of the car. '
            . 'Use the provided photos as the exact car and keep its shape, color and details consistent. '
            . 'Premium automotive commercial, photorealistic, reflections on the paint, shallow depth of field, color graded, '
            . '24fps film look. The car stays parked and still; only the camera moves. '
            . 'Subtle modern instrumental background music, no vocals or speech.';

        $idea = trim($idea);
        if ($idea !== '') {
            $prompt .= ' Style direction: ' . $idea;
        }

        return $prompt;
    }

    /** Poll the provider for a pending video and return the current state as JSON. */
    public function status(Request $request, Car $car, CarVideo $video): JsonResponse
    {
        abort_unless($car->company_id === $request->user()->company_id, 403);
        abort_unless($video->car_id === $car->id, 404);

        $isFal = str_contains((string) $video->model, 'seedance');

        if ($video->isPending() && (($isFal && $video->result_url) || (! $isFal && $video->request_id))) {
            try {
                $s = $isFal
                    ? $this->fal->status((string) $video->result_url)
                    : $this->higgsfield->status($video->request_id);
                $video->update([
                    'status' => $s['status'],
                    'video_url' => $s['video_url'] ?? $video->video_url,
                    'thumbnail_url' => $s['thumbnail_url'] ?? $video->thumbnail_url,
                    'error' => $s['error'],
                ]);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'status' => $video->status,
                    'pending' => $video->isPending(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status' => $video->status,
            'pending' => $video->isPending(),
            'video_url' => $video->video_url,
            'thumbnail_url' => $video->thumbnail_url,
            'error' => $video->error,
        ]);
    }

    public function destroy(Request $request, Car $car, CarVideo $video)
    {
        abort_unless($car->company_id === $request->user()->company_id, 403);
        abort_unless($video->car_id === $car->id, 404);

        $video->delete();

        return back()->with('success', 'Video verwijderd.');
    }

    /** Combine this car's completed clips into one stitched reel via ffmpeg. */
    public function stitch(Request $request, Car $car, VideoStitcher $stitcher)
    {
        abort_unless($car->company_id === $request->user()->company_id, 403);

        if (! $stitcher->isAvailable()) {
            return back()->with('error', 'Samenvoegen is niet beschikbaar: ffmpeg is niet geconfigureerd op de server.');
        }

        $clips = $car->videos()
            ->where('status', 'completed')
            ->whereNotNull('video_url')
            ->orderBy('id')
            ->take(12)
            ->get();

        if ($clips->count() < 2) {
            return back()->with('error', 'Je hebt minstens twee afgeronde clips nodig om een reel te maken.');
        }

        $filename = bin2hex(random_bytes(8)) . '.mp4';
        $relativePath = "cars/{$car->id}/reels/{$filename}";

        $reel = $car->reels()->create([
            'company_id' => $car->company_id,
            'status' => 'processing',
            'clip_count' => $clips->count(),
        ]);

        try {
            @set_time_limit(0);
            $stitcher->stitch($clips->pluck('video_url')->all(), storage_path('app/public/' . $relativePath));
        } catch (\Throwable $e) {
            report($e);
            $reel->update(['status' => 'failed', 'error' => $e->getMessage()]);

            return back()->with('error', 'Samenvoegen mislukt: ' . $e->getMessage());
        }

        $reel->update(['status' => 'completed', 'path' => $relativePath]);

        return back()->with('success', "Reel gemaakt van {$clips->count()} clips.");
    }

    public function destroyReel(Request $request, Car $car, CarReel $reel)
    {
        abort_unless($car->company_id === $request->user()->company_id, 403);
        abort_unless($reel->car_id === $car->id, 404);

        if ($reel->path) {
            Storage::disk('public')->delete($reel->path);
        }
        $reel->delete();

        return back()->with('success', 'Reel verwijderd.');
    }
}
