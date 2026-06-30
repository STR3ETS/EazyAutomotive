<x-app-layout>
    @php
        $fonts = ['Inter', 'Inter Tight', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Playfair Display', 'Oswald', 'Raleway'];
        $fontHeading = $company->brand('font_heading', 'Inter Tight');
        $fontBody = $company->brand('font_body', 'Inter Tight');
        $logoUrl = $company->logo_path ? asset('storage/' . $company->logo_path) : null;
        $initials = collect(explode(' ', $company->name))->filter()->take(2)->map(fn ($w) => strtoupper($w[0] ?? ''))->implode('') ?: 'EA';
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-3 rounded-xl bg-[rgb(22_163_74_/_0.1)] text-sm font-semibold text-[#16a34a]"><i class="fa-solid fa-circle-check mr-1.5"></i>{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-3 rounded-xl bg-[rgb(239_68_68_/_0.1)] text-sm font-semibold text-[#dc2626]"><i class="fa-solid fa-triangle-exclamation mr-1.5"></i>{{ session('error') }}</div>
            @endif

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-eazy-50 flex items-center justify-center"><i class="fa-solid fa-swatchbook text-eazy"></i></div>
                <div>
                    <h1 class="heading-2">Brandbook</h1>
                    <p class="text-helper">De huisstijl en componenten van EazyAutomotive, plus een AI-logo generator.</p>
                </div>
            </div>

            {{-- Logo + AI generator --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm" x-data="logoGen()">
                <h3 class="heading-3 mb-4">Logo</h3>
                <div class="flex flex-col sm:flex-row gap-6">
                    <div class="shrink-0">
                        <div class="w-32 h-32 rounded-2xl border border-gray-200 bg-[#f8fafa] flex items-center justify-center overflow-hidden">
                            @if($logoUrl)<img src="{{ $logoUrl }}" alt="Logo" class="max-w-full max-h-full object-contain">@else<span class="text-helper text-center px-2">Nog geen logo</span>@endif
                        </div>
                        <p class="text-helper mt-2 text-center">Huidig logo</p>
                    </div>
                    <div class="flex-1">
                        <label class="text-sm font-medium text-[#215558] mb-1.5 block">Genereer een logo met AI</label>
                        <textarea x-model="style" rows="2" placeholder="Beschrijf de stijl, bijv. strak en modern met een auto-silhouet, of een rond embleem in donkergroen" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-[#215558] placeholder-gray-400 focus:outline-none focus:border-[#0F9B9F] focus:ring-1 focus:ring-[#0F9B9F] transition resize-none"></textarea>
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach(['Strak en modern, minimalistisch embleem', 'Rond embleem met een auto-silhouet', 'Luxe en clean, alleen een sterk symbool', 'Stoer en industrieel, vet logomerk'] as $s)
                                <button type="button" @click="style = @js($s)" class="cursor-pointer text-[11px] px-2.5 py-1 rounded-full border border-gray-200 text-[#215558] hover:border-[#0F9B9F] hover:bg-eazy-50 transition">{{ \Illuminate\Support\Str::limit($s, 34) }}</button>
                            @endforeach
                        </div>
                        <button type="button" @click="generate()" :disabled="loading" class="btn-primary mt-3">
                            <i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'"></i>
                            <span x-text="loading ? 'Genereren...' : 'Genereer 4 concepten'"></span>
                        </button>
                        <p x-show="error" x-text="error" class="text-sm font-semibold text-[#dc2626] mt-2"></p>
                        <p class="text-helper mt-2">AI maakt concepten als startpunt. Tekst in AI-logo's kan rommelig zijn; het symbool is het bruikbaarst. Laat een concept natekenen/vectoriseren voor een definitief logo.</p>

                        <div x-show="images.length" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
                            <template x-for="(img, i) in images" :key="i">
                                <div class="rounded-xl border border-gray-200 overflow-hidden bg-white hover-lift">
                                    <img :src="img" alt="" class="w-full aspect-square object-contain bg-[#f8fafa]">
                                    <div class="flex items-center justify-between gap-1 p-1.5">
                                        <form method="POST" action="{{ route('brandbook.logo.use') }}" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="image_url" :value="img">
                                            <button type="submit" class="cursor-pointer w-full text-[11px] font-bold text-eazy hover-underline">Gebruik</button>
                                        </form>
                                        <a :href="img" download target="_blank" rel="noopener" class="cursor-pointer text-[#215558]/40 hover:text-[#215558]" title="Download"><i class="fa-solid fa-download text-[11px]"></i></a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kleuren --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <h3 class="heading-3 mb-4">Kleuren</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([
                        ['Primary', '#0F9B9F', 'hover #0d8a8e', '#fff'],
                        ['Secondary', '#215558', 'hover #1a4446', '#fff'],
                        ['Pagina', '#EEEEEE', 'achtergrond', '#215558'],
                        ['Cards', '#FFFFFF', 'wit', '#215558'],
                        ['Tekst donker', '#215558', '', '#fff'],
                        ['Tekst helper', '#5A7B7D', 'rgb(33 85 88 / .6)', '#fff'],
                    ] as [$naam, $hex, $sub, $txt])
                        <div class="rounded-xl overflow-hidden border border-gray-200">
                            <div class="h-16 flex items-end p-2" style="background:{{ $hex }}"><span class="text-[10px] font-mono" style="color:{{ $txt }}">{{ strtoupper($hex) }}</span></div>
                            <div class="p-2">
                                <div class="text-xs font-bold text-[#215558]">{{ $naam }}</div>
                                @if($sub)<div class="text-helper text-[10px]">{{ $sub }}</div>@endif
                            </div>
                        </div>
                    @endforeach
                    <div class="rounded-xl overflow-hidden border border-gray-200 col-span-2">
                        <div class="h-16 bg-gradient-to-r from-[#1a8a8e] via-[#5ec6c8] to-[#1a8a8e]"></div>
                        <div class="p-2"><div class="text-xs font-bold text-[#215558]">Auth gradient</div><div class="text-helper text-[10px]">from-[#1a8a8e] via-[#5ec6c8] to-[#1a8a8e]</div></div>
                    </div>
                </div>
            </div>

            {{-- Typografie --}}
            <div class="bg-white rounded-2xl p-8 shadow-sm">
                <h3 class="heading-3 mb-4">Typografie <span class="text-helper font-normal">Inter Tight</span></h3>
                <div class="space-y-2">
                    <div class="heading-2">Heading 2 voorbeeld</div>
                    <div class="heading-3">Heading 3 voorbeeld</div>
                    <div class="heading-4">Heading 4 voorbeeld</div>
                    <p class="text-body">Body tekst (text-body): de standaard leestekst in #215558 voor alledaagse content op de pagina.</p>
                    <p class="text-helper">Helper tekst (text-helper): subtiele subtekst in rgb(33 85 88 / 0.6).</p>
                    <p class="font-hand text-2xl text-eazy-darker">Handgeschreven accent (Caveat)</p>
                </div>
            </div>

            {{-- Knoppen + labels --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <h3 class="heading-3 mb-4">Knoppen</h3>
                    <div class="flex flex-wrap gap-3">
                        <button type="button" class="btn-primary"><i class="fa-solid fa-plus"></i> Primary</button>
                        <button type="button" class="btn-secondary"><i class="fa-solid fa-gear"></i> Secondary</button>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <h3 class="heading-3 mb-4">Status labels</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="label">label</span>
                        <span class="label label-success">Succes</span>
                        <span class="label label-warning">Waarschuwing</span>
                        <span class="label label-danger">Fout</span>
                        <span class="label label-info">Info</span>
                        <span class="label label-neutral">Neutraal</span>
                    </div>
                </div>
            </div>

            {{-- Inputs + meldingen --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <h3 class="heading-3 mb-4">Formulier-inputs</h3>
                    <label class="text-sm font-medium text-[#215558] mb-1.5 block">Met icoon</label>
                    <div class="relative mb-4">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" placeholder="naam@bedrijf.nl" class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 text-sm text-[#215558] placeholder-gray-400 focus:outline-none focus:border-[#0F9B9F] focus:ring-1 focus:ring-[#0F9B9F] transition">
                    </div>
                    <label class="text-sm font-medium text-[#215558] mb-1.5 block">Zonder icoon</label>
                    <input type="text" placeholder="Tekst" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-[#215558] placeholder-gray-400 focus:outline-none focus:border-[#0F9B9F] focus:ring-1 focus:ring-[#0F9B9F] transition">
                    <label class="flex items-center gap-2 mt-4 cursor-pointer"><input type="checkbox" class="checkbox-custom" checked> <span class="text-body">Custom checkbox</span></label>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <h3 class="heading-3 mb-4">Meldingen</h3>
                    <div class="p-3 rounded-xl bg-[rgb(22_163_74_/_0.1)] text-sm font-semibold text-[#16a34a] mb-3"><i class="fa-solid fa-circle-check mr-1.5"></i>Succesmelding</div>
                    <div class="p-3 rounded-xl bg-[rgb(239_68_68_/_0.1)] text-sm font-semibold text-[#dc2626]"><i class="fa-solid fa-triangle-exclamation mr-1.5"></i>Foutmelding</div>
                </div>
            </div>

            {{-- Sidebar + avatar + iconen --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <h3 class="heading-3 mb-4">Sidebar-items</h3>
                    <div class="space-y-1.5 max-w-xs">
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[#215558] bg-[#0F9B9F]/10">
                            <span class="w-8 h-8 rounded-lg bg-[#0F9B9F] text-white flex items-center justify-center text-sm"><i class="fa-solid fa-house"></i></span>
                            <span class="text-sm font-semibold">Actief</span>
                        </div>
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[#215558]/80 hover:bg-[#215558]/5">
                            <span class="w-8 h-8 rounded-lg bg-[#215558]/8 text-[#215558]/50 flex items-center justify-center text-sm"><i class="fa-solid fa-users"></i></span>
                            <span class="text-sm font-semibold">Inactief</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl p-8 shadow-sm">
                    <h3 class="heading-3 mb-4">Avatar & iconen</h3>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-9 h-9 bg-[#0F9B9F] rounded-full text-sm text-white font-bold flex items-center justify-center">{{ $initials }}</span>
                        <span class="text-helper">Initialen-avatar</span>
                    </div>
                    <div class="flex flex-wrap gap-3 text-[#215558]/70 text-lg">
                        @foreach(['fa-house', 'fa-envelope', 'fa-lock', 'fa-user', 'fa-building', 'fa-gear', 'fa-right-from-bracket', 'fa-plus', 'fa-users', 'fa-chart-bar'] as $ic)
                            <i class="fa-solid {{ $ic }}" title="{{ $ic }}"></i>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Jouw merk --}}
            <form method="POST" action="{{ route('brandbook.update') }}" class="bg-white rounded-2xl p-8 shadow-sm">
                @csrf @method('PUT')
                <h3 class="heading-3 mb-1">Jouw merk</h3>
                <p class="text-helper mb-4">Slogan, lettertypes en tone of voice van jouw bedrijf.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-sm font-medium text-[#215558] mb-1.5 block">Slogan / pay-off</label>
                        <input type="text" name="tagline" value="{{ $company->brand('tagline', '') }}" maxlength="140" placeholder="bijv. Jouw betrouwbare occasioncentrum" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-[#215558] placeholder-gray-400 focus:outline-none focus:border-[#0F9B9F] focus:ring-1 focus:ring-[#0F9B9F] transition">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-[#215558] mb-1.5 block">Stijl-omschrijving</label>
                        <input type="text" name="style" value="{{ $company->brand('style', '') }}" maxlength="255" placeholder="bijv. modern, clean, premium" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-[#215558] placeholder-gray-400 focus:outline-none focus:border-[#0F9B9F] focus:ring-1 focus:ring-[#0F9B9F] transition">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-[#215558] mb-1.5 block">Lettertype koppen</label>
                        <select name="font_heading" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-[#215558] focus:outline-none focus:border-[#0F9B9F] focus:ring-1 focus:ring-[#0F9B9F] transition">
                            @foreach($fonts as $f)<option value="{{ $f }}" @selected($fontHeading === $f)>{{ $f }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-[#215558] mb-1.5 block">Lettertype tekst</label>
                        <select name="font_body" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-[#215558] focus:outline-none focus:border-[#0F9B9F] focus:ring-1 focus:ring-[#0F9B9F] transition">
                            @foreach($fonts as $f)<option value="{{ $f }}" @selected($fontBody === $f)>{{ $f }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-5">
                    <label class="text-sm font-medium text-[#215558] mb-1.5 block">Tone of voice</label>
                    <textarea name="tone" rows="3" maxlength="1500" placeholder="Hoe klinkt je merk? Bijv. nuchter, vriendelijk en deskundig, geen verkooppraat." class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm text-[#215558] placeholder-gray-400 focus:outline-none focus:border-[#0F9B9F] focus:ring-1 focus:ring-[#0F9B9F] transition resize-none">{{ $company->brand('tone', '') }}</textarea>
                </div>
                <button type="submit" class="btn-primary mt-5"><i class="fa-solid fa-floppy-disk"></i> Opslaan</button>
            </form>
        </div>
    </div>

    <script>
        function logoGen() {
            return {
                style: '', loading: false, error: '', images: [],
                async generate() {
                    this.loading = true; this.error = ''; this.images = [];
                    try {
                        const r = await fetch('{{ route('brandbook.logo') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ style: this.style }),
                        });
                        const j = await r.json();
                        if (r.ok && j.images) { this.images = j.images; } else { this.error = j.error || 'Genereren mislukt.'; }
                    } catch (e) { this.error = 'Verbinding mislukt. Probeer het opnieuw.'; }
                    this.loading = false;
                },
            };
        }
    </script>
</x-app-layout>
