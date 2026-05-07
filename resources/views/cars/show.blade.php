<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $car->display_title }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('cars.edit', $car) }}" class="inline-flex items-center px-4 py-2 bg-eazy-darker border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-eazy-dark transition">
                    Bewerken
                </a>
                <form method="POST" action="{{ route('cars.destroy', $car) }}" onsubmit="return confirm('Weet je zeker dat je deze auto wilt verwijderen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                        Verwijderen
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Images --}}
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            @if($car->images->count() > 0)
                                <div class="mb-4">
                                    <img src="{{ $car->images->first()->url }}" alt="{{ $car->display_title }}"
                                        class="w-full h-80 object-cover rounded-lg" id="mainImage">
                                </div>
                                @if($car->images->count() > 1)
                                    <div class="grid grid-cols-5 gap-2">
                                        @foreach($car->images as $image)
                                            <img src="{{ $image->url }}" alt=""
                                                class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition"
                                                onclick="document.getElementById('mainImage').src = this.src">
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="w-full h-80 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                                    Geen foto's beschikbaar
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($car->beschrijving)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Beschrijving</h3>
                                <div class="text-gray-700 whitespace-pre-line">{{ $car->beschrijving }}</div>
                            </div>
                        </div>
                    @endif

                    {{-- Extra Options --}}
                    @if($car->extra_opties && count($car->extra_opties) > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Opties & Accessoires</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($car->extra_opties as $optie)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">{{ $optie }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right: Details --}}
                <div class="space-y-6">
                    {{-- Price & Status --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="text-3xl font-bold text-eazy mb-2">{{ $car->formatted_price }}</div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($car->status === 'active') bg-green-100 text-green-800
                                @elseif($car->status === 'reserved') bg-yellow-100 text-yellow-800
                                @elseif($car->status === 'sold') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($car->status) }}
                            </span>
                            @if($car->is_featured)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-eazy-100 text-eazy-darker ml-1">Uitgelicht</span>
                            @endif
                            <div class="text-sm text-gray-500 mt-2">{{ $car->view_count }} weergaven</div>
                        </div>
                    </div>

                    {{-- Vehicle Details --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Voertuiggegevens</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Kenteken</dt>
                                    <dd class="text-sm font-mono font-medium text-gray-900">{{ $car->kenteken }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Merk</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->merk ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Model</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->handelsbenaming ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Bouwjaar</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->bouwjaar ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Kilometerstand</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->kilometerstand ? number_format($car->kilometerstand, 0, ',', '.') . ' km' : '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Brandstof</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->brandstof_omschrijving ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Kleur</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->eerste_kleur ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Carrosserie</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->inrichting ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Zitplaatsen</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->aantal_zitplaatsen ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Deuren</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->aantal_deuren ?? '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Cilinderinhoud</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->cilinderinhoud ? $car->cilinderinhoud . ' cc' : '-' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">APK tot</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $car->vervaldatum_apk?->format('d-m-Y') ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-6">
                <a href="{{ route('cars.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Terug naar overzicht</a>
            </div>

        </div>
    </div>
</x-app-layout>
