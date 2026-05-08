<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicerenController;
use App\Http\Controllers\RdwLookupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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
});

require __DIR__.'/auth.php';
