<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <i class="fa-solid fa-clapperboard text-purple-500"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Video Studio</h1>
                    <p class="text-sm text-[#215558] opacity-50">Upload foto's of video's, of laat ze leeg en genereer puur uit je beschrijving. AI maakt er een cinematische video van.</p>
                </div>
            </div>

            {{-- Upload + prompt --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-8">
                <form method="POST" action="{{ route('studio.store') }}" enctype="multipart/form-data"
                      x-data="{ submitting: false, files: 0, vids: 0 }" @submit="submitting = true">
                    @csrf

                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Foto's (optioneel, tot 9)</label>
                    <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-[#215558]/15 rounded-xl py-8 cursor-pointer hover:border-eazy hover:bg-eazy-50/40 transition mb-1.5">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-[#215558] opacity-40"></i>
                        <span class="text-sm text-[#215558] opacity-70" x-text="files ? (files + ' foto(\'s) gekozen') : 'Klik om foto\'s te kiezen'"></span>
                        <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple class="hidden"
                               @change="files = $event.target.files.length">
                    </label>
                    <p class="text-[11px] text-[#215558] opacity-50 mb-4">JPG, PNG of WebP, max 20 MB per foto. Meerdere foto's worden tot één montage gemaakt.</p>

                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Video's (optioneel, tot 3)</label>
                    <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-[#215558]/15 rounded-xl py-6 cursor-pointer hover:border-eazy hover:bg-eazy-50/40 transition mb-1.5">
                        <i class="fa-solid fa-film text-2xl text-[#215558] opacity-40"></i>
                        <span class="text-sm text-[#215558] opacity-70" x-text="vids ? (vids + ' video(\'s) gekozen') : 'Klik om video\'s te kiezen'"></span>
                        <input type="file" name="videos[]" accept="video/mp4,video/quicktime,video/webm" multiple class="hidden"
                               @change="vids = $event.target.files.length">
                    </label>
                    <p class="text-[11px] text-[#215558] opacity-50 mb-4">MP4, MOV of WebM. De AI gebruikt korte clips als referentie: het model accepteert een video van ongeveer 15 seconden en onder de 50 MB. Erg lange of grote video's worden door de AI (fal) geweigerd.</p>

                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Beschrijf de video</label>
                    <textarea name="prompt" rows="3" maxlength="1500" required x-ref="prompt"
                        placeholder="Bijv. cinematische woningtour, langzame gliding camera door de woonkamer, warme avondsfeer, eindigt op een wijde shot van de gevel"
                        class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy resize-none">{{ old('prompt') }}</textarea>
                    <div class="flex flex-wrap gap-1.5 mt-2 mb-4">
                        @foreach([
                            'Cinematische woningtour, langzame gliding camera, gouden uur, warme sfeer',
                            'Vloeiende fly-through door het huis, eindigt op de gevel',
                            'Strakke moderne reveal, zachte camerabeweging, clean en luxe',
                            'Dramatische buitenshot bij avond, daarna een interieur sweep',
                        ] as $suggestie)
                            <button type="button" @click="$refs.prompt.value = @js($suggestie)" class="cursor-pointer text-[11px] px-2.5 py-1 rounded-full border border-[#215558]/10 text-[#215558] hover:border-eazy hover:bg-eazy-50 transition">{{ \Illuminate\Support\Str::limit($suggestie, 38) }}</button>
                        @endforeach
                    </div>

                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Lengte</label>
                    <select name="duration" class="block w-full sm:w-48 px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy mb-4">
                        <option value="5">5 seconden</option>
                        <option value="8" selected>8 seconden</option>
                        <option value="10">10 seconden</option>
                        <option value="15">15 seconden (max)</option>
                    </select>

                    <button type="submit" :disabled="submitting"
                        class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark disabled:opacity-50 disabled:cursor-default transition">
                        <i class="fa-solid" :class="submitting ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'"></i>
                        <span x-text="submitting ? 'Uploaden en genereren...' : 'Genereer video'"></span>
                    </button>
                    <p x-show="submitting" class="text-[11px] text-[#215558] opacity-50 mt-2">Je bestanden worden geupload en de video wordt gemaakt. Dit kan een paar minuten duren, pagina niet sluiten.</p>
                </form>
            </div>

            {{-- 360-panorama naar rondkijk-tour (lokaal, geen AI) --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-8">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <i class="fa-solid fa-panorama text-eazy"></i>
                    <h2 class="text-sm font-black text-[#215558]">360-panorama naar rondkijk-tour</h2>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-eazy/10 text-eazy uppercase tracking-wide">Lokaal &middot; geen AI</span>
                </div>
                <p class="text-[12px] text-[#215558] opacity-50 mb-4">Upload een equirectangular 360-foto (breedte is 2x de hoogte). Je krijgt een soepele, onvervormde rondkijk-video, ideaal voor een woningtour. Omdat het geen AI is, krijg je geen vervormingen of verzinsels: precies jouw ruimte.</p>

                <form method="POST" action="{{ route('studio.tour') }}" enctype="multipart/form-data"
                      x-data="{ rendering: false, pano: '' }" @submit="rendering = true">
                    @csrf

                    <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-[#215558]/15 rounded-xl py-8 cursor-pointer hover:border-eazy hover:bg-eazy-50/40 transition mb-1.5">
                        <i class="fa-solid fa-image text-2xl text-[#215558] opacity-40"></i>
                        <span class="text-sm text-[#215558] opacity-70" x-text="pano || 'Klik om je 360-panorama te kiezen'"></span>
                        <input type="file" name="panorama" accept="image/jpeg,image/png" class="hidden" required
                               @change="pano = $event.target.files[0]?.name || ''">
                    </label>
                    <p class="text-[11px] text-[#215558] opacity-50 mb-4">JPG of PNG, equirectangular (2:1), max 50 MB.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Lengte</label>
                            <select name="tour_duration" class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <option value="8">8 sec (vlot)</option>
                                <option value="12" selected>12 sec</option>
                                <option value="15">15 sec (rustig)</option>
                                <option value="20">20 sec (heel rustig)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Kijkhoek</label>
                            <select name="fov" class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <option value="85">Strak</option>
                                <option value="100" selected>Normaal</option>
                                <option value="115">Breed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Draairichting</label>
                            <select name="direction" class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <option value="right" selected>Naar rechts</option>
                                <option value="left">Naar links</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" :disabled="rendering"
                        class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-[#215558] text-white rounded-full text-sm font-bold hover:opacity-90 disabled:opacity-50 disabled:cursor-default transition">
                        <i class="fa-solid" :class="rendering ? 'fa-spinner fa-spin' : 'fa-panorama'"></i>
                        <span x-text="rendering ? 'Tour maken...' : 'Maak 360-tour'"></span>
                    </button>
                    <p x-show="rendering" class="text-[11px] text-[#215558] opacity-50 mt-2">De tour wordt lokaal gerenderd, dit duurt enkele seconden.</p>
                </form>
            </div>

            {{-- Generated videos --}}
            @if($videos->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($videos as $video)
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-4"
                            @if($video->isPending()) x-data="videoPoll('{{ route('studio.status', $video) }}')" x-init="start()" @endif>
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <p class="text-xs text-[#215558] opacity-70 line-clamp-2">{{ $video->prompt }}</p>
                                @php
                                    $badge = match($video->status) {
                                        'completed' => ['bg-emerald-50 text-emerald-600', 'fa-circle-check', 'Klaar'],
                                        'failed' => ['bg-red-50 text-red-500', 'fa-circle-xmark', 'Mislukt'],
                                        default => ['bg-amber-50 text-amber-600', 'fa-spinner fa-spin', 'Bezig'],
                                    };
                                @endphp
                                <span class="shrink-0 inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold {{ $badge[0] }}"><i class="fa-solid {{ $badge[1] }} text-[9px]"></i> {{ $badge[2] }}</span>
                            </div>
                            <p class="text-[10px] text-[#215558] opacity-40 mb-2">{{ $video->image_count }} bestand{{ $video->image_count === 1 ? '' : 'en' }} &middot; {{ $video->created_at->format('d-m-Y H:i') }}</p>

                            @if($video->isCompleted() && $video->video_url)
                                <video src="{{ $video->video_url }}" controls preload="metadata" @if($video->thumbnail_url) poster="{{ $video->thumbnail_url }}" @endif class="w-full rounded-lg bg-black"></video>
                                <div class="flex items-center justify-between mt-3">
                                    <a href="{{ $video->video_url }}" download target="_blank" rel="noopener" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-eazy-dark text-white rounded-full text-xs font-bold hover:bg-eazy-darker transition"><i class="fa-solid fa-download text-[10px]"></i> Downloaden</a>
                                    <form method="POST" action="{{ route('studio.destroy', $video) }}" onsubmit="return confirm('Deze video verwijderen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="cursor-pointer inline-flex items-center justify-center w-8 h-8 text-red-500 rounded-full hover:bg-red-50 transition"><i class="fa-solid fa-trash text-[10px]"></i></button>
                                    </form>
                                </div>
                            @elseif($video->isFailed())
                                <p class="text-xs text-red-500 mt-1">{{ $video->error ?: 'De generatie is mislukt.' }}</p>
                                <form method="POST" action="{{ route('studio.destroy', $video) }}" class="mt-2" onsubmit="return confirm('Deze video verwijderen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="cursor-pointer text-xs text-red-500 font-semibold hover:underline"><i class="fa-solid fa-trash text-[10px] mr-1"></i> Verwijderen</button>
                                </form>
                            @else
                                <p class="text-xs text-[#215558] opacity-50 mt-1"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Bezig met genereren, dit kan een paar minuten duren. De status ververst automatisch.</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-[#215558] opacity-40 text-sm">Nog geen video's. Upload foto's hierboven om je eerste te maken.</div>
            @endif

        </div>
    </div>

    <script>
        /* Poll a pending studio video; reload once it is done (completed or failed). */
        function videoPoll(url) {
            return {
                timer: null,
                start() {
                    this.timer = setInterval(() => {
                        fetch(url, { headers: { 'Accept': 'application/json' } })
                            .then(r => r.json())
                            .then(d => { if (!d.pending) { clearInterval(this.timer); window.location.reload(); } })
                            .catch(() => {});
                    }, 6000);
                }
            };
        }
        window.videoPoll = videoPoll;
    </script>
</x-app-layout>
