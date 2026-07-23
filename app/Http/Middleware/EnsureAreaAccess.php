<?php

namespace App\Http\Middleware;

use App\Support\Roles;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces the function-based roles: every request is mapped to a functional
 * area (from its route name) and blocked when the signed-in user's role does
 * not grant that area. Routes without an area (dashboard, profiel, AI) are open
 * to every team member.
 */
class EnsureAreaAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $area = Roles::areaForRoute($request->route()?->getName());

        if ($user && $area !== null && ! $user->hasArea($area)) {
            abort(403, 'Je hebt met de rol ' . $user->roleLabel() . ' geen toegang tot dit onderdeel. Vraag een beheerder om toegang.');
        }

        return $next($request);
    }
}
