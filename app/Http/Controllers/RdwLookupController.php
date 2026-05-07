<?php

namespace App\Http\Controllers;

use App\Services\RdwService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RdwLookupController extends Controller
{
    public function __construct(private RdwService $rdwService) {}

    public function lookup(Request $request): JsonResponse
    {
        $request->validate(['kenteken' => 'required|string|max:8']);

        $data = $this->rdwService->fetchByKenteken($request->kenteken);

        if (!$data) {
            return response()->json(['error' => 'Kenteken niet gevonden bij RDW.'], 404);
        }

        return response()->json($this->rdwService->mapToCarAttributes($data));
    }
}
