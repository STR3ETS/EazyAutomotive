<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarView;
use App\Services\EmbedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmbedApiController extends Controller
{
    public function __construct(private EmbedService $embedService) {}

    public function cars(Request $request): JsonResponse
    {
        $apiKey = $request->header('X-Api-Key') ?: $request->query('api_key');

        if (!$apiKey) {
            return response()->json(['error' => 'API key is vereist.'], 401);
        }

        $result = $this->embedService->getCarsForEmbed($apiKey, $request->only([
            'merk', 'brandstof', 'prijs_max', 'bouwjaar_min', 'page',
        ]));

        if (!$result) {
            return response()->json(['error' => 'Ongeldige API key.'], 403);
        }

        return response()->json($result);
    }

    public function show(Request $request, int $carId): JsonResponse
    {
        $company = $request->get('embed_company');

        if (!$company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $car = $this->embedService->getCarDetail($company, $carId);

        if (!$car) {
            return response()->json(['error' => 'Auto niet gevonden.'], 404);
        }

        return response()->json(['car' => $car]);
    }

    public function trackView(Request $request, int $carId): JsonResponse
    {
        $company = $request->get('embed_company');

        if (!$company) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only track if the car belongs to this company
        $carExists = $company->cars()->where('id', $carId)->exists();

        if ($carExists) {
            CarView::create([
                'car_id' => $carId,
                'company_id' => $company->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->header('referer'),
                'source' => 'embed',
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
