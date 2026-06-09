<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarPublication;
use App\Models\PlatformConnection;
use App\Models\PublicationLog;
use App\Services\Publishing\PublishingService;
use Illuminate\Http\Request;

class PublicerenController extends Controller
{
    public function __construct(private PublishingService $publishingService) {}

    public function index(Request $request)
    {
        $company = $request->user()->company;
        $platforms = config('platforms');

        $connections = $company->platformConnections()->get()->keyBy('platform');

        $cars = Car::where('company_id', $company->id)
            ->with(['primaryImage', 'publications.platformConnection'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('merk', 'like', "%{$s}%")
                  ->orWhere('handelsbenaming', 'like', "%{$s}%")
                  ->orWhere('kenteken', 'like', "%{$s}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_published' => CarPublication::where('company_id', $company->id)->where('status', 'published')->count(),
            'total_pending' => CarPublication::where('company_id', $company->id)->whereIn('status', ['pending', 'publishing'])->count(),
            'total_failed' => CarPublication::where('company_id', $company->id)->where('status', 'failed')->count(),
            'connected_platforms' => $connections->where('status', 'connected')->count(),
        ];

        $recentLogs = $company->publicationLogs()
            ->with(['car', 'user'])
            ->latest()
            ->take(20)
            ->get();

        return view('company.publiceren', compact(
            'platforms', 'connections', 'cars', 'stats', 'recentLogs', 'company'
        ));
    }

    public function connectPlatform(Request $request, string $platform)
    {
        $platformConfig = config("platforms.{$platform}");
        if (!$platformConfig) {
            abort(404);
        }

        $rules = [];
        foreach ($platformConfig['fields'] as $field) {
            $rules[$field['name']] = $field['required'] ? 'required|string|max:255' : 'nullable|string|max:255';
        }

        $validated = $request->validate($rules);

        $company = $request->user()->company;

        $connection = PlatformConnection::updateOrCreate(
            ['company_id' => $company->id, 'platform' => $platform],
            [
                'credentials' => $validated,
                'status' => 'connected',
                'connected_at' => now(),
                'last_error' => null,
            ]
        );

        $publisher = $this->publishingService->getPublisher($platform);
        if (!$publisher->validateConnection($connection)) {
            $connection->update(['status' => 'error', 'last_error' => 'Ongeldige inloggegevens']);

            return back()->with('error', 'Kon niet verbinden met ' . $platformConfig['name'] . '. Controleer je gegevens.');
        }

        PublicationLog::create([
            'company_id' => $company->id,
            'user_id' => $request->user()->id,
            'action' => 'connect',
            'platform' => $platform,
            'status' => 'success',
            'message' => $platformConfig['name'] . ' verbonden',
        ]);

        return back()->with('success', $platformConfig['name'] . ' succesvol verbonden!');
    }

    public function disconnectPlatform(Request $request, string $platform)
    {
        $company = $request->user()->company;
        $connection = PlatformConnection::where('company_id', $company->id)
            ->where('platform', $platform)
            ->firstOrFail();

        $platformName = config("platforms.{$platform}.name", $platform);

        $connection->update([
            'status' => 'disconnected',
            'credentials' => null,
        ]);

        PublicationLog::create([
            'company_id' => $company->id,
            'user_id' => $request->user()->id,
            'action' => 'disconnect',
            'platform' => $platform,
            'status' => 'success',
            'message' => $platformName . ' losgekoppeld',
        ]);

        return back()->with('success', $platformName . ' losgekoppeld.');
    }

    public function publishCars(Request $request)
    {
        $request->validate([
            'car_ids' => 'required|array|min:1',
            'car_ids.*' => 'integer|exists:cars,id',
            'platform_ids' => 'required|array|min:1',
            'platform_ids.*' => 'integer|exists:platform_connections,id',
        ]);

        $company = $request->user()->company;

        $cars = Car::where('company_id', $company->id)
            ->whereIn('id', $request->car_ids)
            ->get();

        $connections = PlatformConnection::where('company_id', $company->id)
            ->whereIn('id', $request->platform_ids)
            ->connected()
            ->get();

        $results = ['success' => 0, 'pending' => 0, 'failed' => 0];

        foreach ($cars as $car) {
            foreach ($connections as $connection) {
                $publication = $this->publishingService->publish($car, $connection);
                $results[match ($publication->status) {
                    'published' => 'success',
                    'pending' => 'pending',
                    default => 'failed',
                }]++;
            }
        }

        $parts = [];
        if ($results['success'] > 0) {
            $parts[] = "{$results['success']} gepubliceerd";
        }
        if ($results['pending'] > 0) {
            $parts[] = "{$results['pending']} in wachtrij";
        }
        if ($results['failed'] > 0) {
            $parts[] = "{$results['failed']} mislukt";
        }

        return back()->with('success', $parts ? implode(', ', $parts) : 'Geen publicaties verwerkt');
    }

    public function unpublishCar(Request $request, CarPublication $publication)
    {
        if ($publication->company_id !== $request->user()->company->id) {
            abort(403);
        }

        $this->publishingService->unpublish($publication);

        return back()->with('success', 'Auto verwijderd van platform.');
    }
}
