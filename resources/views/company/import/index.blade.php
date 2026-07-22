<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-eazy-50 flex items-center justify-center">
                    <i class="fa-solid fa-file-import text-eazy"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Voorraad importeren</h1>
                    <p class="text-sm text-[#215558] opacity-50">Zet je hele voorraad in een keer in het platform. Geimporteerde auto's komen als concept binnen, zodat je ze nog kunt nakijken.</p>
                </div>
            </div>

            {{-- Resultaat --}}
            @if(session('import_result'))
                @php $r = session('import_result'); @endphp
                <div class="mb-6 bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center"><i class="fa-solid fa-circle-check text-emerald-600"></i></div>
                        <div>
                            <p class="text-sm font-black text-[#215558]">{{ $r['created'] }} auto{{ $r['created'] === 1 ? '' : "'s" }} geimporteerd</p>
                            <p class="text-xs text-[#215558] opacity-50">Ze staan nu bij Auto's. <a href="{{ route('cars.index') }}" class="text-eazy font-semibold hover:underline">Bekijk voorraad</a></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        @if(!empty($r['duplicates']))
                            <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                                <p class="font-bold text-amber-700 text-xs uppercase tracking-wide mb-1">{{ count($r['duplicates']) }} bestonden al</p>
                                <p class="text-amber-700/80 text-[12px] break-words">{{ implode(', ', $r['duplicates']) }}</p>
                            </div>
                        @endif
                        @if(!empty($r['notfound']))
                            <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-3">
                                <p class="font-bold text-red-600 text-xs uppercase tracking-wide mb-1">{{ count($r['notfound']) }} niet gevonden bij RDW</p>
                                <p class="text-red-600/80 text-[12px] break-words">{{ implode(', ', $r['notfound']) }}</p>
                            </div>
                        @endif
                        @if(!empty($r['errors']))
                            <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 sm:col-span-2">
                                @foreach($r['errors'] as $err)
                                    <p class="text-gray-500 text-[12px]">{{ $err }}</p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Foto-resultaat (ZIP) --}}
            @if(session('foto_result'))
                @php $f = session('foto_result'); @endphp
                <div class="mb-6 bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center"><i class="fa-solid fa-images text-emerald-600"></i></div>
                        <div>
                            <p class="text-sm font-black text-[#215558]">{{ $f['attached'] }} foto{{ $f['attached'] === 1 ? '' : "'s" }} gekoppeld aan {{ $f['cars'] }} auto{{ $f['cars'] === 1 ? '' : "'s" }}</p>
                            <p class="text-xs text-[#215558] opacity-50">Bekijk het resultaat bij <a href="{{ route('cars.index') }}" class="text-eazy font-semibold hover:underline">Auto's</a>.</p>
                        </div>
                    </div>
                    @if(!empty($f['unmatched']))
                        <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 text-sm mt-2">
                            <p class="font-bold text-amber-700 text-xs uppercase tracking-wide mb-1">{{ $f['unmatched_total'] }} bestand{{ $f['unmatched_total'] === 1 ? '' : 'en' }} niet gekoppeld</p>
                            <p class="text-amber-700/80 text-[12px] break-words">Geen auto met dat kenteken gevonden: {{ implode(', ', $f['unmatched']) }}{{ $f['unmatched_total'] > count($f['unmatched']) ? ', ...' : '' }}</p>
                        </div>
                    @endif
                </div>
            @endif

            <div x-data="{ tab: 'kentekens' }" class="bg-white rounded-2xl border border-[#215558]/10 overflow-hidden">
                {{-- Tabs --}}
                <div class="flex border-b border-[#215558]/10">
                    <button type="button" @click="tab = 'kentekens'"
                        class="cursor-pointer flex-1 px-4 py-4 text-sm font-bold transition-colors"
                        :class="tab === 'kentekens' ? 'text-eazy border-b-2 border-eazy' : 'text-[#215558]/40 hover:text-[#215558]/70'">
                        <i class="fa-solid fa-keyboard mr-1.5"></i> Kentekens
                    </button>
                    <button type="button" @click="tab = 'csv'"
                        class="cursor-pointer flex-1 px-4 py-4 text-sm font-bold transition-colors"
                        :class="tab === 'csv' ? 'text-eazy border-b-2 border-eazy' : 'text-[#215558]/40 hover:text-[#215558]/70'">
                        <i class="fa-solid fa-file-csv mr-1.5"></i> CSV
                    </button>
                    <button type="button" @click="tab = 'fotos'"
                        class="cursor-pointer flex-1 px-4 py-4 text-sm font-bold transition-colors"
                        :class="tab === 'fotos' ? 'text-eazy border-b-2 border-eazy' : 'text-[#215558]/40 hover:text-[#215558]/70'">
                        <i class="fa-solid fa-images mr-1.5"></i> Foto's (ZIP)
                    </button>
                </div>

                {{-- Kentekens --}}
                <div x-show="tab === 'kentekens'" class="p-6">
                    <p class="text-[13px] text-[#215558] opacity-70 mb-1">Plak je kentekens, een per regel. Het platform haalt merk, model, bouwjaar en brandstof automatisch op bij de RDW.</p>
                    <p class="text-[12px] text-[#215558] opacity-50 mb-4">Optioneel prijs en kilometerstand erachter, gescheiden door een komma of puntkomma. Bijvoorbeeld: <code class="bg-[#215558]/[0.06] px-1.5 py-0.5 rounded">12-ABC-3; 18950; 89000</code></p>

                    <form method="POST" action="{{ route('import.kentekens') }}" x-data="{ busy: false }" @submit="busy = true">
                        @csrf
                        <textarea name="kentekens" rows="8" required placeholder="12-ABC-3&#10;45-XYZ-6; 24950; 42000&#10;98-DEF-1"
                            class="block w-full px-4 py-3 rounded-xl border-[#215558]/10 text-sm font-mono tracking-wide focus:border-eazy focus:ring-eazy resize-y mb-4">{{ old('kentekens') }}</textarea>

                        <div class="flex flex-wrap items-end gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Importeren als</label>
                                <select name="status" class="block w-full sm:w-48 px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    <option value="draft" selected>Concept (nakijken)</option>
                                    <option value="active">Direct actief</option>
                                </select>
                            </div>
                            <button type="submit" :disabled="busy"
                                class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark disabled:opacity-50 disabled:cursor-default transition">
                                <i class="fa-solid" :class="busy ? 'fa-spinner fa-spin' : 'fa-file-import'"></i>
                                <span x-text="busy ? 'Importeren...' : 'Importeer kentekens'"></span>
                            </button>
                        </div>
                        <p x-show="busy" class="text-[11px] text-[#215558] opacity-50 mt-2">De RDW-gegevens worden opgehaald, dit kan even duren bij veel kentekens.</p>
                    </form>
                </div>

                {{-- CSV --}}
                <div x-show="tab === 'csv'" class="p-6" style="display:none">
                    <p class="text-[13px] text-[#215558] opacity-70 mb-1">Heb je een export uit een ander systeem? Upload het als CSV.</p>
                    <p class="text-[12px] text-[#215558] opacity-50 mb-4">Herkende kolommen: kenteken, merk, model, bouwjaar, brandstof, kilometerstand, prijs, kleur, titel, beschrijving en <strong>fotos</strong> (een of meer foto-URL's, gescheiden door een spatie of <code class="bg-[#215558]/[0.06] px-1 rounded">|</code>). <a href="{{ route('import.template') }}" class="text-eazy font-semibold hover:underline">Download voorbeeld-CSV</a></p>

                    <form method="POST" action="{{ route('import.csv') }}" enctype="multipart/form-data" x-data="{ busy: false, file: '' }" @submit="busy = true">
                        @csrf
                        <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-[#215558]/15 rounded-xl py-8 cursor-pointer hover:border-eazy hover:bg-eazy-50/40 transition mb-4">
                            <i class="fa-solid fa-file-csv text-2xl text-[#215558] opacity-40"></i>
                            <span class="text-sm text-[#215558] opacity-70" x-text="file || 'Klik om je CSV-bestand te kiezen'"></span>
                            <input type="file" name="csv" accept=".csv,text/csv,text/plain" class="hidden" required
                                   @change="file = $event.target.files[0]?.name || ''">
                        </label>

                        <label class="flex items-center gap-2 mb-4 cursor-pointer">
                            <input type="checkbox" name="verrijk_rdw" value="1" checked class="rounded border-[#215558]/20 text-eazy focus:ring-eazy">
                            <span class="text-[13px] text-[#215558] opacity-80">Aanvullen met RDW-gegevens op basis van het kenteken</span>
                        </label>

                        <div class="flex flex-wrap items-end gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Importeren als</label>
                                <select name="status" class="block w-full sm:w-48 px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    <option value="draft" selected>Concept (nakijken)</option>
                                    <option value="active">Direct actief</option>
                                </select>
                            </div>
                            <button type="submit" :disabled="busy"
                                class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark disabled:opacity-50 disabled:cursor-default transition">
                                <i class="fa-solid" :class="busy ? 'fa-spinner fa-spin' : 'fa-upload'"></i>
                                <span x-text="busy ? 'Importeren...' : 'Importeer CSV'"></span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Foto's via ZIP --}}
                <div x-show="tab === 'fotos'" class="p-6" style="display:none">
                    <p class="text-[13px] text-[#215558] opacity-70 mb-1">Koppel foto's aan je auto's met een ZIP-bestand.</p>
                    <p class="text-[12px] text-[#215558] opacity-50 mb-4">Importeer <strong>eerst</strong> je auto's (kentekens of CSV). Zet de foto's daarna in een ZIP en begin elke bestandsnaam met het kenteken, bijvoorbeeld <code class="bg-[#215558]/[0.06] px-1.5 py-0.5 rounded">12ABC3_1.jpg</code>, <code class="bg-[#215558]/[0.06] px-1.5 py-0.5 rounded">12ABC3_2.jpg</code>. De foto's worden op volgorde gekoppeld; de eerste wordt de hoofdfoto.</p>

                    <form method="POST" action="{{ route('import.fotos') }}" enctype="multipart/form-data" x-data="{ busy: false, file: '' }" @submit="busy = true">
                        @csrf
                        <label class="flex flex-col items-center justify-center gap-2 border-2 border-dashed border-[#215558]/15 rounded-xl py-8 cursor-pointer hover:border-eazy hover:bg-eazy-50/40 transition mb-4">
                            <i class="fa-solid fa-file-zipper text-2xl text-[#215558] opacity-40"></i>
                            <span class="text-sm text-[#215558] opacity-70" x-text="file || 'Klik om je ZIP met foto\'s te kiezen'"></span>
                            <input type="file" name="zip" accept=".zip,application/zip" class="hidden" required
                                   @change="file = $event.target.files[0]?.name || ''">
                        </label>
                        <p class="text-[11px] text-[#215558] opacity-50 mb-4">ZIP met JPG, PNG of WebP, tot 200 MB. Max 30 foto's per auto.</p>

                        <button type="submit" :disabled="busy"
                            class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark disabled:opacity-50 disabled:cursor-default transition">
                            <i class="fa-solid" :class="busy ? 'fa-spinner fa-spin' : 'fa-images'"></i>
                            <span x-text="busy ? 'Foto\'s koppelen...' : 'Upload foto\'s'"></span>
                        </button>
                        <p x-show="busy" class="text-[11px] text-[#215558] opacity-50 mt-2">De foto's worden gekoppeld, dit kan even duren bij een grote ZIP.</p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
