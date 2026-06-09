<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicerenController;
use App\Http\Controllers\RdwLookupController;
use App\Http\Controllers\VoertuigRapportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public inventory feeds pulled by external portals (addressed by company api_key).
Route::prefix('feed/v1')->group(function () {
    Route::get('/{apiKey}/voorraad.xml', [FeedController::class, 'cars'])->name('feed.cars');
    Route::get('/{apiKey}/voorraad.csv', [FeedController::class, 'carsCsv'])->name('feed.cars.csv');
    Route::get('/{apiKey}/platform/{platform}.xml', [FeedController::class, 'platform'])
        ->where('platform', '[a-z]+')
        ->name('feed.platform');
});

// Authenticated routes with company ownership check
Route::middleware(['auth', 'verified', 'company.ownership'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Cars CRUD
    Route::resource('cars', CarController::class);
    Route::post('/cars/lookup-kenteken', [CarController::class, 'lookupKenteken'])->name('cars.lookup');

    // AJAX RDW lookup (returns JSON)
    Route::post('/rdw/lookup', [RdwLookupController::class, 'lookup'])->name('rdw.lookup');

    // Voertuigrapport (research a car by kenteken)
    Route::get('/onderzoek', [VoertuigRapportController::class, 'index'])->name('onderzoek');

    // Company Settings
    Route::get('/settings', [CompanySettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [CompanySettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/regenerate-api-key', [CompanySettingsController::class, 'regenerateApiKey'])->name('settings.regenerate-key');

    // Ontwerpen (Widget Design)
    Route::get('/ontwerpen', [CompanySettingsController::class, 'ontwerpen'])->name('ontwerpen');
    Route::put('/ontwerpen', [CompanySettingsController::class, 'updateEmbedSettings'])->name('ontwerpen.update');

    // Integratie (Embed Code + Guide)
    Route::get('/integratie', [CompanySettingsController::class, 'integratie'])->name('integratie');

    // Publiceren (Publishing to external platforms)
    Route::get('/publiceren', [PublicerenController::class, 'index'])->name('publiceren');
    Route::post('/publiceren/platform/{platform}/connect', [PublicerenController::class, 'connectPlatform'])->name('publiceren.connect');
    Route::post('/publiceren/platform/{platform}/disconnect', [PublicerenController::class, 'disconnectPlatform'])->name('publiceren.disconnect');
    Route::post('/publiceren/publish', [PublicerenController::class, 'publishCars'])->name('publiceren.publish');
    Route::post('/publiceren/unpublish/{publication}', [PublicerenController::class, 'unpublishCar'])->name('publiceren.unpublish');
});

// Profile routes (from Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // First-login tutorial completion
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
});

require __DIR__.'/auth.php';
