<x-app-layout>
    @php
        $fonts = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Playfair Display', 'Oswald', 'Raleway', 'Nunito'];
        $primary = $company->brand('primary_color', $company->embed_settings['primary_color'] ?? '#0F9B9F');
        $secondary = $company->brand('secondary_color', '#215558');
        $accent = $company->brand('accent_color', '#0B7A7D');
        $fontHeading = $company->brand('font_heading', 'Inter');
        $fontBody = $company->brand('font_body', 'Inter');
        $logoUrl = $company->logo_path ? asset('storage/' . $company->logo_path) : null;
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i><span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i><span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-eazy-50 flex items-center justify-center"><i class="fa-solid fa-swatchbook text-eazy"></i></div>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Brandbook</h1>
                    <p class="text-sm text-[#215558] opacity-50">Je merkidentiteit op een plek, en een AI-logo generator die erbij past.</p>
                </div>
            </div>

            {{-- Logo + AI generator --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-6" x-data="logoGen()">
                <h3 class="text-sm font-bold text-[#215558] mb-4">Logo</h3>
                <div class="flex flex-col sm:flex-row gap-6">
                    <div class="shrink-0">
                        <div class="w-32 h-32 rounded-2xl border border-[#215558]/10 bg-[#f8fafa] flex items-center justify-center overflow-hidden">
                            @if($logoUrl)
                                <img src="{{ $logoUrl }}" alt="Logo" class="max-w-full max-h-full object-contain">
                            @else
                                <span class="text-xs text-[#215558] opacity-40 text-center px-2">Nog geen logo</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-[#215558] opacity-50 mt-2 text-center">Huidig logo</p>
                    </div>
                    <div class="flex-1">
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Genereer een logo met AI</label>
                        <textarea x-model="style" rows="2" placeholder="Beschrijf de stijl, bijv. strak en modern met een auto-silhouet, of een rond embleem in donkergroen" class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy resize-none"></textarea>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach(['Strak en modern, minimalistisch embleem', 'Rond embleem met een auto-silhouet', 'Luxe en clean, alleen een sterk symbool', 'Stoer en industrieel, vet logomerk'] as $s)
                                <button type="button" @click="style = @js($s)" class="cursor-pointer text-[11px] px-2.5 py-1 rounded-full border border-[#215558]/10 text-[#215558] hover:border-eazy hover:bg-eazy-50 transition">{{ \Illuminate\Support\Str::limit($s, 34) }}</button>
                            @endforeach
                        </div>
                        <button type="button" @click="generate()" :disabled="loading" class="cursor-pointer mt-3 inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark disabled:opacity-50 transition">
                            <i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'"></i>
                            <span x-text="loading ? 'Genereren...' : 'Genereer 4 concepten'"></span>
                        </button>
                        <p x-show="error" x-text="error" class="text-xs text-red-600 mt-2"></p>
                        <p class="text-[11px] text-[#215558] opacity-40 mt-2">AI maakt logo-concepten als startpunt. Tekst in AI-logo's kan rommelig zijn; het symbool is het bruikbaarst. Voor een definitief logo laat je het concept natekenen/vectoriseren.</p>

                        <div x-show="images.length" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                            <template x-for="(img, i) in images" :key="i">
                                <div class="rounded-xl border border-[#215558]/10 overflow-hidden bg-white">
                                    <img :src="img" alt="" class="w-full aspect-square object-contain bg-[#f8fafa]">
                                    <div class="flex items-center justify-between gap-1 p-1.5">
                                        <form method="POST" action="{{ route('brandbook.logo.use') }}" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="image_url" :value="img">
                                            <button type="submit" class="cursor-pointer w-full text-[11px] font-bold text-eazy hover:underline">Gebruik</button>
                                        </form>
                                        <a :href="img" download target="_blank" rel="noopener" class="cursor-pointer text-[#215558] opacity-40 hover:opacity-100" title="Download"><i class="fa-solid fa-download text-[11px]"></i></a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Merkbasis --}}
            <form method="POST" action="{{ route('brandbook.update') }}" class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                @csrf @method('PUT')
                <h3 class="text-sm font-bold text-[#215558] mb-4">Merkbasis</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Slogan / pay-off</label>
                        <input type="text" name="tagline" value="{{ $company->brand('tagline', '') }}" maxlength="140" placeholder="bijv. Jouw betrouwbare occasioncentrum" class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Stijl-omschrijving</label>
                        <input type="text" name="style" value="{{ $company->brand('style', '') }}" maxlength="255" placeholder="bijv. modern, clean, premium" class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                    </div>
                </div>

                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 mt-5">Kleuren</label>
                <div class="grid grid-cols-3 gap-4">
                    @foreach([['primary_color', 'Primair', $primary], ['secondary_color', 'Secundair', $secondary], ['accent_color', 'Accent', $accent]] as [$name, $label, $val])
                        <div>
                            <span class="block text-[11px] text-[#215558] opacity-50 mb-1">{{ $label }}</span>
                            <div class="flex items-center gap-2">
                                <input type="color" name="{{ $name }}" value="{{ $val }}" class="h-9 w-12 rounded-lg border border-[#215558]/10 cursor-pointer bg-white p-0.5">
                                <span class="text-xs font-mono text-[#215558] opacity-60">{{ strtoupper($val) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
                    <div>
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Lettertype koppen</label>
                        <select name="font_heading" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            @foreach($fonts as $f)<option value="{{ $f }}" @selected($fontHeading === $f)>{{ $f }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Lettertype tekst</label>
                        <select name="font_body" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            @foreach($fonts as $f)<option value="{{ $f }}" @selected($fontBody === $f)>{{ $f }}</option>@endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Tone of voice</label>
                    <textarea name="tone" rows="3" maxlength="1500" placeholder="Hoe klinkt je merk? Bijv. nuchter, vriendelijk en deskundig, geen verkooppraat." class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy resize-none">{{ $company->brand('tone', '') }}</textarea>
                </div>

                <button type="submit" class="cursor-pointer mt-5 inline-flex items-center gap-2 px-6 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Brandbook opslaan
                </button>
            </form>
        </div>
    </div>

    <script>
        function logoGen() {
            return {
                style: '',
                loading: false,
                error: '',
                images: [],
                async generate() {
                    this.loading = true;
                    this.error = '';
                    this.images = [];
                    try {
                        const r = await fetch('{{ route('brandbook.logo') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ style: this.style }),
                        });
                        const j = await r.json();
                        if (r.ok && j.images) { this.images = j.images; }
                        else { this.error = j.error || 'Genereren mislukt.'; }
                    } catch (e) {
                        this.error = 'Verbinding mislukt. Probeer het opnieuw.';
                    }
                    this.loading = false;
                },
            };
        }
    </script>
</x-app-layout>
