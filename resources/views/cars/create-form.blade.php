<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg mt-0.5"></i>
                    <ul class="text-sm font-medium space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="mb-6">
                @if($manual ?? false)
                    <a href="{{ route('cars.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#215558] opacity-50 hover:opacity-100 transition mb-3">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i> Terug naar overzicht
                    </a>
                @else
                    <a href="{{ route('cars.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#215558] opacity-50 hover:opacity-100 transition mb-3">
                        <i class="fa-solid fa-arrow-left text-[10px]"></i> Ander kenteken opzoeken
                    </a>
                @endif
                <h1 class="text-2xl font-black text-[#215558]">Nieuwe auto toevoegen</h1>
                <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">{{ ($manual ?? false) ? 'Vul de gegevens van de auto in, een kenteken is optioneel' : 'Controleer de gegevens en voeg je verkoopinformatie toe' }}</p>
            </div>

            <form method="POST" action="{{ route('cars.store') }}" enctype="multipart/form-data">
                @csrf

                @if($manual ?? false)
                {{-- Manual vehicle data (no RDW) --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden mb-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                            <i class="fa-solid fa-car text-amber-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#215558]">Voertuiggegevens</h3>
                            <p class="text-xs text-[#215558] opacity-50">Vul de gegevens van de auto in. Een kenteken is optioneel.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php
                            $manualFields = [
                                ['kenteken', 'Kenteken (optioneel)', 'text', 'XX-999-X'],
                                ['merk', 'Merk', 'text', 'bijv. Volkswagen'],
                                ['handelsbenaming', 'Model', 'text', 'bijv. Golf'],
                                ['bouwjaar', 'Bouwjaar', 'number', '2019'],
                                ['brandstof_omschrijving', 'Brandstof', 'text', 'Benzine'],
                                ['eerste_kleur', 'Kleur', 'text', 'Grijs'],
                                ['inrichting', 'Carrosserie', 'text', 'Hatchback'],
                                ['aantal_deuren', 'Aantal deuren', 'number', '5'],
                                ['aantal_zitplaatsen', 'Zitplaatsen', 'number', '5'],
                            ];
                        @endphp
                        @foreach($manualFields as [$name, $label, $type, $ph])
                            <div>
                                <label for="m_{{ $name }}" class="block text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider mb-1.5">{{ $label }}</label>
                                <input type="{{ $type }}" name="{{ $name }}" id="m_{{ $name }}" value="{{ old($name) }}" placeholder="{{ $ph }}" @if($type === 'number') min="0" @endif class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/20">
                            </div>
                        @endforeach
                    </div>
                </div>
                @else
                {{-- RDW Data --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden mb-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#215558]">RDW Gegevens</h3>
                            <p class="text-xs text-[#215558] opacity-50">Automatisch opgehaald uit het kentekenregister</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach([
                            ['Kenteken', $carAttributes['kenteken'], true],
                            ['Merk', $carAttributes['merk'] ?? '-', false],
                            ['Model', $carAttributes['handelsbenaming'] ?? '-', false],
                            ['Bouwjaar', $carAttributes['bouwjaar'] ?? '-', false],
                            ['Brandstof', $carAttributes['brandstof_omschrijving'] ?? '-', false],
                            ['Kleur', $carAttributes['eerste_kleur'] ?? '-', false],
                            ['Carrosserie', $carAttributes['inrichting'] ?? '-', false],
                            ['Zitplaatsen', $carAttributes['aantal_zitplaatsen'] ?? '-', false],
                            ['APK tot', $carAttributes['vervaldatum_apk'] ?? '-', false],
                        ] as [$label, $value, $isMono])
                        <div class="bg-[#ebf2f2]/50 rounded-xl px-4 py-3">
                            <span class="text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">{{ $label }}</span>
                            <div class="{{ $isMono ? 'font-mono font-bold text-base' : 'font-semibold text-sm' }} text-[#215558] mt-0.5">{{ $value }}</div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Hidden fields for all RDW data --}}
                    @foreach($carAttributes as $key => $value)
                        @if(!is_array($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <input type="hidden" name="rdw_raw_data" value="{{ json_encode($carAttributes['rdw_raw_data'] ?? []) }}">
                </div>
                @endif

                {{-- Dealer Fields --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden mb-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                            <i class="fa-solid fa-tag text-blue-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#215558]">Verkoopgegevens</h3>
                            <p class="text-xs text-[#215558] opacity-50">Vul de prijs en overige verkoopinformatie in</p>
                        </div>
                    </div>

                    @php
                        $aiFacts = array_filter([
                            'merk' => $carAttributes['merk'] ?? null,
                            'model' => $carAttributes['handelsbenaming'] ?? null,
                            'bouwjaar' => isset($carAttributes['bouwjaar']) ? (int) $carAttributes['bouwjaar'] : null,
                            'brandstof' => $carAttributes['brandstof_omschrijving'] ?? null,
                            'kleur' => $carAttributes['eerste_kleur'] ?? null,
                            'carrosserie' => $carAttributes['inrichting'] ?? null,
                            'vermogen' => isset($carAttributes['vermogen']) ? (int) $carAttributes['vermogen'] : null,
                            'cilinderinhoud' => isset($carAttributes['cilinderinhoud']) ? (int) $carAttributes['cilinderinhoud'] : null,
                            'aantal_deuren' => isset($carAttributes['aantal_deuren']) ? (int) $carAttributes['aantal_deuren'] : null,
                            'aantal_zitplaatsen' => isset($carAttributes['aantal_zitplaatsen']) ? (int) $carAttributes['aantal_zitplaatsen'] : null,
                            'apk' => $carAttributes['vervaldatum_apk'] ?? null,
                        ], fn ($v) => $v !== null && $v !== '' && $v !== 0);
                    @endphp
                    @unless($manual ?? false)
                        @include('cars.partials.ai-copy', ['facts' => $aiFacts])
                    @endunless

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="titel" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Titel (optioneel)</label>
                            <div class="relative">
                                <i class="fa-solid fa-heading absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="text" name="titel" id="titel" value="{{ old('titel') }}"
                                    placeholder="bijv. Nette BMW 3 Serie met lage km-stand"
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/20">
                            </div>
                            <p class="mt-1 text-[11px] text-[#215558] opacity-40">Laat leeg voor automatische titel (Merk + Model)</p>
                        </div>

                        <div>
                            <label for="prijs" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Prijs</label>
                            <div class="relative">
                                <i class="fa-solid fa-euro-sign absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="number" name="prijs" id="prijs" value="{{ old('prijs') }}"
                                    placeholder="15000" step="1" min="0"
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/20">
                            </div>
                        </div>

                        <div>
                            <label for="kilometerstand" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Kilometerstand</label>
                            <div class="relative">
                                <i class="fa-solid fa-road absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="number" name="kilometerstand" id="kilometerstand" value="{{ old('kilometerstand') }}"
                                    placeholder="95000" min="0"
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/20">
                            </div>
                        </div>

                        <div x-data="{
                            open: false,
                            selected: '{{ old('status', 'active') }}',
                            options: [
                                { value: 'draft', label: 'Concept', icon: 'fa-pencil' },
                                { value: 'active', label: 'Actief', icon: 'fa-circle-check' },
                                { value: 'reserved', label: 'Gereserveerd', icon: 'fa-clock' },
                            ],
                            get selectedLabel() { return this.options.find(o => o.value === this.selected)?.label || 'Actief' },
                            get selectedIcon() { return this.options.find(o => o.value === this.selected)?.icon || 'fa-circle-check' },
                        }" @click.away="open = false" class="relative">
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Status</label>
                            <input type="hidden" name="status" :value="selected">
                            <button type="button" @click="open = !open" class="relative w-full flex items-center gap-2 pl-9 pr-10 py-2.5 rounded-full border border-[#215558]/10 bg-white text-sm text-left focus:border-eazy focus:ring-1 focus:ring-eazy transition cursor-pointer">
                                <i class="fa-solid absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm" :class="selectedIcon"></i>
                                <span class="truncate text-[#215558]" x-text="selectedLabel"></span>
                                <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-[10px] transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="absolute z-50 mt-1.5 w-full bg-white rounded-2xl border border-[#215558]/10 shadow-lg shadow-black/5 py-1.5 overflow-hidden" style="display: none;">
                                <template x-for="option in options" :key="option.value">
                                    <button type="button" @click="selected = option.value; open = false" class="cursor-pointer w-full flex items-center gap-2.5 px-4 py-2 text-sm text-left hover:bg-[#ebf2f2]/70 transition-colors" :class="{ 'bg-[#ebf2f2]/50 font-semibold text-eazy': selected === option.value, 'text-[#215558]': selected !== option.value }">
                                        <i class="fa-solid text-xs w-4 text-center" :class="option.icon + ' ' + (selected === option.value ? 'text-eazy' : 'text-[#215558]/30')"></i>
                                        <span x-text="option.label"></span>
                                        <i x-show="selected === option.value" class="fa-solid fa-check ml-auto text-eazy text-[10px]"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label for="beschrijving" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Beschrijving</label>
                        <textarea name="beschrijving" id="beschrijving" rows="4"
                            placeholder="Omschrijf de auto, bijzonderheden, opties etc."
                            class="block w-full px-4 py-2.5 rounded-2xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/20">{{ old('beschrijving') }}</textarea>
                    </div>

                    <div class="mt-5">
                        <label for="extra_opties" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Extra opties</label>
                        <div class="relative">
                            <i class="fa-solid fa-list-check absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                            <input type="text" name="extra_opties" id="extra_opties" value="{{ old('extra_opties') }}"
                                placeholder="Airco, Navigatie, Leder, Stoelverwarming"
                                class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/20">
                        </div>
                        <p class="mt-1 text-[11px] text-[#215558] opacity-40">Scheid opties met een komma</p>
                    </div>
                </div>

                {{-- Images --}}
                <div x-data="imageUpload()" class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden mb-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                        <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
                            <i class="fa-solid fa-images text-violet-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#215558]">Foto's</h3>
                            <p class="text-xs text-[#215558] opacity-50">Upload maximaal 20 foto's. De eerste wordt de hoofdfoto.</p>
                        </div>
                    </div>

                    <label for="images" class="group cursor-pointer block">
                        <div class="border-2 border-dashed border-[#215558]/15 rounded-xl p-8 text-center hover:border-eazy hover:bg-eazy-50/30 transition-colors">
                            <i class="fa-solid fa-cloud-arrow-up text-[#215558]/20 text-3xl mb-2 group-hover:text-eazy transition-colors"></i>
                            <p class="text-sm font-bold text-[#215558] opacity-60 group-hover:text-eazy group-hover:opacity-100 transition-colors">Klik om foto's te uploaden</p>
                            <p class="text-[11px] text-[#215558] opacity-40 mt-1">JPG, PNG of WebP. Max 5MB per foto.</p>
                        </div>
                    </label>
                    <input type="file" name="images[]" id="images" x-ref="input" multiple accept="image/jpeg,image/png,image/webp" class="hidden" @change="onChange()">

                    <div x-show="previews.length" class="mt-4">
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                            <template x-for="(p, i) in previews" :key="p.url">
                                <div class="relative group/thumb">
                                    <img :src="p.url" alt="" class="w-full h-24 object-cover rounded-lg border border-[#215558]/10">
                                    <span x-show="i === 0" class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded-md bg-eazy text-white text-[9px] font-bold">Hoofdfoto</span>
                                    <button type="button" @click="remove(i)" title="Verwijderen" class="cursor-pointer absolute top-1 right-1 w-6 h-6 rounded-full bg-black/60 text-white text-xs flex items-center justify-center opacity-0 group-hover/thumb:opacity-100 transition hover:bg-red-500">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <p class="text-[11px] text-[#215558] opacity-50 mt-2"><span x-text="previews.length"></span> foto('s) geselecteerd. De eerste is de hoofdfoto.</p>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-between">
                    <a href="{{ route('cars.index') }}" class="inline-flex items-center gap-1.5 text-sm text-[#215558] opacity-50 hover:opacity-100 transition font-medium">
                        <i class="fa-solid fa-xmark text-xs"></i> Annuleren
                    </a>
                    <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-8 py-3 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 hover:shadow-eazy/30 transition-all">
                        <i class="fa-solid fa-floppy-disk"></i> Auto opslaan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        /* Live preview for the photo upload: shows thumbnails, a count, a hoofdfoto badge,
           and lets you remove files before submitting. Keeps the file input in sync. */
        function imageUpload() {
            return {
                files: [],
                previews: [],
                max: 20,
                onChange() {
                    const input = this.$refs.input;
                    const incoming = Array.from(input.files).filter(f => f.type.startsWith('image/'));
                    const dt = new DataTransfer();
                    this.files.forEach(f => dt.items.add(f));
                    incoming.forEach(f => { if (dt.items.length < this.max) dt.items.add(f); });
                    input.files = dt.files;
                    this.sync();
                },
                sync() {
                    this.revoke();
                    this.files = Array.from(this.$refs.input.files);
                    this.previews = this.files.map(f => ({ name: f.name, url: URL.createObjectURL(f) }));
                },
                remove(i) {
                    const dt = new DataTransfer();
                    this.files.forEach((f, idx) => { if (idx !== i) dt.items.add(f); });
                    this.$refs.input.files = dt.files;
                    this.sync();
                },
                revoke() {
                    this.previews.forEach(p => URL.revokeObjectURL(p.url));
                },
            };
        }
        window.imageUpload = imageUpload;
    </script>
</x-app-layout>
