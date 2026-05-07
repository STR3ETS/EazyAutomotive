<?php

use App\Http\Controllers\Api\EmbedApiController;
use Illuminate\Support\Facades\Route;

// Public embed API - authenticated via API key header/query parameter
Route::prefix('embed/v1')->middleware('embed.api')->group(function () {
    Route::get('/cars', [EmbedApiController::class, 'cars']);
    Route::get('/cars/{car}', [EmbedApiController::class, 'show']);
    Route::post('/cars/{car}/view', [EmbedApiController::class, 'trackView']);
});
