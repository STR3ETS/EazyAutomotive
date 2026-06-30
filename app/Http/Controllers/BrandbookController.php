<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\AI\FalImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandbookController extends Controller
{
    public function __construct(private FalImageService $fal) {}

    public function index(Request $request)
    {
        $company = Company::findOrFail($request->user()->company_id);

        return view('company.brandbook.index', compact('company'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'primary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font_heading' => 'nullable|string|max:60',
            'font_body' => 'nullable|string|max:60',
            'tagline' => 'nullable|string|max:140',
            'tone' => 'nullable|string|max:1500',
            'style' => 'nullable|string|max:255',
        ]);

        $company = Company::findOrFail($request->user()->company_id);
        $company->brand_settings = array_merge($company->brand_settings ?? [], $data);
        $company->save();

        return back()->with('success', 'Brandbook opgeslagen.');
    }

    /** Generate logo concepts with AI based on the brand. Returns image URLs as JSON. */
    public function generateLogo(Request $request): JsonResponse
    {
        if (! $this->fal->isConfigured()) {
            return response()->json(['error' => 'AI-beeldgeneratie is niet geconfigureerd (FAL_KEY ontbreekt).'], 422);
        }

        $data = $request->validate(['style' => 'nullable|string|max:255']);
        $company = Company::findOrFail($request->user()->company_id);

        try {
            @set_time_limit(120);
            $images = $this->fal->generate($this->buildLogoPrompt($company, $data['style'] ?? ''), 4, 'square_hd');
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['images' => $images]);
    }

    /** Save a chosen generated (or any) image URL as the company logo. */
    public function useLogo(Request $request)
    {
        $data = $request->validate(['image_url' => 'required|url']);
        $company = Company::findOrFail($request->user()->company_id);

        $resp = Http::timeout(60)->get($data['image_url']);
        if (! $resp->successful() || $resp->body() === '') {
            return back()->with('error', 'Kon de afbeelding niet ophalen.');
        }

        $ext = pathinfo((string) parse_url($data['image_url'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
        $path = 'logos/' . $company->id . '/' . Str::random(24) . '.' . $ext;
        Storage::disk('public')->put($path, $resp->body());

        if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
            Storage::disk('public')->delete($company->logo_path);
        }
        $company->update(['logo_path' => $path]);

        return back()->with('success', 'Logo ingesteld.');
    }

    private function buildLogoPrompt(Company $company, string $style): string
    {
        $primary = $company->brand('primary_color', $company->embed_settings['primary_color'] ?? '#0F9B9F');

        $prompt = 'Professional minimalist logo design for the brand "' . $company->name . '". ';
        $prompt .= $style !== '' ? trim($style) . '. ' : 'Clean modern automotive feel. ';
        $prompt .= 'A simple, iconic, memorable symbol in flat vector style, centered on a plain white background, '
            . 'using ' . $primary . ' as the main color. Crisp, high quality, balanced, no photographic detail.';

        return $prompt;
    }
}
