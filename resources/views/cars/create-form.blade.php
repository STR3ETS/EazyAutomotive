<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nieuwe auto toevoegen</h2>
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

            <form method="POST" action="{{ route('cars.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- RDW Data (read-only overview) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">RDW Gegevens</h3>
                        <p class="text-sm text-gray-500 mb-4">Automatisch opgehaald. Deze gegevens worden opgeslagen bij de auto.</p>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-xs text-gray-500">Kenteken</span>
                                <div class="font-mono font-bold text-lg">{{ $carAttributes['kenteken'] }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Merk</span>
                                <div class="font-medium">{{ $carAttributes['merk'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Model</span>
                                <div class="font-medium">{{ $carAttributes['handelsbenaming'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Bouwjaar</span>
                                <div>{{ $carAttributes['bouwjaar'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Brandstof</span>
                                <div>{{ $carAttributes['brandstof_omschrijving'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Kleur</span>
                                <div>{{ $carAttributes['eerste_kleur'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Carrosserie</span>
                                <div>{{ $carAttributes['inrichting'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">Zitplaatsen</span>
                                <div>{{ $carAttributes['aantal_zitplaatsen'] ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">APK tot</span>
                                <div>{{ $carAttributes['vervaldatum_apk'] ?? '-' }}</div>
                            </div>
                        </div>

                        {{-- Hidden fields for all RDW data --}}
                        @foreach($carAttributes as $key => $value)
                            @if(!is_array($value))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <input type="hidden" name="rdw_raw_data" value="{{ json_encode($carAttributes['rdw_raw_data'] ?? []) }}">
                    </div>
                </div>

                {{-- Dealer Fields --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Verkoopgegevens</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="titel" class="block text-sm font-medium text-gray-700">Titel (optioneel)</label>
                                <input type="text" name="titel" id="titel" value="{{ old('titel') }}"
                                    placeholder="bijv. Nette BMW 3 Serie met lage km-stand"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                                <p class="mt-1 text-xs text-gray-400">Laat leeg voor automatische titel (Merk + Model)</p>
                            </div>

                            <div>
                                <label for="prijs" class="block text-sm font-medium text-gray-700">Prijs (EUR)</label>
                                <input type="number" name="prijs" id="prijs" value="{{ old('prijs') }}"
                                    placeholder="bijv. 15000" step="1" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="kilometerstand" class="block text-sm font-medium text-gray-700">Kilometerstand</label>
                                <input type="number" name="kilometerstand" id="kilometerstand" value="{{ old('kilometerstand') }}"
                                    placeholder="bijv. 95000" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                                    <option value="draft" @selected(old('status') === 'draft')>Concept</option>
                                    <option value="active" @selected(old('status', 'active') === 'active')>Actief</option>
                                    <option value="reserved" @selected(old('status') === 'reserved')>Gereserveerd</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="beschrijving" class="block text-sm font-medium text-gray-700">Beschrijving</label>
                            <textarea name="beschrijving" id="beschrijving" rows="4"
                                placeholder="Omschrijf de auto, bijzonderheden, opties etc."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">{{ old('beschrijving') }}</textarea>
                        </div>

                        <div class="mt-6">
                            <label for="extra_opties" class="block text-sm font-medium text-gray-700">Extra opties</label>
                            <input type="text" name="extra_opties" id="extra_opties" value="{{ old('extra_opties') }}"
                                placeholder="bijv. Airco, Navigatie, Leder, Stoelverwarming"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            <p class="mt-1 text-xs text-gray-400">Scheid opties met een komma</p>
                        </div>
                    </div>
                </div>

                {{-- Images --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Foto's</h3>
                        <p class="text-sm text-gray-500 mb-4">Upload maximaal 20 foto's. De eerste foto wordt de hoofdfoto.</p>

                        <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/webp"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-eazy-50 file:text-eazy-dark hover:file:bg-eazy-100">
                        <p class="mt-1 text-xs text-gray-400">JPG, PNG of WebP. Max 5MB per foto.</p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('cars.create') }}" class="text-sm text-gray-500 hover:text-gray-700">Ander kenteken</a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-eazy border border-transparent rounded-md font-semibold text-sm text-white hover:bg-eazy-dark transition">
                        Auto opslaan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
