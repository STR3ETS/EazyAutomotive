<?php

namespace App\Http\Controllers;

use App\Models\StudioVideo;
use App\Services\Video\FalVideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Free-form AI video studio: upload any photos (houses, products, anything) plus
 * a prompt, and fal Seedance 2.0 turns them into a cinematic montage. Decoupled
 * from cars. Photos are uploaded straight to fal storage; nothing is kept locally.
 */
class StudioVideoController extends Controller
{
    public function __construct(private FalVideoService $fal) {}

    public function index(Request $request)
    {
        $videos = StudioVideo::where('company_id', $request->user()->company_id)
            ->latest()
            ->take(30)
            ->get();

        return view('company.studio.index', compact('videos'));
    }

    public function store(Request $request)
    {
        if (! $this->fal->isConfigured()) {
            return back()->with('error', 'Videogeneratie is nog niet geconfigureerd (FAL_KEY ontbreekt).');
        }

        $validated = $request->validate([
            'prompt' => 'required|string|max:1500',
            'duration' => 'nullable|in:5,8,10,15', // model max is 15 seconds per render
            'images' => 'required|array|min:1|max:9',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:20480', // 20 MB each
        ]);

        try {
            @set_time_limit(180);

            $falUrls = [];
            foreach ($request->file('images') as $file) {
                $falUrls[] = $this->fal->uploadImage(
                    (string) file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName() ?: 'image.jpg',
                    $file->getMimeType() ?: 'image/jpeg',
                );
            }

            $opts = empty($validated['duration']) ? [] : ['duration' => $validated['duration']];
            $result = $this->fal->generate($falUrls, $this->buildPrompt($validated['prompt']), $opts);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Genereren mislukt: ' . $e->getMessage());
        }

        StudioVideo::create([
            'company_id' => $request->user()->company_id,
            'status' => $result['status'],
            'prompt' => $validated['prompt'],
            'model' => $this->fal->modelLabel(),
            'image_count' => count($falUrls),
            'request_id' => $result['request_id'],
            'result_url' => $result['result_url'],
        ]);

        $count = count($falUrls);

        return back()->with('success', "Je video wordt gemaakt van {$count} foto('s). Dit duurt een paar minuten; de status ververst automatisch.");
    }

    public function status(Request $request, StudioVideo $studioVideo): JsonResponse
    {
        abort_unless($studioVideo->company_id === $request->user()->company_id, 403);

        if ($studioVideo->isPending() && $studioVideo->result_url) {
            try {
                $s = $this->fal->status($studioVideo->result_url);
                $studioVideo->update([
                    'status' => $s['status'],
                    'video_url' => $s['video_url'] ?? $studioVideo->video_url,
                    'thumbnail_url' => $s['thumbnail_url'] ?? $studioVideo->thumbnail_url,
                    'error' => $s['error'],
                ]);
            } catch (\Throwable $e) {
                report($e);

                return response()->json([
                    'status' => $studioVideo->status,
                    'pending' => $studioVideo->isPending(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status' => $studioVideo->status,
            'pending' => $studioVideo->isPending(),
            'video_url' => $studioVideo->video_url,
            'thumbnail_url' => $studioVideo->thumbnail_url,
            'error' => $studioVideo->error,
        ]);
    }

    public function destroy(Request $request, StudioVideo $studioVideo)
    {
        abort_unless($studioVideo->company_id === $request->user()->company_id, 403);

        $studioVideo->delete();

        return back()->with('success', 'Video verwijderd.');
    }

    /** The studio uses the user's own prompt, with a light cinematic quality baseline. */
    private function buildPrompt(string $idea): string
    {
        return trim($idea)
            . ' Photorealistic, cinematic camera movement, smooth gliding motion, shallow depth of field, color graded, high detail. No warping or distortion.';
    }
}
