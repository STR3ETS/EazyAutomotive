<?php

namespace App\Http\Controllers;

use App\Services\AI\AiDesignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiDesignController extends Controller
{
    public function __construct(private AiDesignService $service) {}

    /**
     * Generate one universal widget design (subset of embed_settings) covering
     * the card and the detail page from a prompt and an optional brand URL.
     * Returns the settings as JSON so the Ontwerpen page can apply them to the
     * live preview; the dealer still presses save (which re-validates) to persist.
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prompt' => 'required|string|max:1000',
            'url' => 'nullable|url|max:255',
        ]);

        if (empty(config('ai.api_key'))) {
            return response()->json([
                'error' => 'De AI is nog niet geconfigureerd (ANTHROPIC_API_KEY ontbreekt).',
            ], 422);
        }

        try {
            $result = $this->service->generate(
                $validated['prompt'],
                $validated['url'] ?? null,
            );
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 'Genereren mislukt: ' . $e->getMessage()], 500);
        }

        if (empty($result['settings'])) {
            return response()->json([
                'error' => 'De AI kon geen ontwerp maken. Probeer een duidelijkere omschrijving.',
            ], 422);
        }

        return response()->json($result);
    }
}
