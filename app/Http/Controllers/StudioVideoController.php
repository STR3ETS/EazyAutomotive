<?php

namespace App\Http\Controllers;

use App\Models\StudioVideo;
use App\Services\Video\FalVideoService;
use App\Services\Video\PanoramaTourService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'images' => 'nullable|array|max:9',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:20480', // 20 MB each
            'videos' => 'nullable|array|max:3',
            'videos.*' => 'mimetypes:video/mp4,video/quicktime,video/webm', // no app-side size cap; fal caps refs at ~50MB/15s
        ]);

        $imageFiles = $request->file('images', []);
        $videoFiles = $request->file('videos', []);

        try {
            @set_time_limit(300);

            $opts = [];
            if (! empty($validated['duration'])) {
                $opts['duration'] = $validated['duration'];
            }

            $imageUrls = [];
            $videoUrls = [];

            if (empty($imageFiles) && empty($videoFiles)) {
                // No media uploaded: generate purely from the text prompt.
                $result = $this->fal->generateFromText($this->buildPrompt($validated['prompt']), $opts);
            } else {
                foreach ($imageFiles as $file) {
                    $imageUrls[] = $this->fal->uploadFile(
                        (string) file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName() ?: 'image.jpg',
                        $file->getMimeType() ?: 'image/jpeg',
                    );
                }
                foreach ($videoFiles as $file) {
                    $videoUrls[] = $this->fal->uploadFile(
                        (string) file_get_contents($file->getRealPath()),
                        $file->getClientOriginalName() ?: 'clip.mp4',
                        $file->getMimeType() ?: 'video/mp4',
                    );
                }
                $opts['video_urls'] = $videoUrls;
                $result = $this->fal->generate($imageUrls, $this->buildPrompt($validated['prompt']), $opts);
            }
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Genereren mislukt: ' . $e->getMessage());
        }

        StudioVideo::create([
            'company_id' => $request->user()->company_id,
            'status' => $result['status'],
            'prompt' => $validated['prompt'],
            'model' => $this->fal->modelLabel(),
            'image_count' => count($imageUrls) + count($videoUrls),
            'request_id' => $result['request_id'],
            'result_url' => $result['result_url'],
        ]);

        $parts = [];
        if (count($imageUrls)) {
            $parts[] = count($imageUrls) . ' foto' . (count($imageUrls) === 1 ? '' : "'s");
        }
        if (count($videoUrls)) {
            $parts[] = count($videoUrls) . ' video' . (count($videoUrls) === 1 ? '' : "'s");
        }
        $bron = $parts === [] ? 'je beschrijving' : implode(' en ', $parts);

        return back()->with('success', 'Je video wordt gemaakt van ' . $bron . '. Dit duurt een paar minuten; de status ververst automatisch.');
    }

    /**
     * Maakt een soepele 360-rondkijk-video uit een equirectangular panoramafoto,
     * volledig lokaal met ffmpeg. Geen AI, geen vervorming: de bol wordt netjes
     * teruggeprojecteerd naar een recht camerabeeld dat rondpant.
     */
    public function storeTour(Request $request, PanoramaTourService $tour)
    {
        if (! $tour->isAvailable()) {
            return back()->with('error', 'De 360-tour renderer (ffmpeg) is niet beschikbaar op deze server.');
        }

        $validated = $request->validate([
            'panorama' => 'required|image|mimes:jpeg,jpg,png|max:51200', // 50 MB
            'tour_duration' => 'nullable|in:8,12,15,20',
            'direction' => 'nullable|in:left,right',
            'fov' => 'nullable|in:85,100,115',
        ]);

        $file = $request->file('panorama');

        // Een echte 360-foto is equirectangular: breedte is 2x de hoogte.
        [$w, $h] = @getimagesize($file->getRealPath()) ?: [0, 0];
        if ($w < 1000 || $h < 1 || abs(($w / max(1, $h)) - 2) > 0.2) {
            return back()->with('error', 'Dit lijkt geen 360-panorama. Upload een equirectangular foto met een 2:1 verhouding (breedte is twee keer de hoogte).');
        }

        @set_time_limit(300);

        $fov = (int) ($validated['fov'] ?? 100);
        $uuid = (string) Str::uuid();
        $dir = Storage::disk('public')->path('tours');
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $mp4 = $dir . DIRECTORY_SEPARATOR . $uuid . '.mp4';
        $thumb = $dir . DIRECTORY_SEPARATOR . $uuid . '.jpg';

        try {
            $tour->render($file->getRealPath(), $mp4, [
                'duration' => (int) ($validated['tour_duration'] ?? 12),
                'direction' => $validated['direction'] ?? 'right',
                'fov' => $fov,
            ]);
            $tour->poster($file->getRealPath(), $thumb, $fov);
        } catch (\Throwable $e) {
            report($e);
            @unlink($mp4);

            return back()->with('error', 'Tour maken mislukt: ' . $e->getMessage());
        }

        StudioVideo::create([
            'company_id' => $request->user()->company_id,
            'status' => 'completed',
            'prompt' => '360 rondkijk-tour uit panoramafoto',
            'model' => '360 tour (lokaal, ffmpeg)',
            'image_count' => 1,
            'video_url' => Storage::disk('public')->url('tours/' . $uuid . '.mp4'),
            'thumbnail_url' => is_file($thumb) ? Storage::disk('public')->url('tours/' . $uuid . '.jpg') : null,
        ]);

        return back()->with('success', 'Je 360-tour is gemaakt uit de panoramafoto.');
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

        // Lokaal gerenderde tour-bestanden meteen opruimen.
        if ($studioVideo->video_url && str_contains($studioVideo->video_url, '/storage/tours/')) {
            $base = pathinfo((string) parse_url($studioVideo->video_url, PHP_URL_PATH), PATHINFO_FILENAME);
            if ($base !== '') {
                Storage::disk('public')->delete(["tours/{$base}.mp4", "tours/{$base}.jpg"]);
            }
        }

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
