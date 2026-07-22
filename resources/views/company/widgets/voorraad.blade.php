<x-app-layout>
    @php
        $s = $company->embed_settings ?? [];
        $val = fn ($k, $d) => $s[$k] ?? $d;
        $fonts = ['system' => 'Systeem (standaard)', 'Inter' => 'Inter', 'Roboto' => 'Roboto', 'Open Sans' => 'Open Sans', 'Lato' => 'Lato', 'Montserrat' => 'Montserrat', 'Poppins' => 'Poppins'];
    @endphp

    <style>
        .pv-root { --p:#0F9B9F; --cardbg:#fff; --radius:12px; --imgh:170px; --font:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif; --shadow:none; }
        .pv-grid { display:grid; gap:12px; }
        .pv-card { background:var(--cardbg); border:1px solid #eef1f4; border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow); transition:transform .18s, box-shadow .18s; font-family:var(--font); }
        .pv-root[data-hover="lift"] .pv-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,.12); }
        .pv-root[data-hover="scale"] .pv-card:hover { transform:scale(1.03); }
        .pv-root[data-hover="shadow"] .pv-card:hover { box-shadow:0 12px 35px rgba(0,0,0,.18); }
        .pv-root[data-hover="glow"] .pv-card:hover { box-shadow:0 0 20px var(--p); }
        .pv-img { height:var(--imgh); background:linear-gradient(135deg,#dfe7e9,#c4d3d6); display:flex; align-items:center; justify-content:center; color:#8aa1a4; font-size:11px; }
        .pv-body { padding:12px; }
        .pv-title { font-size:14px; font-weight:700; color:#111827; font-family:var(--font); }
        .pv-price { font-size:18px; font-weight:800; color:var(--p); margin-top:2px; font-family:var(--font); }
        .pv-labels { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; }
        .pv-label { font-size:11px; display:inline-flex; align-items:center; gap:4px; color:#4b5563; }
        .pv-root[data-label="badge"] .pv-label { background:#f3f4f6; padding:3px 8px; border-radius:5px; }
        .pv-root[data-label="pill"] .pv-label { background:#f3f4f6; padding:3px 10px; border-radius:9999px; }
        .pv-root[data-label="outline"] .pv-label { border:1px solid #d1d5db; padding:3px 8px; border-radius:6px; }
        .pv-root[data-label="icon-text"] .pv-label { padding:2px 0; }
        .ctl-label { display:block; font-size:11px; font-weight:700; color:#215558; opacity:.8; text-transform:uppercase; letter-spacing:.04em; margin-bottom:6px; }
        .ctl-card { background:#fff; border:1px solid rgba(33,85,88,.1); border-radius:16px; padding:20px; }
    </style>

    <div class="py-8" x-data="aiDesigner()">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i><span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('widgets.index') }}" class="w-9 h-9 rounded-lg bg-white border border-[#215558]/10 flex items-center justify-center text-[#215558] hover:border-eazy transition"><i class="fa-solid fa-arrow-left text-sm"></i></a>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Voorraad-widget</h1>
                    <p class="text-sm text-[#215558] opacity-50">Ontwerp je autoaanbod en haal de code voor je website op.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('widgets.voorraad.update') }}" id="designForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    {{-- Controls --}}
                    <div class="lg:col-span-3 space-y-5">

                        {{-- Thema's --}}
                        <div class="ctl-card">
                            <span class="ctl-label">Thema's</span>
                            <p class="text-xs text-[#215558] opacity-50 mb-3 -mt-1">Kies een startpunt, pas daarna gerust verder aan.</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($themes as $t)
                                    <button type="button" onclick='applyTheme(@json($t))'
                                        class="cursor-pointer text-left border border-[#215558]/10 rounded-xl p-3 hover:border-eazy hover:bg-eazy-50/40 transition">
                                        <span class="flex items-center gap-2 mb-1.5">
                                            <span class="w-4 h-4 rounded-full" style="background: {{ $t['primary_color'] }}"></span>
                                            <span class="text-xs font-bold text-[#215558]">{{ $t['naam'] }}</span>
                                        </span>
                                        <span class="flex gap-1">
                                            <span class="h-1.5 w-6 rounded-full" style="background: {{ $t['primary_color'] }}"></span>
                                            <span class="h-1.5 w-3 rounded-full bg-[#215558]/10"></span>
                                            <span class="h-1.5 w-2 rounded-full bg-[#215558]/10"></span>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- AI-designer --}}
                        <div class="rounded-2xl border border-eazy/20 bg-gradient-to-br from-eazy-50/60 to-white p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-eazy"></i>
                                <h3 class="text-sm font-bold text-[#215558]">Genereer met AI</h3>
                            </div>
                            <p class="text-xs text-[#215558] opacity-60 mb-3">Beschrijf de gewenste stijl, of plak een website-URL om de kleuren en het lettertype over te nemen.</p>
                            <input type="text" x-model="prompt" placeholder="Bijv. strak en modern, donkerblauw, ronde hoeken"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy mb-2">
                            <div class="flex gap-2">
                                <input type="url" x-model="url" placeholder="https://jouwwebsite.nl (optioneel)"
                                    class="block flex-1 px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <button type="button" @click="generate()" :disabled="loading"
                                    class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 bg-eazy text-white rounded-xl text-sm font-bold hover:bg-eazy-dark disabled:opacity-50 transition shrink-0">
                                    <i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'"></i>
                                    <span x-text="loading ? 'Bezig...' : 'Genereer'"></span>
                                </button>
                            </div>
                            <p x-show="error" x-text="error" class="text-xs text-red-600 mt-2" style="display:none"></p>
                            <p x-show="note" x-text="note" class="text-xs text-emerald-600 mt-2" style="display:none"></p>
                        </div>

                        {{-- Layout --}}
                        <div class="ctl-card">
                            <span class="ctl-label">Layout</span>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kolommen</label>
                                    <select name="columns" class="preview-input block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                        @foreach([1,2,3,4] as $c)<option value="{{ $c }}" @selected($val('columns',3)==$c)>{{ $c }}</option>@endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Foto-positie</label>
                                    <select name="image_position" class="preview-input block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                        <option value="top" @selected($val('image_position','top')==='top')>Boven</option>
                                        <option value="bottom" @selected($val('image_position','top')==='bottom')>Onder</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="flex items-center justify-between text-xs font-semibold text-[#374151] mb-1.5">Fotohoogte <span class="text-eazy font-bold" id="ih_val">{{ $val('image_height',200) }}px</span></label>
                                    <input type="range" name="image_height" min="120" max="340" value="{{ $val('image_height',200) }}" class="preview-input w-full accent-eazy" oninput="document.getElementById('ih_val').textContent=this.value+'px'">
                                </div>
                            </div>
                        </div>

                        {{-- Stijl --}}
                        <div class="ctl-card">
                            <span class="ctl-label">Stijl</span>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Hoofdkleur</label>
                                    <input type="color" name="primary_color" value="{{ $val('primary_color','#0F9B9F') }}" class="preview-input h-10 w-full rounded-xl border border-[#215558]/10 cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kaartachtergrond</label>
                                    <input type="color" name="card_bg_color" value="{{ $val('card_bg_color','#ffffff') }}" class="preview-input h-10 w-full rounded-xl border border-[#215558]/10 cursor-pointer">
                                </div>
                                <div class="col-span-2">
                                    <label class="flex items-center justify-between text-xs font-semibold text-[#374151] mb-1.5">Ronde hoeken <span class="text-eazy font-bold" id="rad_val">{{ $val('card_border_radius',12) }}px</span></label>
                                    <input type="range" name="card_border_radius" min="0" max="30" value="{{ $val('card_border_radius',12) }}" class="preview-input w-full accent-eazy" oninput="document.getElementById('rad_val').textContent=this.value+'px'">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Schaduw</label>
                                    <select name="card_shadow" class="preview-input block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                        @foreach(['none'=>'Geen','sm'=>'Subtiel','md'=>'Normaal','lg'=>'Groot'] as $k=>$lbl)<option value="{{ $k }}" @selected($val('card_shadow','none')===$k)>{{ $lbl }}</option>@endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Hover-effect</label>
                                    <select name="hover_effect" class="preview-input block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                        @foreach(['lift'=>'Omhoog','shadow'=>'Schaduw','scale'=>'Vergroten','glow'=>'Gloed','none'=>'Geen'] as $k=>$lbl)<option value="{{ $k }}" @selected($val('hover_effect','lift')===$k)>{{ $lbl }}</option>@endforeach
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Lettertype</label>
                                    <select name="font_family" class="preview-input block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                        @foreach($fonts as $k=>$lbl)<option value="{{ $k }}" @selected($val('font_family','system')===$k)>{{ $lbl }}</option>@endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Labels & zichtbaarheid --}}
                        <div class="ctl-card">
                            <span class="ctl-label">Labels &amp; zichtbaarheid</span>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-[#374151] mb-1.5">Labelstijl</label>
                                <select name="label_style" class="preview-input block w-full sm:w-1/2 px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    @foreach(['badge'=>'Badge','pill'=>'Pil','outline'=>'Omlijnd','icon-text'=>'Alleen tekst'] as $k=>$lbl)<option value="{{ $k }}" @selected($val('label_style','badge')===$k)>{{ $lbl }}</option>@endforeach
                                </select>
                            </div>
                            <div class="flex flex-wrap gap-4">
                                @foreach(['show_price'=>'Prijs tonen','show_km'=>'Km-stand tonen','show_fuel'=>'Brandstof tonen'] as $k=>$lbl)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="{{ $k }}" value="1" @checked($val($k,true)) class="preview-input rounded border-[#215558]/20 text-eazy focus:ring-eazy">
                                        <span class="text-[13px] text-[#215558] opacity-80">{{ $lbl }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Preview + embed (sticky) --}}
                    <div class="lg:col-span-2">
                        <div class="lg:sticky lg:top-6 space-y-5">
                            <div class="ctl-card">
                                <span class="ctl-label">Voorbeeld</span>
                                <div class="pv-root bg-[#f4faf9] rounded-xl p-3 -mx-1" id="pvRoot">
                                    <div class="pv-grid" id="pvGrid">
                                        @for($i=0;$i<3;$i++)
                                            <div class="pv-card">
                                                <div class="pv-img">Foto</div>
                                                <div class="pv-body">
                                                    <div class="pv-title">Volkswagen Golf 1.0 TSI</div>
                                                    <div class="pv-price pv-show-price">&euro; 18.950</div>
                                                    <div class="pv-labels">
                                                        <span class="pv-label">2019</span>
                                                        <span class="pv-label pv-show-km">89.000 km</span>
                                                        <span class="pv-label pv-show-fuel">Benzine</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="cursor-pointer w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition">
                                <i class="fa-solid fa-floppy-disk text-xs"></i> Ontwerp opslaan
                            </button>

                            {{-- Insluitcode --}}
                            <div class="ctl-card">
                                <span class="ctl-label">Insluitcode</span>
                                <p class="text-xs text-[#215558] opacity-50 mb-3 -mt-1">Plak dit op je website waar het aanbod moet komen.</p>
                                <div class="relative">
                                    <pre class="bg-gray-900 text-green-400 rounded-xl p-4 text-[11px] overflow-x-auto leading-relaxed" id="embedCode">&lt;!-- EazyAutomotive Voorraad --&gt;
&lt;div id="eazy-automotive-widget"&gt;&lt;/div&gt;
&lt;script
  src="{{ url('/embed/v1/widget.js') }}"
  data-api-key="{{ $company->api_key }}"
  data-base-url="{{ url('/') }}"
  defer&gt;
&lt;/script&gt;</pre>
                                    <button type="button" onclick="copyCode('embedCode','embedBtn')" class="cursor-pointer absolute top-2 right-2 inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-gray-700 text-gray-300 rounded-lg text-[11px] hover:bg-gray-600 transition" id="embedBtn">
                                        <i class="fa-regular fa-copy"></i> Kopieer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const PV_SHADOWS = { none:'none', sm:'0 1px 3px rgba(0,0,0,0.08)', md:'0 4px 12px rgba(0,0,0,0.1)', lg:'0 10px 30px rgba(0,0,0,0.15)' };
        const PV_FONTS = ['Inter','Roboto','Open Sans','Lato','Montserrat','Poppins'];

        function pvFont(name) {
            if (!name || name === 'system' || PV_FONTS.indexOf(name) === -1) return "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
            const id = 'pv-font-' + name.replace(/\s/g,'-').toLowerCase();
            if (!document.getElementById(id)) {
                const l = document.createElement('link'); l.id = id; l.rel = 'stylesheet';
                l.href = 'https://fonts.googleapis.com/css2?family=' + name.replace(/\s/g,'+') + ':wght@400;500;600;700&display=swap';
                document.head.appendChild(l);
            }
            return "'" + name + "',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
        }

        function fld(name) { return document.getElementById('designForm').elements[name]; }

        function updatePreview() {
            const root = document.getElementById('pvRoot');
            root.style.setProperty('--p', fld('primary_color').value);
            root.style.setProperty('--cardbg', fld('card_bg_color').value);
            root.style.setProperty('--radius', fld('card_border_radius').value + 'px');
            root.style.setProperty('--imgh', fld('image_height').value + 'px');
            root.style.setProperty('--shadow', PV_SHADOWS[fld('card_shadow').value] || 'none');
            root.style.setProperty('--font', pvFont(fld('font_family').value));
            root.dataset.hover = fld('hover_effect').value;
            root.dataset.label = fld('label_style').value;
            document.getElementById('pvGrid').style.gridTemplateColumns = 'repeat(' + Math.min(2, parseInt(fld('columns').value, 10) || 2) + ', 1fr)';
            root.querySelectorAll('.pv-show-price').forEach(e => e.style.display = fld('show_price').checked ? '' : 'none');
            root.querySelectorAll('.pv-show-km').forEach(e => e.style.display = fld('show_km').checked ? '' : 'none');
            root.querySelectorAll('.pv-show-fuel').forEach(e => e.style.display = fld('show_fuel').checked ? '' : 'none');
        }

        function applyTheme(t) {
            const set = { primary_color:t.primary_color, card_bg_color:t.card_bg_color, card_border_radius:t.card_border_radius, card_shadow:t.card_shadow, hover_effect:t.hover_effect, label_style:t.label_style, font_family:t.font_family };
            for (const k in set) { if (fld(k) != null && set[k] != null) fld(k).value = set[k]; }
            document.getElementById('rad_val').textContent = t.card_border_radius + 'px';
            updatePreview();
        }

        /* AI: mapt teruggegeven instellingen op de aanwezige velden */
        function applyDesignSettings(s) {
            ['primary_color','card_bg_color','card_border_radius','card_shadow','hover_effect','label_style','font_family','columns','image_height','image_position'].forEach(k => {
                if (s[k] != null && fld(k) != null) fld(k).value = s[k];
            });
            if (s.card_border_radius != null) document.getElementById('rad_val').textContent = s.card_border_radius + 'px';
            if (s.image_height != null) document.getElementById('ih_val').textContent = s.image_height + 'px';
            updatePreview();
        }

        function copyCode(preId, btnId) {
            const code = document.getElementById(preId).textContent.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
            navigator.clipboard.writeText(code);
            const btn = document.getElementById(btnId);
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Gekopieerd!';
            setTimeout(() => btn.innerHTML = '<i class="fa-regular fa-copy"></i> Kopieer', 2000);
        }

        function aiDesigner() {
            return {
                prompt: '', url: '', loading: false, error: '', note: '',
                async generate() {
                    this.error = ''; this.note = '';
                    if (!this.prompt.trim()) { this.error = 'Beschrijf eerst de gewenste stijl.'; return; }
                    this.loading = true;
                    try {
                        const res = await fetch('{{ route('widgets.ai') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ prompt: this.prompt, url: this.url || null }),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.error = data.error || 'Genereren mislukt.'; return; }
                        applyDesignSettings(data.settings || {});
                        this.note = 'Ontwerp toegepast. Controleer het voorbeeld en klik op opslaan.';
                    } catch (e) { this.error = 'Verbinding mislukt. Probeer het opnieuw.'; }
                    finally { this.loading = false; }
                },
            };
        }
        window.aiDesigner = aiDesigner;

        document.querySelectorAll('.preview-input').forEach(el => {
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        });
        updatePreview();
    </script>
</x-app-layout>
