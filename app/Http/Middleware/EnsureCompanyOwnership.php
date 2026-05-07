<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyOwnership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->company_id) {
            abort(403, 'Geen bedrijf gekoppeld aan dit account.');
        }

        // Check bound models for company_id
        foreach ($request->route()->parameters() as $param) {
            if (is_object($param) && property_exists($param, 'company_id')) {
                if ($param->company_id !== $user->company_id) {
                    abort(403, 'Geen toegang tot deze resource.');
                }
            }
        }

        return $next($request);
    }
}
