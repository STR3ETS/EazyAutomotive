<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarVideo;
use App\Services\Video\HiggsfieldService;
use App\Services\Video\VideoPromptComposer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'car_image_id' => 'nullable|integer',
            'image_url' => 'nullable|url|max:1024', // local-dev override (Higgsfield cannot reach localhost photos)
        ]);

        // Resolve the source photo: a manual URL (local testing), a chosen car
        // photo, or the primary/first photo. Car photos are resolved through the
        // relation so only this car's images can be used.
        $imageUrl = ! empty($validated['image_url']) ? $validated['image_url'] : null;

        if (! $imageUrl && ! empty($validated['car_image_id'])) {
            $imageUrl = optional($car->images()->whereKey($validated['car_image_id'])->first())->url;
        }

        $imageUrl ??= optional($car->primaryImage)->url ?? optional($car->images()->first())->url;

        if (! $imageUrl) {
            return back()->with('error', 'Deze auto heeft nog geen foto. Voeg eerst een foto toe of geef een afbeeldings-URL op.');
        }

        // Turn the dealer's short idea into a rich cinematic prompt before sending.
        $cinematicPrompt = $this->composer->compose($car, $validated['prompt']);

        try {
            $result = $this->higgsfield->generate($imageUrl, $cinematicPrompt);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Genereren mislukt: ' . $e->getMessage());
        }

        $car->videos()->create([
            'company_id' => $car->company_id,
            'status' => $result['status'],
            'prompt' => $validated['prompt'],
            'model' => (string) config('services.higgsfield.model', 'dop-turbo'),
            'source_image_url' => $imageUrl,
            'request_id' => $result['request_id'],
        ]);

        return back()->with('success', 'De video wordt gegenereerd. Dit kan een paar minuten duren; ververs de status.');
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
}
