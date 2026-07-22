<?php

use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\AiDesignController;
use App\Http\Controllers\CarCopyController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\CarVideoController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProefritController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BedrijfsvoorraadController;
use App\Http\Controllers\BookkeepingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KoopovereenkomstController;
use App\Http\Controllers\PublicerenController;
use App\Http\Controllers\RdwLookupController;
use App\Http\Controllers\StudioVideoController;
use App\Http\Controllers\VoertuigRapportController;
use App\Http\Controllers\VoorraadImportController;
use App\Http\Controllers\WidgetController;
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
    Route::get('/cars/handmatig', [CarController::class, 'createManual'])->name('cars.manual');
    Route::resource('cars', CarController::class);
    Route::post('/cars/lookup-kenteken', [CarController::class, 'lookupKenteken'])->name('cars.lookup');

    // Voorraad-import / onboarding (kentekens plakken of CSV, RDW-verrijkt)
    Route::get('/voorraad-import', [VoorraadImportController::class, 'index'])->name('import.index');
    Route::post('/voorraad-import/kentekens', [VoorraadImportController::class, 'kentekens'])->name('import.kentekens');
    Route::post('/voorraad-import/csv', [VoorraadImportController::class, 'csv'])->name('import.csv');
    Route::post('/voorraad-import/fotos', [VoorraadImportController::class, 'fotos'])->name('import.fotos');
    Route::get('/voorraad-import/sjabloon', [VoorraadImportController::class, 'template'])->name('import.template');
    Route::post('/cars/ai-copy', [CarCopyController::class, 'generate'])->name('cars.ai-copy');

    // AI promo-video's per auto (Higgsfield image-to-video)
    Route::post('/cars/{car}/videos', [CarVideoController::class, 'store'])->name('cars.videos.store');
    Route::get('/cars/{car}/videos/{video}/status', [CarVideoController::class, 'status'])->name('cars.videos.status');
    Route::delete('/cars/{car}/videos/{video}', [CarVideoController::class, 'destroy'])->name('cars.videos.destroy');
    Route::post('/cars/{car}/reel', [CarVideoController::class, 'stitch'])->name('cars.reel.store');
    Route::delete('/cars/{car}/reel/{reel}', [CarVideoController::class, 'destroyReel'])->name('cars.reel.destroy');

    // AJAX RDW lookup (returns JSON)
    Route::post('/rdw/lookup', [RdwLookupController::class, 'lookup'])->name('rdw.lookup');

    // Voertuigrapport (research a car by kenteken)
    Route::get('/onderzoek', [VoertuigRapportController::class, 'index'])->name('onderzoek');

    // RDW vrijwaring / bedrijfsvoorraad (echte registermutatie via ORV)
    Route::get('/vrijwaring', [BedrijfsvoorraadController::class, 'index'])->name('bedrijfsvoorraad.index');
    Route::post('/vrijwaring/vrijwaren', [BedrijfsvoorraadController::class, 'vrijwaren'])->name('bedrijfsvoorraad.vrijwaren');
    Route::post('/vrijwaring/uit', [BedrijfsvoorraadController::class, 'uit'])->name('bedrijfsvoorraad.uit');
    Route::get('/vrijwaring/{mutatie}/bewijs', [BedrijfsvoorraadController::class, 'print'])->name('bedrijfsvoorraad.print');

    // Verkoopdocumenten: koop-/verkoopovereenkomsten
    Route::get('/koopovereenkomsten', [KoopovereenkomstController::class, 'index'])->name('koopovereenkomsten.index');
    Route::get('/koopovereenkomsten/nieuw', [KoopovereenkomstController::class, 'create'])->name('koopovereenkomsten.create');
    Route::post('/koopovereenkomsten', [KoopovereenkomstController::class, 'store'])->name('koopovereenkomsten.store');
    Route::get('/koopovereenkomsten/{koopovereenkomst}/print', [KoopovereenkomstController::class, 'print'])->name('koopovereenkomsten.print');
    Route::delete('/koopovereenkomsten/{koopovereenkomst}', [KoopovereenkomstController::class, 'destroy'])->name('koopovereenkomsten.destroy');

    // Company Settings
    Route::get('/settings', [CompanySettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [CompanySettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/regenerate-api-key', [CompanySettingsController::class, 'regenerateApiKey'])->name('settings.regenerate-key');

    // Widgets: ontwerp + insluitcode per widget (vervangt Ontwerpen + Integratie)
    Route::get('/widgets', [WidgetController::class, 'index'])->name('widgets.index');
    Route::get('/widgets/voorraad', [WidgetController::class, 'voorraad'])->name('widgets.voorraad');
    Route::put('/widgets/voorraad', [WidgetController::class, 'updateVoorraad'])->name('widgets.voorraad.update');
    Route::get('/widgets/contact', [WidgetController::class, 'contact'])->name('widgets.contact');
    Route::get('/widgets/proefrit', [WidgetController::class, 'proefrit'])->name('widgets.proefrit');
    Route::put('/widgets/proefrit', [WidgetController::class, 'updateProefrit'])->name('widgets.proefrit.update');
    Route::get('/widgets/taxatie', [WidgetController::class, 'taxatie'])->name('widgets.taxatie');
    Route::post('/widgets/ai-design', [AiDesignController::class, 'generate'])->name('widgets.ai');

    // Oude routes -> redirect (backward-compat voor bestaande links en bookmarks)
    Route::get('/ontwerpen', fn () => redirect()->route('widgets.voorraad'))->name('ontwerpen');
    Route::get('/integratie', fn () => redirect()->route('widgets.index'))->name('integratie');

    // CRM / leadbeheer (alle leads samen: proefrit, contact, inruil, financiering, handmatig)
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::patch('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

    // AI Video Studio: upload any photos + prompt -> fal Seedance montage
    Route::get('/studio', [StudioVideoController::class, 'index'])->name('studio.index');
    Route::post('/studio', [StudioVideoController::class, 'store'])->name('studio.store');
    // 360-panorama -> soepele rondkijk-tour, lokaal via ffmpeg (geen AI)
    Route::post('/studio/tour', [StudioVideoController::class, 'storeTour'])->name('studio.tour');
    Route::get('/studio/{studioVideo}/status', [StudioVideoController::class, 'status'])->name('studio.status');
    Route::delete('/studio/{studioVideo}', [StudioVideoController::class, 'destroy'])->name('studio.destroy');

    // Facturatie
    Route::get('/facturen', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/facturen/nieuw', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/facturen', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/facturen/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/facturen/{invoice}/bewerken', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/facturen/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::post('/facturen/{invoice}/versturen', [InvoiceController::class, 'send'])->name('invoices.send');
    Route::post('/facturen/{invoice}/betaling', [InvoiceController::class, 'registerPayment'])->name('invoices.payment');
    Route::post('/facturen/{invoice}/annuleren', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::delete('/facturen/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/facturen/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Klanten (debiteuren)
    Route::get('/klanten', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/klanten', [CustomerController::class, 'store'])->name('customers.store');
    Route::put('/klanten/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/klanten/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Inkoop & kosten
    Route::get('/kosten', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/kosten', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('/kosten/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/kosten/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/kosten/{expense}/bijlage', [ExpenseController::class, 'attachment'])->name('expenses.attachment');

    // Boekhouding (financieel overzicht + BTW-aangifte + export)
    Route::get('/boekhouding', [BookkeepingController::class, 'index'])->name('bookkeeping.index');
    Route::get('/boekhouding/export', [BookkeepingController::class, 'export'])->name('bookkeeping.export');


    // Proefrit-aanvragen (leads uit de embed-widget)
    Route::get('/proefritten', [ProefritController::class, 'index'])->name('proefritten');
    Route::patch('/proefritten/{aanvraag}', [ProefritController::class, 'updateStatus'])->name('proefritten.status');

    // Publiceren (Publishing to external platforms)
    Route::get('/publiceren', [PublicerenController::class, 'index'])->name('publiceren');
    Route::post('/publiceren/platform/{platform}/connect', [PublicerenController::class, 'connectPlatform'])->name('publiceren.connect');
    Route::post('/publiceren/platform/{platform}/disconnect', [PublicerenController::class, 'disconnectPlatform'])->name('publiceren.disconnect');
    Route::post('/publiceren/publish', [PublicerenController::class, 'publishCars'])->name('publiceren.publish');
    Route::post('/publiceren/unpublish/{publication}', [PublicerenController::class, 'unpublishCar'])->name('publiceren.unpublish');

    // AI-collega: chat + autonome acties met activiteitenlog/undo
    Route::post('/ai/chat', [AiAssistantController::class, 'send'])->name('ai.send');
    Route::post('/ai/activity/{activity}/undo', [AiAssistantController::class, 'undo'])->name('ai.undo');
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
