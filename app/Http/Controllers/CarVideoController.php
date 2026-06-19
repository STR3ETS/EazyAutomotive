<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarReel;
use App\Models\CarVideo;
use App\Services\Video\HiggsfieldService;
use App\Services\Video\VideoPromptComposer;
use App\Services\Video\VideoStitcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarVideoController extends Controller
{
    public function __construct(
        private HiggsfieldService $higgsfield,
        private VideoPromptComposer $composer,
    ) {}

    /** Kick off an AI promo-video generation for a car, from its photo. */
    public function store(Request $request, Car $car)
    {
        abort_unless($car->company_id === $request->user()->company_id, 403);

        if (! $this->higgsfield->isConfigured()) {
            return back()->with('error', 'Videogeneratie is nog niet geconfigureerd (HIGGSFIELD_CREDENTIALS ontbreekt).');
        }

        $validated = $request->validate([
            'prompt' => 'required|string|max:1000',
            'car_image_ids' => 'nullable|array',
            'car_image_ids.*' => 'integer',
            'image_url' => 'nullable|url|max:1024', // local-dev override (Higgsfield cannot reach localhost photos)
        ]);

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

        // Turn the dealer's short idea into a rich cinematic prompt (once, reused per photo).
        $cinematicPrompt = $this->composer->compose($car, $validated['prompt']);
        $model = (string) config('services.higgsfield.model', 'dop-turbo');

        $created = 0;
        $lastError = null;

        foreach ($imageUrls as $imageUrl) {
            try {
                $result = $this->higgsfield->generate($imageUrl, $cinematicPrompt);
            } catch (\Throwable $e) {
                report($e);
                $lastError = $e->getMessage();

                continue;
            }

            $car->videos()->create([
                'company_id' => $car->company_id,
                'status' => $result['status'],
                'prompt' => $validated['prompt'],
                'model' => $model,
                'source_image_url' => $imageUrl,
                'request_id' => $result['request_id'],
            ]);
            $created++;
        }

        if ($created === 0) {
            return back()->with('error', 'Genereren mislukt: ' . ($lastError ?? 'onbekende fout'));
        }

        $message = $created === 1
            ? 'De video wordt gegenereerd. Dit kan een paar minuten duren; de status ververst automatisch.'
            : "Er worden {$created} video's gegenereerd (1 per foto). Dit kan een paar minuten duren; de status ververst automatisch.";

        if ($lastError !== null) {
            $message .= ' Let op: een of meer foto\'s konden niet worden verwerkt.';
        }

        return back()->with('success', $message);
    }

    /** Poll Higgsfield for a pending video and return the current state as JSON. */
    public function status(Request $request, Car $car, CarVideo $video): JsonResponse
    {
        abort_unless($car->company_id === $request->user()->company_id, 403);
        abort_unless($video->car_id === $car->id, 404);

        if ($video->isPending() && $video->request_id) {
            try {
                $s = $this->higgsfield->status($video->request_id);
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
