<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-wand-magic-sparkles text-eazy"></i>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Widget Designer</h2>
        </div>
    </x-slot>

    {{-- Preload Google Fonts for font picker cards --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto:wght@400;700&family=Open+Sans:wght@400;600&family=Lato:wght@400;700&family=Montserrat:wght@400;600&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        /* ── Option Cards ── */
        .opt-card {
            position: relative;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.75rem 0.5rem;
            cursor: pointer;
            transition: all 0.18s ease;
            text-align: center;
            background: #fff;
            user-select: none;
        }
        .opt-card:hover {
            border-color: #0F9B9F;
            background: #F0FAFA;
        }
        .opt-card.selected {
            border-color: #0F9B9F;
            background: #E6F7F7;
            box-shadow: 0 0 0 1px #0F9B9F;
        }
        .opt-card:active { transform: scale(0.97); }
        .opt-check {
            display: none;
            position: absolute;
            top: 5px;
            right: 6px;
            font-size: 9px;
            color: #fff;
            background: #0F9B9F;
            width: 16px;
            height: 16px;
            line-height: 16px;
            text-align: center;
            border-radius: 50%;
        }
        .opt-card.selected .opt-check { display: block; }

        /* ── Toggle Switches ── */
        .toggle-switch {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            user-select: none;
        }
        .toggle-switch input[type="checkbox"] { display: none; }
        .toggle-track {
            width: 44px;
            height: 24px;
            background: #d1d5db;
            border-radius: 999px;
            position: relative;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .toggle-track::after {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            background: white;
            border-radius: 50%;
            top: 3px;
            left: 3px;
            transition: transform 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle-switch input:checked + .toggle-track {
            background: #0F9B9F;
        }
        .toggle-switch input:checked + .toggle-track::after {
            transform: translateX(20px);
        }

        /* ── Section Headers ── */
        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f3f4f6;
        }
        .section-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        /* ── Range Sliders ── */
        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 6px;
            border-radius: 999px;
            background: #e5e7eb;
            outline: none;
            cursor: pointer;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #0F9B9F;
            border: 3px solid white;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
            cursor: pointer;
            transition: transform 0.15s;
        }
        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.15);
        }
        input[type="range"]::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #0F9B9F;
            border: 3px solid white;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
            cursor: pointer;
        }

        /* ── Color Picker ── */
        input[type="color"] {
            -webkit-appearance: none;
            appearance: none;
            border: none;
            padding: 2px;
            cursor: pointer;
            border-radius: 10px;
        }
        input[type="color"]::-webkit-color-swatch-wrapper { padding: 2px; }
        input[type="color"]::-webkit-color-swatch {
            border: none;
            border-radius: 8px;
        }

        /* ── Font Card Sample Text ── */
        .font-sample { font-size: 28px; line-height: 1.1; font-weight: 600; color: #1f2937; }
        .font-card-system .font-sample { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .font-card-inter .font-sample { font-family: 'Inter', sans-serif; }
        .font-card-roboto .font-sample { font-family: 'Roboto', sans-serif; }
        .font-card-opensans .font-sample { font-family: 'Open Sans', sans-serif; }
        .font-card-lato .font-sample { font-family: 'Lato', sans-serif; }
        .font-card-montserrat .font-sample { font-family: 'Montserrat', sans-serif; }
        .font-card-poppins .font-sample { font-family: 'Poppins', sans-serif; }

        /* ── Pill Buttons (currency) ── */
        .pill-btn {
            padding: 0.4rem 1rem;
            border-radius: 999px;
            border: 2px solid #e5e7eb;
            background: #fff;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            user-select: none;
        }
        .pill-btn:hover {
            border-color: #0F9B9F;
            color: #0F9B9F;
        }
        .pill-btn.selected {
            border-color: #0F9B9F;
            background: #0F9B9F;
            color: #fff;
        }

        /* ── Detail section disabled state ── */
        .detail-fields-disabled {
            opacity: 0.35;
            pointer-events: none;
            filter: grayscale(0.5);
        }

        /* ── Shadow preview mini cards ── */
        .shadow-mini {
            width: 100%;
            height: 28px;
            border-radius: 6px;
            background: white;
            margin-bottom: 6px;
        }
    </style>

    <div class="py-8 sm:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Success Toast --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl shadow-sm" id="successToast">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button onclick="document.getElementById('successToast').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            {{-- Top Row: Embed Code + API Key --}}
            <div class="grid grid-cols-1 gap-6 mb-8">

                {{-- Embed Code --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="section-icon bg-violet-50 text-violet-500">
                            <i class="fa-solid fa-code"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Embed Code</h3>
                            <p class="text-xs text-gray-400">Kopieer en plak op je website</p>
                        </div>
                    </div>
                    <div class="relative">
                        <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-xs overflow-x-auto leading-relaxed" id="embedCode">&lt;!-- EazyAutomotive Widget --&gt;
&lt;div id="eazy-automotive-widget"&gt;&lt;/div&gt;
&lt;script
  src="{{ url('/embed/v1/widget.js') }}"
  data-api-key="{{ $company->api_key }}"
  data-base-url="{{ url('/') }}"
  defer&gt;
&lt;/script&gt;</pre>
                        <button onclick="copyEmbedCode()" class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 text-gray-300 rounded-lg text-xs hover:bg-gray-600 transition" id="copyBtn">
                            <i class="fa-regular fa-copy"></i> Kopieer
                        </button>
                    </div>
                </div>

                {{-- API Key --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="section-icon bg-amber-50 text-amber-500">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">API Key</h3>
                            <p class="text-xs text-gray-400">Identificeert jouw bedrijf</p>
                        </div>
                    </div>
                    <code class="block bg-gray-50 px-4 py-3 rounded-lg text-xs font-mono text-gray-500 break-all mb-4 border border-gray-100">{{ $company->api_key }}</code>
                    <form method="POST" action="{{ route('settings.regenerate-key') }}" onsubmit="return confirm('Weet je zeker? Je bestaande embed code stopt met werken.')">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-lg text-xs font-semibold hover:bg-red-100 transition">
                            <i class="fa-solid fa-rotate"></i> Opnieuw genereren
                        </button>
                    </form>
                </div>

            </div>

            {{-- ═══════════════════════════════════════════════ --}}
            {{-- Widget Settings Form --}}
            {{-- ═══════════════════════════════════════════════ --}}
            @php $s = $company->embed_settings ?? []; @endphp

            <form method="POST" action="{{ route('embed.settings') }}" id="settingsForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                    {{-- ═══ LEFT COLUMN: Settings (3/5) ═══ --}}
                    <div class="lg:col-span-3 space-y-6">

                        {{-- ── LAYOUT ── --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="section-header">
                                <div class="section-icon bg-blue-50 text-blue-500">
                                    <i class="fa-solid fa-table-columns"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Layout</h4>
                                    <p class="text-xs text-gray-400">Kolommen, foto positie & hoogte</p>
                                </div>
                            </div>

                            {{-- Columns --}}
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Kolommen</label>
                            <input type="hidden" name="columns" id="columns" value="{{ $s['columns'] ?? 3 }}">
                            <div class="grid grid-cols-3 gap-3 mb-5" data-selector-group="columns">
                                @foreach([2, 3, 4] as $col)
                                <div class="opt-card {{ ($s['columns'] ?? 3) == $col ? 'selected' : '' }}" data-value="{{ $col }}">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="flex gap-1 justify-center mb-2">
                                        @for($i = 0; $i < $col; $i++)
                                        <div class="h-10 rounded bg-blue-100 border border-blue-200" style="width: {{ 60 / $col }}px;"></div>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600">{{ $col }} kolommen</span>
                                </div>
                                @endforeach
                            </div>

                            {{-- Image Position --}}
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Foto positie</label>
                            <input type="hidden" name="image_position" id="image_position" value="{{ $s['image_position'] ?? 'top' }}">
                            <div class="grid grid-cols-2 gap-3 mb-5" data-selector-group="image_position">
                                <div class="opt-card {{ ($s['image_position'] ?? 'top') === 'top' ? 'selected' : '' }}" data-value="top">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="w-16 mx-auto mb-2">
                                        <div class="h-6 rounded-t bg-blue-200 mb-0.5"></div>
                                        <div class="h-1 w-10 rounded bg-gray-200 mb-0.5"></div>
                                        <div class="h-1 w-8 rounded bg-gray-200"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600"><i class="fa-solid fa-arrow-up mr-1 text-gray-400"></i>Boven</span>
                                </div>
                                <div class="opt-card {{ ($s['image_position'] ?? 'top') === 'bottom' ? 'selected' : '' }}" data-value="bottom">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="w-16 mx-auto mb-2">
                                        <div class="h-1 w-10 rounded bg-gray-200 mb-0.5"></div>
                                        <div class="h-1 w-8 rounded bg-gray-200 mb-0.5"></div>
                                        <div class="h-6 rounded-b bg-blue-200"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-600"><i class="fa-solid fa-arrow-down mr-1 text-gray-400"></i>Onder</span>
                                </div>
                            </div>

                            {{-- Image Height --}}
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Foto hoogte</label>
                                    <span class="text-xs font-bold text-eazy bg-eazy-50 px-2.5 py-0.5 rounded-full tabular-nums" id="image_height_val">{{ $s['image_height'] ?? 200 }}px</span>
                                </div>
                                <input type="range" name="image_height" id="image_height" min="100" max="350" value="{{ $s['image_height'] ?? 200 }}">
                            </div>
                        </div>

                        {{-- ── CARD STYLING ── --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="section-header">
                                <div class="section-icon bg-pink-50 text-pink-500">
                                    <i class="fa-solid fa-palette"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Card Styling</h4>
                                    <p class="text-xs text-gray-400">Kleuren, randen, schaduw & hover</p>
                                </div>
                            </div>

                            {{-- Colors grid --}}
                            <div class="grid grid-cols-3 gap-4 mb-6">
                                @foreach([
                                    ['primary_color', 'Hoofdkleur', '#0F9B9F'],
                                    ['card_bg_color', 'Achtergrond', '#ffffff'],
                                    ['card_border_color', 'Randkleur', '#e5e7eb'],
                                ] as [$name, $label, $default])
                                <div>
                                    <label class="text-xs font-medium text-gray-500 mb-1.5 block">{{ $label }}</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="{{ $name }}" id="{{ $name }}" value="{{ $s[$name] ?? $default }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="{{ $name }}_text">{{ $s[$name] ?? $default }}</code>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Sliders --}}
                            <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-6">
                                @foreach([
                                    ['card_border_width', 'Randdikte', 0, 4, 1, 'px'],
                                    ['card_border_radius', 'Ronde hoeken', 0, 30, 12, 'px'],
                                    ['card_padding', 'Padding', 0, 40, 16, 'px'],
                                ] as [$name, $label, $min, $max, $default, $unit])
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-medium text-gray-500">{{ $label }}</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="{{ $name }}_val">{{ $s[$name] ?? $default }}{{ $unit }}</span>
                                    </div>
                                    <input type="range" name="{{ $name }}" id="{{ $name }}" min="{{ $min }}" max="{{ $max }}" value="{{ $s[$name] ?? $default }}">
                                </div>
                                @endforeach
                            </div>

                            {{-- Shadow --}}
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Schaduw</label>
                            <input type="hidden" name="card_shadow" id="card_shadow" value="{{ $s['card_shadow'] ?? 'none' }}">
                            <div class="grid grid-cols-4 gap-2 mb-5" data-selector-group="card_shadow">
                                @foreach([
                                    ['none', 'Geen', 'none'],
                                    ['sm', 'Klein', '0 1px 3px rgba(0,0,0,0.1)'],
                                    ['md', 'Medium', '0 4px 12px rgba(0,0,0,0.1)'],
                                    ['lg', 'Groot', '0 10px 30px rgba(0,0,0,0.15)'],
                                ] as [$val, $label, $shadow])
                                <div class="opt-card {{ ($s['card_shadow'] ?? 'none') === $val ? 'selected' : '' }}" data-value="{{ $val }}">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="shadow-mini" style="box-shadow: {{ $shadow }}; {{ $val === 'none' ? 'border: 1px dashed #d1d5db;' : '' }}"></div>
                                    <span class="text-[10px] font-semibold text-gray-500">{{ $label }}</span>
                                </div>
                                @endforeach
                            </div>

                            {{-- Hover Effect --}}
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Hover effect</label>
                            <input type="hidden" name="hover_effect" id="hover_effect" value="{{ $s['hover_effect'] ?? 'lift' }}">
                            <div class="grid grid-cols-5 gap-2" data-selector-group="hover_effect">
                                @foreach([
                                    ['lift', 'Omhoog', 'fa-arrow-up'],
                                    ['shadow', 'Schaduw', 'fa-clone'],
                                    ['scale', 'Vergroot', 'fa-expand'],
                                    ['glow', 'Gloed', 'fa-sun'],
                                    ['none', 'Geen', 'fa-ban'],
                                ] as [$val, $label, $icon])
                                <div class="opt-card {{ ($s['hover_effect'] ?? 'lift') === $val ? 'selected' : '' }}" data-value="{{ $val }}">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <i class="fa-solid {{ $icon }} text-gray-400 text-base mb-1"></i>
                                    <span class="text-[10px] font-semibold text-gray-500 block">{{ $label }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── TYPOGRAPHY ── --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="section-header">
                                <div class="section-icon bg-indigo-50 text-indigo-500">
                                    <i class="fa-solid fa-font"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Typografie</h4>
                                    <p class="text-xs text-gray-400">Lettertype, groottes & kleuren</p>
                                </div>
                            </div>

                            {{-- Font Family Cards --}}
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Lettertype</label>
                            <input type="hidden" name="font_family" id="font_family" value="{{ $s['font_family'] ?? 'system' }}">
                            <div class="grid grid-cols-4 gap-2 mb-6" data-selector-group="font_family">
                                @foreach([
                                    ['system', 'Systeem', 'system'],
                                    ['Inter', 'Inter', 'inter'],
                                    ['Roboto', 'Roboto', 'roboto'],
                                    ['Open Sans', 'Open Sans', 'opensans'],
                                    ['Lato', 'Lato', 'lato'],
                                    ['Montserrat', 'Montserrat', 'montserrat'],
                                    ['Poppins', 'Poppins', 'poppins'],
                                ] as [$val, $label, $cls])
                                <div class="opt-card font-card-{{ $cls }} {{ ($s['font_family'] ?? 'system') === $val ? 'selected' : '' }}" data-value="{{ $val }}">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="font-sample mb-1">Aa</div>
                                    <span class="text-[10px] font-semibold text-gray-500">{{ $label }}</span>
                                </div>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-5">
                                {{-- Title Size --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-medium text-gray-500">Titel grootte</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="title_size_val">{{ $s['title_size'] ?? 16 }}px</span>
                                    </div>
                                    <input type="range" name="title_size" id="title_size" min="12" max="28" value="{{ $s['title_size'] ?? 16 }}">
                                </div>

                                {{-- Title Color --}}
                                <div>
                                    <label class="text-xs font-medium text-gray-500 mb-1.5 block">Titelkleur</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="title_color" id="title_color" value="{{ $s['title_color'] ?? '#111827' }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="title_color_text">{{ $s['title_color'] ?? '#111827' }}</code>
                                    </div>
                                </div>

                                {{-- Price Size --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-medium text-gray-500">Prijs grootte</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="price_size_val">{{ $s['price_size'] ?? 20 }}px</span>
                                    </div>
                                    <input type="range" name="price_size" id="price_size" min="12" max="36" value="{{ $s['price_size'] ?? 20 }}">
                                </div>
                            </div>

                            {{-- Currency --}}
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Valuta</label>
                            <input type="hidden" name="currency" id="currency" value="{{ $s['currency'] ?? 'EUR' }}">
                            <div class="flex gap-2 flex-wrap" data-selector-group="currency">
                                @foreach([
                                    ['EUR', '€ Euro'],
                                    ['USD', '$ Dollar'],
                                    ['GBP', '£ Pond'],
                                    ['none', 'Geen'],
                                ] as [$val, $label])
                                <div class="pill-btn {{ ($s['currency'] ?? 'EUR') === $val ? 'selected' : '' }}" data-value="{{ $val }}">{{ $label }}</div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── LABELS ── --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="section-header">
                                <div class="section-icon bg-orange-50 text-orange-500">
                                    <i class="fa-solid fa-tags"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Labels</h4>
                                    <p class="text-xs text-gray-400">Stijl, kleuren & spacing van spec-labels</p>
                                </div>
                            </div>

                            {{-- Label Style Cards --}}
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Stijl</label>
                            <input type="hidden" name="label_style" id="label_style" value="{{ $s['label_style'] ?? 'badge' }}">
                            <div class="grid grid-cols-4 gap-2 mb-5" data-selector-group="label_style">
                                {{-- Badge --}}
                                <div class="opt-card {{ ($s['label_style'] ?? 'badge') === 'badge' ? 'selected' : '' }}" data-value="badge">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="flex justify-center mb-2">
                                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">2021</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500">Badge</span>
                                </div>
                                {{-- Outline --}}
                                <div class="opt-card {{ ($s['label_style'] ?? 'badge') === 'outline' ? 'selected' : '' }}" data-value="outline">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="flex justify-center mb-2">
                                        <span class="text-[10px] border border-gray-400 text-gray-600 px-2 py-0.5 rounded">2021</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500">Outline</span>
                                </div>
                                {{-- Icon + Text --}}
                                <div class="opt-card {{ ($s['label_style'] ?? 'badge') === 'icon-text' ? 'selected' : '' }}" data-value="icon-text">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="flex justify-center items-center gap-1 mb-2">
                                        <i class="fa-regular fa-calendar text-gray-400 text-[10px]"></i>
                                        <span class="text-[10px] text-gray-600">2021</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500">Icoon</span>
                                </div>
                                {{-- Pill --}}
                                <div class="opt-card {{ ($s['label_style'] ?? 'badge') === 'pill' ? 'selected' : '' }}" data-value="pill">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="flex justify-center mb-2">
                                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full">2021</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500">Pill</span>
                                </div>
                            </div>

                            {{-- Label Colors --}}
                            <div class="grid grid-cols-2 gap-4 mb-5">
                                <div>
                                    <label class="text-xs font-medium text-gray-500 mb-1.5 block">Achtergrondkleur</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="label_bg_color" id="label_bg_color" value="{{ $s['label_bg_color'] ?? '#f3f4f6' }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="label_bg_color_text">{{ $s['label_bg_color'] ?? '#f3f4f6' }}</code>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500 mb-1.5 block">Tekstkleur</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="label_text_color" id="label_text_color" value="{{ $s['label_text_color'] ?? '#4b5563' }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="label_text_color_text">{{ $s['label_text_color'] ?? '#4b5563' }}</code>
                                    </div>
                                </div>
                            </div>

                            {{-- Label Spacing --}}
                            <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                                @foreach([
                                    ['label_radius', 'Ronde hoeken', 0, 20, 4, 'px'],
                                    ['label_padding_x', 'Ruimte horizontaal', 0, 24, 8, 'px'],
                                    ['label_padding_y', 'Ruimte verticaal', 0, 16, 3, 'px'],
                                    ['label_gap', 'Tussenruimte', 0, 20, 6, 'px'],
                                ] as [$name, $label, $min, $max, $default, $unit])
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-medium text-gray-500">{{ $label }}</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="{{ $name }}_val">{{ $s[$name] ?? $default }}{{ $unit }}</span>
                                    </div>
                                    <input type="range" name="{{ $name }}" id="{{ $name }}" min="{{ $min }}" max="{{ $max }}" value="{{ $s[$name] ?? $default }}">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── VISIBILITY ── --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="section-header">
                                <div class="section-icon bg-green-50 text-green-500">
                                    <i class="fa-solid fa-eye"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Zichtbaarheid</h4>
                                    <p class="text-xs text-gray-400">Welke informatie tonen op de card</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @foreach([
                                    ['show_price', 'Prijs tonen', 'fa-euro-sign', true],
                                    ['show_km', 'Kilometerstand tonen', 'fa-road', true],
                                    ['show_fuel', 'Brandstof tonen', 'fa-gas-pump', true],
                                ] as [$name, $label, $icon, $default])
                                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                                    <div class="flex items-center gap-3">
                                        <i class="fa-solid {{ $icon }} text-gray-300 w-4 text-center"></i>
                                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="hidden" name="{{ $name }}" value="0">
                                        <input type="checkbox" name="{{ $name }}" value="1" {{ ($s[$name] ?? $default) ? 'checked' : '' }} id="{{ $name }}">
                                        <span class="toggle-track"></span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── DETAIL PAGE ── --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="section-header">
                                <div class="section-icon bg-purple-50 text-purple-500">
                                    <i class="fa-solid fa-expand"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Detail pagina</h4>
                                    <p class="text-xs text-gray-400">Styling wanneer een auto wordt geopend</p>
                                </div>
                            </div>

                            {{-- Toggle --}}
                            <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl mb-5">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-sliders text-gray-400"></i>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-700 block">Eigen detail styling</span>
                                        <span class="text-xs text-gray-400">Standaard neemt hij card styling over</span>
                                    </div>
                                </div>
                                <label class="toggle-switch">
                                    <input type="hidden" name="detail_custom" value="0">
                                    <input type="checkbox" name="detail_custom" value="1" {{ ($s['detail_custom'] ?? false) ? 'checked' : '' }} id="detail_custom">
                                    <span class="toggle-track"></span>
                                </label>
                            </div>

                            <div id="detailFields" class="{{ ($s['detail_custom'] ?? false) ? '' : 'detail-fields-disabled' }}">
                                {{-- Detail Colors --}}
                                <div class="grid grid-cols-2 gap-4 mb-5">
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 mb-1.5 block">Achtergrondkleur</label>
                                        <div class="flex items-center gap-2">
                                            <input type="color" name="detail_bg_color" id="detail_bg_color" value="{{ $s['detail_bg_color'] ?? ($s['card_bg_color'] ?? '#ffffff') }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                            <code class="text-[10px] text-gray-400 font-mono" id="detail_bg_color_text">{{ $s['detail_bg_color'] ?? ($s['card_bg_color'] ?? '#ffffff') }}</code>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 mb-1.5 block">Titelkleur</label>
                                        <div class="flex items-center gap-2">
                                            <input type="color" name="detail_title_color" id="detail_title_color" value="{{ $s['detail_title_color'] ?? ($s['title_color'] ?? '#111827') }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                            <code class="text-[10px] text-gray-400 font-mono" id="detail_title_color_text">{{ $s['detail_title_color'] ?? ($s['title_color'] ?? '#111827') }}</code>
                                        </div>
                                    </div>
                                </div>

                                {{-- Detail Sliders --}}
                                <div class="grid grid-cols-2 gap-x-6 gap-y-4 mb-5">
                                    @foreach([
                                        ['detail_border_radius', 'Ronde hoeken', 0, 30, $s['card_border_radius'] ?? 12, 'px'],
                                        ['detail_padding', 'Padding', 0, 60, $s['card_padding'] ?? 24, 'px'],
                                        ['detail_title_size', 'Titel grootte', 14, 36, 24, 'px'],
                                        ['detail_price_size', 'Prijs grootte', 14, 42, 24, 'px'],
                                        ['detail_gallery_height', 'Foto hoogte', 150, 500, 350, 'px'],
                                        ['detail_overlay_opacity', 'Overlay donkerheid', 20, 90, 60, '%'],
                                    ] as [$name, $label, $min, $max, $default, $unit])
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="text-xs font-medium text-gray-500">{{ $label }}</label>
                                            <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="{{ $name }}_val">{{ $s[$name] ?? $default }}{{ $unit }}</span>
                                        </div>
                                        <input type="range" name="{{ $name }}" id="{{ $name }}" min="{{ $min }}" max="{{ $max }}" value="{{ $s[$name] ?? $default }}">
                                    </div>
                                    @endforeach
                                </div>

                                {{-- Detail Spec Columns --}}
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Spec kolommen</label>
                                <input type="hidden" name="detail_spec_columns" id="detail_spec_columns" value="{{ $s['detail_spec_columns'] ?? 2 }}">
                                <div class="grid grid-cols-3 gap-2" data-selector-group="detail_spec_columns">
                                    @foreach([1, 2, 3] as $col)
                                    <div class="opt-card {{ ($s['detail_spec_columns'] ?? 2) == $col ? 'selected' : '' }}" data-value="{{ $col }}">
                                        <i class="fa-solid fa-check opt-check"></i>
                                        <div class="flex gap-0.5 justify-center mb-1">
                                            @for($i = 0; $i < $col; $i++)
                                            <div class="h-4 rounded bg-purple-100 border border-purple-200" style="width: {{ 40 / $col }}px;"></div>
                                            @endfor
                                        </div>
                                        <span class="text-[10px] font-semibold text-gray-500">{{ $col }} {{ $col === 1 ? 'kolom' : 'kolommen' }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-eazy border border-transparent rounded-xl font-bold text-sm text-white hover:bg-eazy-dark shadow-lg shadow-eazy/25 hover:shadow-eazy/40 transition-all">
                                <i class="fa-solid fa-floppy-disk"></i> Instellingen opslaan
                            </button>
                        </div>

                    </div>

                    {{-- ═══ RIGHT COLUMN: Live Preview (2/5) ═══ --}}
                    <div class="lg:col-span-2">
                        <div class="lg:sticky lg:top-6">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fa-solid fa-eye text-xs text-gray-400"></i>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Live preview</label>
                            </div>
                            <div class="border border-gray-200 rounded-xl p-6 bg-gray-50/80" id="previewContainer">
                                <div id="previewCard" style="max-width:320px;margin:0 auto;overflow:hidden;transition:all 0.2s ease;">
                                    <div id="previewImgTop">
                                        <div id="previewImgPlaceholder" style="width:100%;background:linear-gradient(135deg,#dbeafe 0%,#bfdbfe 50%,#93c5fd 100%);display:flex;align-items:center;justify-content:center;">
                                            <i class="fa-solid fa-image text-blue-300 text-3xl"></i>
                                        </div>
                                    </div>
                                    <div id="previewBody">
                                        <div id="previewTitle" style="font-weight:600;line-height:1.3;margin-bottom:0.25rem;">Volkswagen Golf</div>
                                        <div id="previewPrice" style="font-weight:700;margin-bottom:0.75rem;">€ 24.950</div>
                                        <div id="previewSpecs" style="display:flex;flex-wrap:wrap;">
                                            <span class="preview-label" data-type="year" style="font-size:0.75rem;display:inline-flex;align-items:center;gap:4px;">
                                                <svg class="preview-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;flex-shrink:0;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>2021
                                            </span>
                                            <span class="preview-label" id="previewFuel" data-type="fuel" style="font-size:0.75rem;display:inline-flex;align-items:center;gap:4px;">
                                                <svg class="preview-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;flex-shrink:0;"><path d="M3 22V5a2 2 0 012-2h8a2 2 0 012 2v17"/><path d="M15 10h2a2 2 0 012 2v3a2 2 0 002 2h0a2 2 0 002-2V9.83a2 2 0 00-.59-1.42L18 6"/></svg>Benzine
                                            </span>
                                            <span class="preview-label" id="previewKm" data-type="km" style="font-size:0.75rem;display:inline-flex;align-items:center;gap:4px;">
                                                <svg class="preview-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;flex-shrink:0;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>45.230 km
                                            </span>
                                            <span class="preview-label" data-type="color" style="font-size:0.75rem;display:inline-flex;align-items:center;gap:4px;">
                                                <svg class="preview-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;flex-shrink:0;"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="10.5" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="12" r="2.5"/><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12a10 10 0 005.012 8.662"/></svg>Zwart
                                            </span>
                                        </div>
                                    </div>
                                    <div id="previewImgBottom" style="display:none;">
                                        <div id="previewImgPlaceholderBottom" style="width:100%;background:linear-gradient(135deg,#dbeafe 0%,#bfdbfe 50%,#93c5fd 100%);display:flex;align-items:center;justify-content:center;">
                                            <i class="fa-solid fa-image text-blue-300 text-3xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 text-center flex items-center justify-center gap-1">
                                <i class="fa-solid fa-hand-pointer"></i> Hover over de card voor het hover-effect
                            </p>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <script>
        /* ═══════════════════════════════════════════════════
           UTILITIES
           ═══════════════════════════════════════════════════ */
        function copyEmbedCode() {
            const code = document.getElementById('embedCode').textContent;
            navigator.clipboard.writeText(code.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&'));
            const btn = document.getElementById('copyBtn');
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Gekopieerd!';
            setTimeout(() => btn.innerHTML = '<i class="fa-regular fa-copy"></i> Kopieer', 2000);
        }

        const $ = id => document.getElementById(id);
        const SHADOWS = { none: 'none', sm: '0 1px 3px rgba(0,0,0,0.08)', md: '0 4px 12px rgba(0,0,0,0.1)', lg: '0 10px 30px rgba(0,0,0,0.15)' };
        const CURRENCIES = { EUR: '€', USD: '$', GBP: '£', none: '' };
        const SYSTEM_FONT = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";

        /* ═══════════════════════════════════════════════════
           CARD SELECTORS (visual option pickers)
           ═══════════════════════════════════════════════════ */
        document.querySelectorAll('[data-selector-group]').forEach(container => {
            const inputId = container.dataset.selectorGroup;
            const input = document.getElementById(inputId);
            const cards = container.querySelectorAll('[data-value]');

            cards.forEach(card => {
                card.addEventListener('click', () => {
                    cards.forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    input.value = card.dataset.value;
                    updatePreview();
                });
            });
        });

        /* ═══════════════════════════════════════════════════
           DETAIL TOGGLE
           ═══════════════════════════════════════════════════ */
        $('detail_custom').addEventListener('change', function() {
            const df = $('detailFields');
            df.classList.toggle('detail-fields-disabled', !this.checked);
        });

        /* ═══════════════════════════════════════════════════
           LIVE PREVIEW
           ═══════════════════════════════════════════════════ */
        function updatePreview() {
            const card = $('previewCard');
            const body = $('previewBody');
            const title = $('previewTitle');
            const price = $('previewPrice');
            const imgTop = $('previewImgTop');
            const imgBottom = $('previewImgBottom');
            const labels = document.querySelectorAll('.preview-label');

            /* Card */
            const primaryColor = $('primary_color').value;
            const bgColor = $('card_bg_color').value;
            const borderColor = $('card_border_color').value;
            const borderWidth = $('card_border_width').value;
            const radius = $('card_border_radius').value;
            const padding = $('card_padding').value;
            const shadow = $('card_shadow').value;
            const imgHeight = $('image_height').value;

            card.style.background = bgColor;
            card.style.border = `${borderWidth}px solid ${borderColor}`;
            card.style.borderRadius = radius + 'px';
            card.style.boxShadow = SHADOWS[shadow] || 'none';
            body.style.padding = padding + 'px';

            /* Image position */
            const imgPos = $('image_position').value;
            imgTop.style.display = imgPos === 'top' ? 'block' : 'none';
            imgBottom.style.display = imgPos === 'bottom' ? 'block' : 'none';
            $('previewImgPlaceholder').style.height = imgHeight + 'px';
            $('previewImgPlaceholderBottom').style.height = imgHeight + 'px';

            /* Font */
            const fontFamily = $('font_family').value;
            card.style.fontFamily = fontFamily === 'system' ? SYSTEM_FONT : `'${fontFamily}', ${SYSTEM_FONT}`;

            /* Title */
            const titleSize = $('title_size').value;
            const titleColor = $('title_color').value;
            title.style.fontSize = titleSize + 'px';
            title.style.color = titleColor;

            /* Price */
            const priceSize = $('price_size').value;
            const currency = $('currency').value;
            const symbol = CURRENCIES[currency] || '€';
            price.style.fontSize = priceSize + 'px';
            price.style.color = primaryColor;
            price.textContent = symbol ? `${symbol} 24.950` : '24.950';
            price.style.display = $('show_price').checked ? 'block' : 'none';

            /* Labels */
            const labelStyle = $('label_style').value;
            const labelBg = $('label_bg_color').value;
            const labelColor = $('label_text_color').value;
            const labelRadius = $('label_radius').value;
            const labelPadX = $('label_padding_x').value;
            const labelPadY = $('label_padding_y').value;
            const labelGap = $('label_gap').value;
            const icons = document.querySelectorAll('.preview-icon');

            labels.forEach(l => {
                l.style.color = labelColor;
                l.style.padding = `${labelPadY}px ${labelPadX}px`;
                l.style.border = 'none';

                if (labelStyle === 'badge') {
                    l.style.background = labelBg;
                    l.style.borderRadius = labelRadius + 'px';
                } else if (labelStyle === 'outline') {
                    l.style.background = 'transparent';
                    l.style.border = `1px solid ${labelColor}`;
                    l.style.borderRadius = labelRadius + 'px';
                } else if (labelStyle === 'icon-text') {
                    l.style.background = 'transparent';
                    l.style.padding = `${labelPadY}px 0`;
                } else if (labelStyle === 'pill') {
                    l.style.background = labelBg;
                    l.style.borderRadius = '9999px';
                }
            });
            icons.forEach(icon => {
                icon.style.display = labelStyle === 'icon-text' ? 'inline-block' : 'none';
            });
            $('previewSpecs').style.gap = labelGap + 'px';

            /* Visibility */
            $('previewKm').style.display = $('show_km').checked ? 'inline-flex' : 'none';
            $('previewFuel').style.display = $('show_fuel').checked ? 'inline-flex' : 'none';

            /* Hover effect */
            const hoverEffect = $('hover_effect').value;
            card.onmouseenter = () => {
                if (hoverEffect === 'lift') { card.style.transform = 'translateY(-4px)'; card.style.boxShadow = '0 12px 30px rgba(0,0,0,0.12)'; }
                else if (hoverEffect === 'shadow') { card.style.boxShadow = '0 12px 35px rgba(0,0,0,0.18)'; }
                else if (hoverEffect === 'scale') { card.style.transform = 'scale(1.03)'; }
                else if (hoverEffect === 'glow') { card.style.boxShadow = `0 0 20px ${primaryColor}44`; }
            };
            card.onmouseleave = () => {
                card.style.transform = 'none';
                card.style.boxShadow = SHADOWS[shadow] || 'none';
            };

            /* ── Value Displays ── */
            // Colors
            $('primary_color_text').textContent = primaryColor;
            $('card_bg_color_text').textContent = bgColor;
            $('card_border_color_text').textContent = borderColor;
            $('title_color_text').textContent = titleColor;
            $('label_bg_color_text').textContent = labelBg;
            $('label_text_color_text').textContent = labelColor;

            // Range values
            $('card_border_width_val').textContent = borderWidth + 'px';
            $('card_border_radius_val').textContent = radius + 'px';
            $('card_padding_val').textContent = padding + 'px';
            $('image_height_val').textContent = imgHeight + 'px';
            $('title_size_val').textContent = titleSize + 'px';
            $('price_size_val').textContent = priceSize + 'px';
            $('label_radius_val').textContent = labelRadius + 'px';
            $('label_padding_x_val').textContent = labelPadX + 'px';
            $('label_padding_y_val').textContent = labelPadY + 'px';
            $('label_gap_val').textContent = labelGap + 'px';

            // Detail fields
            if ($('detail_bg_color_text')) $('detail_bg_color_text').textContent = $('detail_bg_color').value;
            if ($('detail_title_color_text')) $('detail_title_color_text').textContent = $('detail_title_color').value;
            if ($('detail_border_radius_val')) $('detail_border_radius_val').textContent = $('detail_border_radius').value + 'px';
            if ($('detail_padding_val')) $('detail_padding_val').textContent = $('detail_padding').value + 'px';
            if ($('detail_title_size_val')) $('detail_title_size_val').textContent = $('detail_title_size').value + 'px';
            if ($('detail_price_size_val')) $('detail_price_size_val').textContent = $('detail_price_size').value + 'px';
            if ($('detail_gallery_height_val')) $('detail_gallery_height_val').textContent = $('detail_gallery_height').value + 'px';
            if ($('detail_overlay_opacity_val')) $('detail_overlay_opacity_val').textContent = $('detail_overlay_opacity').value + '%';
        }

        /* ═══════════════════════════════════════════════════
           EVENT BINDINGS
           ═══════════════════════════════════════════════════ */
        document.querySelectorAll('#settingsForm input[type="range"], #settingsForm input[type="color"], #settingsForm input[type="checkbox"]').forEach(el => {
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        });

        /* Initial render */
        updatePreview();
    </script>
</x-app-layout>
