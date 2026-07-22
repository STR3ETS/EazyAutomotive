<x-app-layout>
    @php
        $s = $company->embed_settings ?? [];
        $val = fn ($k, $d) => $s[$k] ?? $d;
        $googleFonts = ['Inter', 'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins'];
        $shadows = ['none' => 'none', 'sm' => '0 1px 3px rgba(0,0,0,0.08)', 'md' => '0 4px 12px rgba(0,0,0,0.1)', 'lg' => '0 10px 30px rgba(0,0,0,0.15)'];
        $primary = $val('primary_color', '#0F9B9F');
        $cardbg = $val('card_bg_color', '#ffffff');
        $radius = (int) $val('card_border_radius', 12);
        $shadow = $shadows[$val('card_shadow', 'none')] ?? 'none';
        $hover = $val('hover_effect', 'lift');
        $label = $val('label_style', 'badge');
        $layout = $val('card_layout', 'classic');
        $font = $val('font_family', 'system');
        $fontStack = in_array($font, $googleFonts) ? "'{$font}',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif" : "-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif";
        $rgb = sscanf($cardbg, '#%02x%02x%02x');
        $lightBg = $rgb ? (0.299 * $rgb[0] + 0.587 * $rgb[1] + 0.114 * $rgb[2]) > 150 : true;
        $titleColor = $lightBg ? '#111827' : '#f8fafc';
        $labelBg = $lightBg ? '#f3f4f6' : 'rgba(255,255,255,.10)';
        $labelText = $lightBg ? '#4b5563' : '#e5e7eb';
        $labelBorder = $lightBg ? '#d1d5db' : 'rgba(255,255,255,.28)';
    @endphp

    @if(in_array($font, $googleFonts))
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $font) }}:wght@400;500;600;700&display=swap">
    @endif

    <style>
        .pv-grid { display:grid; gap:12px; }
        .pv-card { background:{{ $cardbg }}; border:1px solid #eef1f4; border-radius:{{ $radius }}px; overflow:hidden; box-shadow:{{ $shadow }}; transition:transform .18s, box-shadow .18s; font-family:{{ $fontStack }}; }
        @if($hover==='lift') .pv-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,.12); }
        @elseif($hover==='scale') .pv-card:hover { transform:scale(1.03); }
        @elseif($hover==='shadow') .pv-card:hover { box-shadow:0 12px 35px rgba(0,0,0,.18); }
        @elseif($hover==='glow') .pv-card:hover { box-shadow:0 0 20px {{ $primary }}; }
        @endif
        .pv-img { background:linear-gradient(135deg,#dfe7e9,#c4d3d6); display:flex; align-items:center; justify-content:center; color:#8aa1a4; font-size:11px; }
        .pv-body { padding:12px; }
        .pv-title { font-size:13px; font-weight:700; color:{{ $titleColor }}; font-family:{{ $fontStack }}; }
        .pv-price { font-size:17px; font-weight:800; color:{{ $primary }}; margin-top:2px; font-family:{{ $fontStack }}; }
        .pv-labels { display:flex; flex-wrap:wrap; gap:6px; margin-top:10px; }
        .pv-label { font-size:11px; display:inline-flex; align-items:center; gap:4px; color:{{ $labelText }};
            @if($label==='badge') background:{{ $labelBg }}; padding:3px 8px; border-radius:5px;
            @elseif($label==='pill') background:{{ $labelBg }}; padding:3px 10px; border-radius:9999px;
            @elseif($label==='outline') border:1px solid {{ $labelBorder }}; padding:3px 8px; border-radius:6px;
            @endif }
        .ctl-label { display:block; font-size:11px; font-weight:700; color:#215558; opacity:.8; text-transform:uppercase; letter-spacing:.04em; margin-bottom:6px; }
        .ctl-card { background:#fff; border:1px solid rgba(33,85,88,.1); border-radius:16px; padding:20px; }
        .pv-grid[data-layout="list"] .pv-card { display:flex; }
        .pv-grid[data-layout="list"] .pv-img { width:42%; height:auto !important; align-self:stretch; }
        .pv-grid[data-layout="list"] .pv-body { flex:1; display:flex; flex-direction:column; justify-content:center; }
        .pv-grid[data-layout="overlay"] .pv-card { position:relative; }
        .pv-grid[data-layout="overlay"] .pv-body { position:absolute; left:0; right:0; bottom:0; padding-top:2rem; background:linear-gradient(to top, rgba(0,0,0,.85), rgba(0,0,0,.2) 65%, transparent); }
        .pv-grid[data-layout="overlay"] .pv-title { color:#fff !important; text-shadow:0 1px 6px rgba(0,0,0,.65); }
        .pv-grid[data-layout="overlay"] .pv-price { text-shadow:0 1px 6px rgba(0,0,0,.7); }
        .pv-grid[data-layout="overlay"] .pv-label { color:#fff !important; background:rgba(255,255,255,.18) !important; border-color:rgba(255,255,255,.45) !important; }
    </style>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i><span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('widgets.index') }}" class="w-9 h-9 rounded-lg bg-white border border-[#215558]/10 flex items-center justify-center text-[#215558] hover:border-eazy transition"><i class="fa-solid fa-arrow-left text-sm"></i></a>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Voorraad-widget</h1>
                    <p class="text-sm text-[#215558] opacity-50">Layout en de code voor je website. Kleur en stijl regel je bij Huisstijl.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('widgets.voorraad.update') }}" id="voorraadForm">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    {{-- Controls --}}
                    <div class="lg:col-span-3 space-y-5">
                        {{-- Huisstijl-verwijzing --}}
                        <a href="{{ route('widgets.theme') }}" class="flex items-center gap-3 bg-eazy-50/50 border border-eazy/20 rounded-2xl px-5 py-4 hover:border-eazy transition">
                            <i class="fa-solid fa-palette text-eazy"></i>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-[#215558]">Kleur, lettertype &amp; stijl</p>
                                <p class="text-xs text-[#215558] opacity-50">Pas je aan bij Huisstijl, dat geldt voor al je widgets.</p>
                            </div>
                            <i class="fa-solid fa-arrow-right text-xs text-eazy"></i>
                        </a>

                        {{-- Layout --}}
                        <div class="ctl-card">
                            <span class="ctl-label">Layout</span>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Kolommen</label>
                                    <select name="columns" class="pv-input block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                        @foreach([1,2,3,4] as $c)<option value="{{ $c }}" @selected($val('columns',3)==$c)>{{ $c }}</option>@endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-[#374151] mb-1.5">Foto-positie</label>
                                    <select name="image_position" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                        <option value="top" @selected($val('image_position','top')==='top')>Boven</option>
                                        <option value="bottom" @selected($val('image_position','top')==='bottom')>Onder</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="flex items-center justify-between text-xs font-semibold text-[#374151] mb-1.5">Fotohoogte <span class="text-eazy font-bold" id="ih_val">{{ $val('image_height',200) }}px</span></label>
                                    <input type="range" name="image_height" min="120" max="340" value="{{ $val('image_height',200) }}" class="pv-input w-full accent-eazy" oninput="document.getElementById('ih_val').textContent=this.value+'px'">
                                </div>
                            </div>
                        </div>

                        {{-- Zichtbaarheid --}}
                        <div class="ctl-card">
                            <span class="ctl-label">Zichtbaarheid</span>
                            <div class="flex flex-wrap gap-4">
                                @foreach(['show_price'=>'Prijs tonen','show_km'=>'Km-stand tonen','show_fuel'=>'Brandstof tonen'] as $k=>$lbl)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="{{ $k }}" value="1" @checked($val($k,true)) class="pv-input rounded border-[#215558]/20 text-eazy focus:ring-eazy">
                                        <span class="text-[13px] text-[#215558] opacity-80">{{ $lbl }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Preview + embed --}}
                    <div class="lg:col-span-2">
                        <div class="lg:sticky lg:top-6 space-y-5">
                            <div class="ctl-card">
                                <span class="ctl-label">Voorbeeld</span>
                                <div class="bg-[#f4faf9] rounded-xl p-3">
                                    <div class="pv-grid" id="pvGrid" data-layout="{{ $layout }}">
                                        @for($i=0;$i<3;$i++)
                                            <div class="pv-card">
                                                <div class="pv-img" style="height:{{ $val('image_height',200) }}px">Foto</div>
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
                                <i class="fa-solid fa-floppy-disk text-xs"></i> Opslaan
                            </button>

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
        function vfld(name) { return document.getElementById('voorraadForm').elements[name]; }
        function updatePreview() {
            const grid = document.getElementById('pvGrid');
            const cols = grid.dataset.layout === 'list' ? 1 : Math.min(2, parseInt(vfld('columns').value, 10) || 2);
            grid.style.gridTemplateColumns = 'repeat(' + cols + ', 1fr)';
            document.querySelectorAll('.pv-img').forEach(e => e.style.height = vfld('image_height').value + 'px');
            document.querySelectorAll('.pv-show-price').forEach(e => e.style.display = vfld('show_price').checked ? '' : 'none');
            document.querySelectorAll('.pv-show-km').forEach(e => e.style.display = vfld('show_km').checked ? '' : 'none');
            document.querySelectorAll('.pv-show-fuel').forEach(e => e.style.display = vfld('show_fuel').checked ? '' : 'none');
        }
        function copyCode(preId, btnId) {
            const code = document.getElementById(preId).textContent.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
            navigator.clipboard.writeText(code);
            const btn = document.getElementById(btnId);
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Gekopieerd!';
            setTimeout(() => btn.innerHTML = '<i class="fa-regular fa-copy"></i> Kopieer', 2000);
        }
        document.querySelectorAll('.pv-input').forEach(el => { el.addEventListener('input', updatePreview); el.addEventListener('change', updatePreview); });
        updatePreview();
    </script>
</x-app-layout>
