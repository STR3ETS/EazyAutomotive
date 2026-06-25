<?php

use App\Http\Controllers\Api\EmbedApiController;
use App\Http\Controllers\Api\LeadEmbedController;
use App\Http\Controllers\Api\ProefritEmbedController;
use Illuminate\Support\Facades\Route;

// Public embed API - authenticated via API key header/query parameter
Route::prefix('embed/v1')->middleware('embed.api')->group(function () {
    Route::get('/cars', [EmbedApiController::class, 'cars']);
    Route::get('/cars/{car}', [EmbedApiController::class, 'show']);
    Route::post('/cars/{car}/view', [EmbedApiController::class, 'trackView']);

    // Proefrit (test-drive) widget
    Route::get('/proefrit/config', [ProefritEmbedController::class, 'config']);
    Route::post('/proefrit', [ProefritEmbedController::class, 'store'])->middleware('throttle:15,1');

    // Contact / inruil / financiering form -> CRM lead
    Route::get('/lead/config', [LeadEmbedController::class, 'config']);
    Route::post('/lead', [LeadEmbedController::class, 'store'])->middleware('throttle:15,1');
});
