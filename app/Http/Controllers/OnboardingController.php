<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OnboardingController extends Controller
{
    /**
     * Mark the first-login tutorial as completed for the current user so it
     * doesn't auto-open again. Idempotent.
     */
    public function complete(Request $request): Response
    {
        $user = $request->user();

        if (is_null($user->onboarding_completed_at)) {
            $user->onboarding_completed_at = now();
            $user->save();
        }

        return response()->noContent();
    }
}
