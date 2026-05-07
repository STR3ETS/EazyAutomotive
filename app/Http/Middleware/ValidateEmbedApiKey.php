<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateEmbedApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Api-Key') ?: $request->query('api_key');

        if (!$apiKey) {
            return response()->json(['error' => 'API key is vereist.'], 401);
        }

        $company = Company::where('api_key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$company) {
            return response()->json(['error' => 'Ongeldige API key.'], 403);
        }

        $request->merge(['embed_company' => $company]);

        return $next($request);
    }
}
