<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Auto bewerken: {{ $car->display_title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cars.update', $car) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- RDW Data (read-only) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">RDW Gegevens</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-xs text-gray-500">Kenteken</span>
                                <div class="font-mono font-bold text-lg">{{ $car->kenteken }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Merk</span>
                                <div class="font-medium">{{ $car->merk ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Model</span>
                                <div class="font-medium">{{ $car->handelsbenaming ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Bouwjaar</span>
                                <div>{{ $car->bouwjaar ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Brandstof</span>
                                <div>{{ $car->brandstof_omschrijving ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Kleur</span>
                                <div>{{ $car->eerste_kleur ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Editable Fields --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Verkoopgegevens</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="titel" class="block text-sm font-medium text-gray-700">Titel (optioneel)</label>
                                <input type="text" name="titel" id="titel" value="{{ old('titel', $car->titel) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="prijs" class="block text-sm font-medium text-gray-700">Prijs (EUR)</label>
                                <input type="number" name="prijs" id="prijs" value="{{ old('prijs', $car->prijs ? $car->prijs / 100 : '') }}"
                                    step="1" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="kilometerstand" class="block text-sm font-medium text-gray-700">Kilometerstand</label>
                                <input type="number" name="kilometerstand" id="kilometerstand" value="{{ old('kilometerstand', $car->kilometerstand) }}"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                                    <option value="draft" @selected(old('status', $car->status) === 'draft')>Concept</option>
                                    <option value="active" @selected(old('status', $car->status) === 'active')>Actief</option>
                                    <option value="reserved" @selected(old('status', $car->status) === 'reserved')>Gereserveerd</option>
                                    <option value="sold" @selected(old('status', $car->status) === 'sold')>Verkocht</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="beschrijving" class="block text-sm font-medium text-gray-700">Beschrijving</label>
                            <textarea name="beschrijving" id="beschrijving" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">{{ old('beschrijving', $car->beschrijving) }}</textarea>
                        </div>

                        <div class="mt-6">
                            <label for="extra_opties" class="block text-sm font-medium text-gray-700">Extra opties</label>
                            <input type="text" name="extra_opties" id="extra_opties"
                                value="{{ old('extra_opties', is_array($car->extra_opties) ? implode(', ', $car->extra_opties) : '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            <p class="mt-1 text-xs text-gray-400">Scheid opties met een komma</p>
                        </div>

                        <div class="mt-6">
                            <label class="inline-flex items-center">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $car->is_featured) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-eazy shadow-sm focus:ring-eazy">
                                <span class="ml-2 text-sm text-gray-700">Uitgelicht (featured)</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Current Images --}}
                @if($car->images->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Huidige foto's</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($car->images as $image)
                                    <div class="relative group">
                                        <img src="{{ $image->url }}" alt="" class="w-full h-32 object-cover rounded">
                                        <label class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center cursor-pointer opacity-0 group-hover:opacity-100 transition text-xs">
                                            <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="sr-only">
                                            &times;
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-2 text-xs text-gray-400">Klik op het rode kruisje om foto's te verwijderen</p>
                        </div>
                    </div>
                @endif

                {{-- New Images --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Nieuwe foto's toevoegen</h3>
                        <input type="file" name="new_images[]" id="new_images" multiple accept="image/jpeg,image/png,image/webp"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-eazy-50 file:text-eazy-dark hover:file:bg-eazy-100">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('cars.show', $car) }}" class="text-sm text-gray-500 hover:text-gray-700">Annuleren</a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-eazy border border-transparent rounded-md font-semibold text-sm text-white hover:bg-eazy-dark transition">
                        Wijzigingen opslaan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
