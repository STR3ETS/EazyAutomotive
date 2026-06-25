<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
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

                    {{-- AI Promo-video's --}}
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-[#215558]/5">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                                <i class="fa-solid fa-clapperboard text-purple-500 text-xs"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#215558]">Promovideo's (AI)</h3>
                                <p class="text-[11px] text-[#215558] opacity-50">Genereer in één klik een cinematische video met muziek van al je foto's. Seedance 2.0 maakt er een vloeiende montage van.</p>
                            </div>
                        </div>

                        @if($car->images->count() === 0)
                            <p class="text-sm text-[#215558] opacity-60">Voeg eerst foto's toe. De video wordt gemaakt van de foto's van deze auto.</p>
                        @else
                            @php $allImageIds = $car->images->pluck('id')->values(); @endphp
                            <form method="POST" action="{{ route('cars.videos.store', $car) }}" x-data="{ submitting: false, allIds: @js($allImageIds), sel: @js($allImageIds) }" @submit="submitting = true">
                                @csrf
                                @if($car->images->count() > 1)
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider">Kies de foto's (<span x-text="sel.length"></span> geselecteerd)</label>
                                        <button type="button" @click="sel = (sel.length === allIds.length ? [] : [...allIds])" class="cursor-pointer text-[11px] font-semibold text-eazy hover:underline"><span x-text="sel.length === allIds.length ? 'Niets' : 'Alles'"></span></button>
                                    </div>
                                    <div class="flex flex-wrap gap-2 mb-1.5">
                                        @foreach($car->images as $image)
                                            <label class="cursor-pointer relative">
                                                <input type="checkbox" name="car_image_ids[]" value="{{ $image->id }}" class="sr-only" x-model.number="sel">
                                                <img src="{{ $image->url }}" alt="" class="w-16 h-16 object-cover rounded-lg border-2 transition" :class="sel.includes({{ $image->id }}) ? 'border-eazy' : 'border-transparent hover:border-[#215558]/20'">
                                                <span x-show="sel.includes({{ $image->id }})" class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-eazy text-white text-[10px] flex items-center justify-center"><i class="fa-solid fa-check"></i></span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <p class="text-[11px] text-[#215558] opacity-50 mb-3"><span x-text="sel.length"></span> foto('s) geselecteerd. Alle geselecteerde foto's (max 9) worden samengevoegd tot één cinematische video met muziek.</p>
                                @else
                                    <input type="hidden" name="car_image_ids[]" value="{{ $allImageIds->first() }}">
                                @endif
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Beschrijf de video</label>
                                <textarea name="prompt" rows="2" maxlength="1000" required x-ref="prompt"
                                    placeholder="Bijv. cinematische orbit rond de auto, dramatische avondbelichting, langzame dolly-in"
                                    class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy resize-none"></textarea>
                                <div class="flex flex-wrap gap-1.5 mt-2 mb-3">
                                    @foreach([
                                        'Cinematische orbit rond de auto, dramatische belichting',
                                        'Langzame dolly-in met reflecties op de lak',
                                        'Dynamische reveal, camera sweep van voor naar achter',
                                        'Luxe sfeer, zachte camerabeweging, gouden uur',
                                    ] as $suggestie)
                                        <button type="button" @click="$refs.prompt.value = @js($suggestie)" class="cursor-pointer text-[11px] px-2.5 py-1 rounded-full border border-[#215558]/10 text-[#215558] hover:border-eazy hover:bg-eazy-50 transition">{{ \Illuminate\Support\Str::limit($suggestie, 32) }}</button>
                                    @endforeach
                                </div>
                                @if(config('app.env') !== 'production')
                                    <input type="url" name="image_url" placeholder="Test: publieke afbeeldings-URL (lokaal nodig, fal kan localhost niet bereiken)" class="block w-full px-4 py-2 mb-3 rounded-xl border-[#215558]/10 text-xs focus:border-eazy focus:ring-eazy">
                                @endif
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Lengte</label>
                                <select name="duration" class="block w-full sm:w-48 px-4 py-2.5 mb-3 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    <option value="5">5 seconden</option>
                                    <option value="8" selected>8 seconden</option>
                                    <option value="10">10 seconden</option>
                                    <option value="15">15 seconden (max)</option>
                                </select>
                                <button type="submit" :disabled="submitting || sel.length === 0" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark disabled:opacity-50 disabled:cursor-default transition">
                                    <i class="fa-solid" :class="submitting ? 'fa-spinner fa-spin' : 'fa-clapperboard'"></i>
                                    <span x-text="submitting ? 'Bezig...' : 'Genereer cinematische video'"></span>
                                </button>
                            </form>
                        @endif

                        @if($car->videos->count() > 0)
                            <div class="mt-5 pt-5 border-t border-[#215558]/5 space-y-4">
                                @foreach($car->videos as $video)
                                    <div class="rounded-xl border border-[#215558]/10 p-3"
                                        @if($video->isPending()) x-data="videoPoll('{{ route('cars.videos.status', [$car, $video]) }}')" x-init="start()" @endif>
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-xs text-[#215558] opacity-70 line-clamp-2">{{ $video->prompt }}</p>
                                                <p class="text-[10px] text-[#215558] opacity-40 mt-1">{{ $video->created_at->format('d-m-Y H:i') }}</p>
                                            </div>
                                            @php
                                                $badge = match($video->status) {
                                                    'completed' => ['bg-emerald-50', 'text-emerald-600', 'Klaar', 'fa-circle-check'],
                                                    'failed' => ['bg-red-50', 'text-red-500', 'Mislukt', 'fa-circle-exclamation'],
                                                    'in_progress' => ['bg-amber-50', 'text-amber-600', 'Bezig', 'fa-spinner fa-spin'],
                                                    default => ['bg-gray-100', 'text-gray-500', 'In wachtrij', 'fa-clock'],
                                                };
                                            @endphp
                                            <span class="shrink-0 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badge[0] }} {{ $badge[1] }}">
                                                <i class="fa-solid {{ $badge[3] }} text-[9px]"></i> {{ $badge[2] }}
                                            </span>
                                        </div>

                                        @if($video->isCompleted() && $video->video_url)
                                            <video src="{{ $video->video_url }}" controls preload="metadata" class="w-full rounded-lg mt-3 bg-black"></video>
                                            <div class="flex items-center gap-2 mt-2">
                                                <a href="{{ $video->video_url }}" download target="_blank" rel="noopener" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-eazy-dark text-white rounded-full text-xs font-bold hover:bg-eazy-darker transition"><i class="fa-solid fa-download text-[10px]"></i> Downloaden</a>
                                                <form method="POST" action="{{ route('cars.videos.destroy', [$car, $video]) }}" onsubmit="return confirm('Deze video verwijderen?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 text-red-500 rounded-full text-xs font-semibold hover:bg-red-50 transition"><i class="fa-solid fa-trash text-[10px]"></i> Verwijderen</button>
                                                </form>
                                            </div>
                                        @elseif($video->isFailed())
                                            <p class="text-xs text-red-500 mt-2">{{ $video->error ?: 'De generatie is mislukt.' }}</p>
                                            <form method="POST" action="{{ route('cars.videos.destroy', [$car, $video]) }}" class="mt-2" onsubmit="return confirm('Deze video verwijderen?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="cursor-pointer text-xs text-red-500 font-semibold hover:underline"><i class="fa-solid fa-trash text-[10px] mr-1"></i> Verwijderen</button>
                                            </form>
                                        @else
                                            <p class="text-xs text-[#215558] opacity-50 mt-2"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Bezig met genereren, dit kan een paar minuten duren. De status ververst automatisch.</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Reel-stitching (ffmpeg) is vervangen door fal: Seedance levert al een complete montage met audio in één video. --}}

                        @if($car->reels->count() > 0)
                            <div class="mt-5 pt-5 border-t border-[#215558]/5 space-y-4">
                                <h4 class="text-xs font-bold text-[#215558]">Reels</h4>
                                @foreach($car->reels as $reel)
                                    <div class="rounded-xl border border-[#215558]/10 p-3">
                                        @if($reel->isCompleted())
                                            <video src="{{ $reel->url }}" controls preload="metadata" class="w-full rounded-lg bg-black"></video>
                                            <div class="flex items-center justify-between mt-2">
                                                <span class="text-[11px] text-[#215558] opacity-50">{{ $reel->clip_count }} clips &middot; {{ $reel->created_at->format('d-m-Y H:i') }}</span>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ $reel->url }}" download target="_blank" rel="noopener" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-eazy-dark text-white rounded-full text-xs font-bold hover:bg-eazy-darker transition"><i class="fa-solid fa-download text-[10px]"></i> Downloaden</a>
                                                    <form method="POST" action="{{ route('cars.reel.destroy', [$car, $reel]) }}" onsubmit="return confirm('Deze reel verwijderen?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="cursor-pointer inline-flex items-center justify-center w-8 h-8 text-red-500 rounded-full hover:bg-red-50 transition"><i class="fa-solid fa-trash text-[10px]"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        @elseif($reel->status === 'failed')
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-xs text-red-500">Samenvoegen mislukt: {{ $reel->error ?: 'onbekende fout' }}</p>
                                                <form method="POST" action="{{ route('cars.reel.destroy', [$car, $reel]) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="cursor-pointer text-xs text-red-500 font-semibold hover:underline">Verwijderen</button>
                                                </form>
                                            </div>
                                        @else
                                            <p class="text-xs text-[#215558] opacity-50"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Reel wordt samengevoegd...</p>
                                        @endif
                                    </div>
                                @endforeach
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

    <script>
        /* Poll a pending video; reload the page once it is done (completed or failed). */
        function videoPoll(url) {
            return {
                timer: null,
                start() {
                    this.timer = setInterval(() => this.check(), 8000);
                    setTimeout(() => this.check(), 4000);
                },
                async check() {
                    try {
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        const data = await res.json();
                        if (data && data.pending === false) {
                            clearInterval(this.timer);
                            window.location.reload();
                        }
                    } catch (e) { /* keep polling */ }
                },
            };
        }
        window.videoPoll = videoPoll;
    </script>
</x-app-layout>
