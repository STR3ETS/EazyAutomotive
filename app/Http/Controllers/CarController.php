<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Services\RdwService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CarController extends Controller
{
    public function __construct(private RdwService $rdwService) {}

    public function index(Request $request)
    {
        $viewMode = $request->get('view', 'table');

        $query = Car::where('company_id', $request->user()->company_id)
            ->with('primaryImage')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('merk', 'like', "%{$s}%")
                    ->orWhere('handelsbenaming', 'like', "%{$s}%")
                    ->orWhere('kenteken', 'like', "%{$s}%");
            }))
            ->latest();

        if ($viewMode === 'kanban') {
            $allCars = $query->get();
            $kanbanColumns = [
                'draft' => ['label' => 'Concept', 'icon' => 'fa-pencil', 'bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'cars' => $allCars->where('status', 'draft')],
                'active' => ['label' => 'Actief', 'icon' => 'fa-circle-check', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'cars' => $allCars->where('status', 'active')],
                'reserved' => ['label' => 'Gereserveerd', 'icon' => 'fa-clock', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'cars' => $allCars->where('status', 'reserved')],
                'sold' => ['label' => 'Verkocht', 'icon' => 'fa-flag-checkered', 'bg' => 'bg-red-50', 'text' => 'text-red-500', 'cars' => $allCars->where('status', 'sold')],
            ];

            return view('cars.index', [
                'cars' => $allCars,
                'kanbanColumns' => $kanbanColumns,
                'viewMode' => $viewMode,
            ]);
        }

        $cars = $query->paginate(20)->withQueryString();

        return view('cars.index', [
            'cars' => $cars,
            'kanbanColumns' => [],
            'viewMode' => $viewMode,
        ]);
    }

    public function create()
    {
        return view('cars.create');
    }

    public function lookupKenteken(Request $request)
    {
        $request->validate(['kenteken' => 'required|string|max:8']);

        $rdwData = $this->rdwService->fetchByKenteken($request->kenteken);

        if (!$rdwData) {
            return back()->with('error', 'Kenteken niet gevonden bij RDW.')->withInput();
        }

        $carAttributes = $this->rdwService->mapToCarAttributes($rdwData);

        return view('cars.create-form', compact('carAttributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kenteken' => 'required|string|max:8',
            'merk' => 'nullable|string|max:100',
            'handelsbenaming' => 'nullable|string|max:100',
            'voertuigsoort' => 'nullable|string|max:50',
            'inrichting' => 'nullable|string|max:50',
            'eerste_kleur' => 'nullable|string|max:50',
            'tweede_kleur' => 'nullable|string|max:50',
            'aantal_cilinders' => 'nullable|integer|min:0',
            'cilinderinhoud' => 'nullable|integer|min:0',
            'vermogen' => 'nullable|integer|min:0',
            'massa_rijklaar' => 'nullable|integer|min:0',
            'aantal_zitplaatsen' => 'nullable|integer|min:0',
            'aantal_deuren' => 'nullable|integer|min:0',
            'datum_eerste_toelating' => 'nullable|date',
            'vervaldatum_apk' => 'nullable|date',
            'brandstof_omschrijving' => 'nullable|string|max:50',
            'catalogusprijs' => 'nullable|integer|min:0',
            'titel' => 'nullable|string|max:255',
            'beschrijving' => 'nullable|string|max:10000',
            'prijs' => 'nullable|numeric|min:0',
            'kilometerstand' => 'nullable|integer|min:0',
            'bouwjaar' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'required|in:draft,active,reserved,sold',
            'is_featured' => 'nullable|boolean',
            'extra_opties' => 'nullable|string',
            'images' => 'nullable|array|max:20',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'rdw_raw_data' => 'nullable',
        ]);

        // Convert price from euros to cents
        if (isset($validated['prijs'])) {
            $validated['prijs'] = (int) ($validated['prijs'] * 100);
        }

        // Parse extra opties from comma-separated string to array
        if (!empty($validated['extra_opties'])) {
            $validated['extra_opties'] = array_map('trim', explode(',', $validated['extra_opties']));
        }

        // Decode rdw_raw_data if it's a JSON string
        if (!empty($validated['rdw_raw_data']) && is_string($validated['rdw_raw_data'])) {
            $validated['rdw_raw_data'] = json_decode($validated['rdw_raw_data'], true);
        }

        $validated['company_id'] = $request->user()->company_id;
        $validated['kenteken'] = $this->rdwService->normalizeKenteken($validated['kenteken']);

        $images = $validated['images'] ?? [];
        unset($validated['images']);

        $car = Car::create($validated);

        // Handle image uploads
        $sortIndex = 0;
        foreach ($images as $image) {
            if (!$image instanceof \Illuminate\Http\UploadedFile || !$image->isValid()) {
                continue;
            }

            $extension = $image->getClientOriginalExtension() ?: ($image->guessExtension() ?: 'jpg');
            $filename = Str::random(40) . '.' . $extension;
            $storagePath = "cars/{$car->id}/{$filename}";

            Storage::disk('public')->put($storagePath, file_get_contents($image->getPathname()));

            $car->images()->create([
                'path' => $storagePath,
                'filename' => $image->getClientOriginalName(),
                'sort_order' => $sortIndex,
                'is_primary' => $sortIndex === 0,
            ]);
            $sortIndex++;
        }

        return redirect()->route('cars.show', $car)->with('success', 'Auto succesvol toegevoegd!');
    }

    public function show(Car $car)
    {
        $car->load('images');

        return view('cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        $car->load('images');

        return view('cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'merk' => 'nullable|string|max:100',
            'handelsbenaming' => 'nullable|string|max:100',
            'eerste_kleur' => 'nullable|string|max:50',
            'titel' => 'nullable|string|max:255',
            'beschrijving' => 'nullable|string|max:10000',
            'prijs' => 'nullable|numeric|min:0',
            'kilometerstand' => 'nullable|integer|min:0',
            'bouwjaar' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'required|in:draft,active,reserved,sold',
            'is_featured' => 'nullable|boolean',
            'extra_opties' => 'nullable|string',
            'new_images' => 'nullable|array|max:20',
            'new_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer|exists:car_images,id',
        ]);

        // Convert price from euros to cents
        if (isset($validated['prijs'])) {
            $validated['prijs'] = (int) ($validated['prijs'] * 100);
        }

        // Parse extra opties
        if (!empty($validated['extra_opties'])) {
            $validated['extra_opties'] = array_map('trim', explode(',', $validated['extra_opties']));
        } else {
            $validated['extra_opties'] = [];
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        $newImages = $validated['new_images'] ?? [];
        $deleteImages = $validated['delete_images'] ?? [];
        unset($validated['new_images'], $validated['delete_images']);

        $car->update($validated);

        // Delete images
        foreach ($deleteImages as $imageId) {
            $image = $car->images()->find($imageId);
            if ($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            }
        }

        // Add new images
        $maxSort = $car->images()->max('sort_order') ?? -1;
        $sortIndex = 0;
        foreach ($newImages as $image) {
            if (!$image instanceof \Illuminate\Http\UploadedFile || !$image->isValid()) {
                continue;
            }

            $extension = $image->getClientOriginalExtension() ?: ($image->guessExtension() ?: 'jpg');
            $filename = Str::random(40) . '.' . $extension;
            $storagePath = "cars/{$car->id}/{$filename}";

            Storage::disk('public')->put($storagePath, file_get_contents($image->getPathname()));

            $car->images()->create([
                'path' => $storagePath,
                'filename' => $image->getClientOriginalName(),
                'sort_order' => $maxSort + $sortIndex + 1,
                'is_primary' => $car->images()->count() === 0 && $sortIndex === 0,
            ]);
            $sortIndex++;
        }

        return redirect()->route('cars.show', $car)->with('success', 'Auto bijgewerkt!');
    }

    public function destroy(Car $car)
    {
        // Delete images from storage
        foreach ($car->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $car->delete();

        return redirect()->route('cars.index')->with('success', 'Auto verwijderd.');
    }
}
