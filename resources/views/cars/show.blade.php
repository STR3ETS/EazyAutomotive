<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <a href="{{ route('cars.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#215558] opacity-50 hover:opacity-100 transition mb-3">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i> Terug naar overzicht
                    </a>
                    <h1 class="text-2xl font-black text-[#215558]">{{ $car->display_title }}</h1>
                    <div class="flex items-center gap-3 mt-1.5">
                        <code class="text-xs font-mono bg-[#ebf2f2] text-[#215558] opacity-70 px-2 py-0.5 rounded-lg">{{ $car->kenteken }}</code>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[11px] font-bold uppercase tracking-wide
                            @if($car->status === 'active') bg-emerald-50 text-emerald-600
                            @elseif($car->status === 'reserved') bg-amber-50 text-amber-600
                            @elseif($car->status === 'sold') bg-red-50 text-red-500
                            @else bg-gray-100 text-gray-500 @endif">
                            <i class="fa-solid
                                @if($car->status === 'active') fa-circle-check
                                @elseif($car->status === 'reserved') fa-clock
                                @elseif($car->status === 'sold') fa-flag-checkered
                                @else fa-pencil @endif text-[9px]"></i>
                            {{ ucfirst($car->status) }}
                        </span>
                        @if($car->is_featured)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-600">
                                <i class="fa-solid fa-star text-[9px]"></i> Uitgelicht
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('cars.edit', $car) }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 hover:shadow-eazy/30 transition-all">
                        <i class="fa-solid fa-pen text-xs"></i> Bewerken
                    </a>
                    <form method="POST" action="{{ route('cars.destroy', $car) }}" onsubmit="return confirm('Weet je zeker dat je deze auto wilt verwijderen? Dit kan niet ongedaan worden gemaakt.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 rounded-full text-sm font-bold hover:bg-red-100 transition">
                            <i class="fa-solid fa-trash-can text-xs"></i> Verwijderen
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Images + Description + Options --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Images --}}
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden">
                        @if($car->images->count() > 0)
                            <div class="mb-3">
                                <img src="{{ $car->images->first()->url }}" alt="{{ $car->display_title }}"
                                    class="w-full h-80 object-cover rounded-xl" id="mainImage">
                            </div>
                            @if($car->images->count() > 1)
                                <div class="grid grid-cols-5 gap-2">
                                    @foreach($car->images as $image)
                                        <img src="{{ $image->url }}" alt=""
                                            class="w-full h-16 object-cover rounded-lg cursor-pointer hover:opacity-75 transition border-2 border-transparent hover:border-eazy"
                                            onclick="document.getElementById('mainImage').src = this.src">
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="w-full h-80 bg-[#ebf2f2] rounded-xl flex flex-col items-center justify-center">
                                <i class="fa-solid fa-image text-[#215558]/15 text-4xl mb-2"></i>
                                <p class="text-sm text-[#215558] opacity-40">Geen foto's beschikbaar</p>
                            </div>
                        @endif
                    </div>

                    {{-- Description --}}
                    @if($car->beschrijving)
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-[#215558]/5">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                    <i class="fa-solid fa-align-left text-blue-500 text-xs"></i>
                                </div>
                                <h3 class="text-sm font-bold text-[#215558]">Beschrijving</h3>
                            </div>
                            <div class="text-sm text-[#215558] opacity-70 whitespace-pre-line leading-relaxed">{{ $car->beschrijving }}</div>
                        </div>
                    @endif

                    {{-- Extra Options --}}
                    @if($car->extra_opties && count($car->extra_opties) > 0)
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-[#215558]/5">
                                <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                                    <i class="fa-solid fa-list-check text-green-500 text-xs"></i>
                                </div>
                                <h3 class="text-sm font-bold text-[#215558]">Opties & Accessoires</h3>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($car->extra_opties as $optie)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#ebf2f2] text-[#215558]">
                                        <i class="fa-solid fa-check text-eazy text-[8px]"></i> {{ $optie }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right: Price & Specs --}}
                <div class="space-y-6">

                    {{-- Price & Views --}}
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                        <div class="text-3xl font-black text-eazy mb-3">{{ $car->formatted_price }}</div>
                        <div class="flex items-center gap-4 text-xs text-[#215558] opacity-50 font-medium">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-eye text-[10px]"></i> {{ $car->view_count }} weergaven
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar text-[10px]"></i> {{ $car->created_at->format('d-m-Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Vehicle Details --}}
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-[#215558]/5">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                                <i class="fa-solid fa-car text-amber-500 text-xs"></i>
                            </div>
                            <h3 class="text-sm font-bold text-[#215558]">Voertuiggegevens</h3>
                        </div>
                        <dl class="space-y-3">
                            @foreach([
                                ['fa-hashtag', 'Kenteken', $car->kenteken],
                                ['fa-building', 'Merk', $car->merk ?? '-'],
                                ['fa-car', 'Model', $car->handelsbenaming ?? '-'],
                                ['fa-calendar', 'Bouwjaar', $car->bouwjaar ?? '-'],
                                ['fa-road', 'Kilometerstand', $car->kilometerstand ? number_format($car->kilometerstand, 0, ',', '.') . ' km' : '-'],
                                ['fa-gas-pump', 'Brandstof', $car->brandstof_omschrijving ?? '-'],
                                ['fa-palette', 'Kleur', $car->eerste_kleur ?? '-'],
                                ['fa-truck', 'Carrosserie', $car->inrichting ?? '-'],
                                ['fa-chair', 'Zitplaatsen', $car->aantal_zitplaatsen ?? '-'],
                                ['fa-door-open', 'Deuren', $car->aantal_deuren ?? '-'],
                                ['fa-gauge-high', 'Cilinderinhoud', $car->cilinderinhoud ? $car->cilinderinhoud . ' cc' : '-'],
                                ['fa-shield-halved', 'APK tot', $car->vervaldatum_apk?->format('d-m-Y') ?? '-'],
                            ] as [$icon, $label, $value])
                            <div class="flex items-center justify-between py-1 {{ !$loop->last ? 'border-b border-[#215558]/5' : '' }}">
                                <dt class="flex items-center gap-2 text-xs text-[#215558] opacity-50">
                                    <i class="fa-solid {{ $icon }} w-3.5 text-center text-[10px]"></i> {{ $label }}
                                </dt>
                                <dd class="text-sm font-semibold text-[#215558]">{{ $value }}</dd>
                            </div>
                            @endforeach
                        </dl>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
