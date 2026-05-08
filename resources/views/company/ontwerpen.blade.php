<x-app-layout>
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
            border-bottom: 1px solid rgba(33,85,88,0.08);
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

        /* ── Shadow preview mini cards ── */
        .shadow-mini {
            width: 100%;
            height: 28px;
            border-radius: 6px;
            background: white;
            margin-bottom: 6px;
        }
    </style>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Success Toast --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl" id="successToast">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button onclick="document.getElementById('successToast').remove()" class="cursor-pointer ml-auto text-emerald-400 hover:text-emerald-600 transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Ontwerpen</h1>
                    <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Pas het uiterlijk van je autowidget aan</p>
                </div>
                <a href="{{ route('integratie') }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-[#215558] text-white rounded-full text-sm font-bold hover:bg-eazy-darker transition">
                    <i class="fa-solid fa-code text-xs"></i> Plaatsen op je website
                </a>
            </div>

            {{-- Widget Settings Form --}}
            @php $s = $company->embed_settings ?? []; @endphp

            {{-- Tab Switcher --}}
            <div class="flex gap-1 bg-white rounded-full border border-[#215558]/10 p-1 mb-6 w-fit" x-data x-ref="tabBar">
                <button type="button" @click="$dispatch('switch-tab', { tab: 'card' })"
                    class="cursor-pointer tab-btn inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold transition-all"
                    :class="$store.designTab.active === 'card' ? 'bg-eazy text-white shadow-md shadow-eazy/20' : 'text-[#215558]/60 hover:text-[#215558] hover:bg-[#ebf2f2]/50'">
                    <i class="fa-solid fa-id-card text-xs"></i> Kaart
                </button>
                <button type="button" @click="$dispatch('switch-tab', { tab: 'detail' })"
                    class="cursor-pointer tab-btn inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold transition-all"
                    :class="$store.designTab.active === 'detail' ? 'bg-eazy text-white shadow-md shadow-eazy/20' : 'text-[#215558]/60 hover:text-[#215558] hover:bg-[#ebf2f2]/50'">
                    <i class="fa-solid fa-expand text-xs"></i> Detailpagina
                </button>
            </div>

            <form method="POST" action="{{ route('ontwerpen.update') }}" id="settingsForm">
                @csrf
                @method('PUT')

                {{-- ═══════════════════════════════════════ --}}
                {{-- CARD TAB --}}
                {{-- ═══════════════════════════════════════ --}}
                <div x-show="$store.designTab.active === 'card'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                    {{-- LEFT COLUMN: Settings (3/5) --}}
                    <div class="lg:col-span-3 space-y-6">

                        {{-- ── LAYOUT ── --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-blue-50 text-blue-500">
                                    <i class="fa-solid fa-table-columns"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Layout</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Hoe worden je auto's weergegeven?</p>
                                </div>
                            </div>

                            {{-- Columns --}}
                            <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 block">Aantal kolommen</label>
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
                            <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 block">Foto positie</label>
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
                                    <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider">Foto hoogte</label>
                                    <span class="text-xs font-bold text-eazy bg-eazy-50 px-2.5 py-0.5 rounded-full tabular-nums" id="image_height_val">{{ $s['image_height'] ?? 200 }}px</span>
                                </div>
                                <input type="range" name="image_height" id="image_height" min="100" max="350" value="{{ $s['image_height'] ?? 200 }}">
                            </div>
                        </div>

                        {{-- ── CARD STYLING ── --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-pink-50 text-pink-500">
                                    <i class="fa-solid fa-palette"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Uiterlijk</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Kleuren, randen en effecten</p>
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
                                    <label class="text-xs font-medium text-[#215558] opacity-70 mb-1.5 block">{{ $label }}</label>
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
                                        <label class="text-xs font-medium text-[#215558] opacity-70">{{ $label }}</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="{{ $name }}_val">{{ $s[$name] ?? $default }}{{ $unit }}</span>
                                    </div>
                                    <input type="range" name="{{ $name }}" id="{{ $name }}" min="{{ $min }}" max="{{ $max }}" value="{{ $s[$name] ?? $default }}">
                                </div>
                                @endforeach
                            </div>

                            {{-- Shadow --}}
                            <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 block">Schaduw</label>
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
                            <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 block">Hover effect</label>
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
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-indigo-50 text-indigo-500">
                                    <i class="fa-solid fa-font"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Tekst</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Lettertype, groottes en kleuren</p>
                                </div>
                            </div>

                            {{-- Font Family Cards --}}
                            <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 block">Lettertype</label>
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
                                        <label class="text-xs font-medium text-[#215558] opacity-70">Titel grootte</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="title_size_val">{{ $s['title_size'] ?? 16 }}px</span>
                                    </div>
                                    <input type="range" name="title_size" id="title_size" min="12" max="28" value="{{ $s['title_size'] ?? 16 }}">
                                </div>

                                {{-- Title Color --}}
                                <div>
                                    <label class="text-xs font-medium text-[#215558] opacity-70 mb-1.5 block">Titelkleur</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="title_color" id="title_color" value="{{ $s['title_color'] ?? '#111827' }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="title_color_text">{{ $s['title_color'] ?? '#111827' }}</code>
                                    </div>
                                </div>

                                {{-- Price Size --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-medium text-[#215558] opacity-70">Prijs grootte</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="price_size_val">{{ $s['price_size'] ?? 20 }}px</span>
                                    </div>
                                    <input type="range" name="price_size" id="price_size" min="12" max="36" value="{{ $s['price_size'] ?? 20 }}">
                                </div>
                            </div>

                            {{-- Currency --}}
                            <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 block">Valuta</label>
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
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-orange-50 text-orange-500">
                                    <i class="fa-solid fa-tags"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Kenmerken</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Hoe worden bouwjaar, km, brandstof etc. getoond?</p>
                                </div>
                            </div>

                            {{-- Label Style Cards --}}
                            <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 block">Stijl</label>
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
                                    <label class="text-xs font-medium text-[#215558] opacity-70 mb-1.5 block">Achtergrondkleur</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="label_bg_color" id="label_bg_color" value="{{ $s['label_bg_color'] ?? '#f3f4f6' }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="label_bg_color_text">{{ $s['label_bg_color'] ?? '#f3f4f6' }}</code>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-[#215558] opacity-70 mb-1.5 block">Tekstkleur</label>
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
                                        <label class="text-xs font-medium text-[#215558] opacity-70">{{ $label }}</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="{{ $name }}_val">{{ $s[$name] ?? $default }}{{ $unit }}</span>
                                    </div>
                                    <input type="range" name="{{ $name }}" id="{{ $name }}" min="{{ $min }}" max="{{ $max }}" value="{{ $s[$name] ?? $default }}">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── VISIBILITY ── --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-green-50 text-green-500">
                                    <i class="fa-solid fa-eye"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Tonen / verbergen</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Wat is zichtbaar op de autokaart?</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @foreach([
                                    ['show_price', 'Prijs tonen', 'fa-euro-sign', true],
                                    ['show_km', 'Kilometerstand tonen', 'fa-road', true],
                                    ['show_fuel', 'Brandstof tonen', 'fa-gas-pump', true],
                                ] as [$name, $label, $icon, $default])
                                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-[#215558]/5' : '' }}">
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

                        {{-- Submit (Card Tab) --}}
                        <div class="flex justify-end">
                            <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-8 py-3 bg-eazy border border-transparent rounded-full font-bold text-sm text-white hover:bg-eazy-dark shadow-lg shadow-eazy/25 hover:shadow-eazy/40 transition-all">
                                <i class="fa-solid fa-floppy-disk"></i> Ontwerp opslaan
                            </button>
                        </div>

                    </div>

                    {{-- RIGHT COLUMN: Card Live Preview (2/5) --}}
                    <div class="lg:col-span-2">
                        <div class="lg:sticky lg:top-6">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fa-solid fa-eye text-xs text-[#215558] opacity-50"></i>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Live voorbeeld — Kaart</label>
                            </div>
                            <div class="rounded-2xl bg-[#ebf2f2]/50" id="previewContainer">
                                <div id="previewCard" style="overflow:hidden;transition:all 0.2s ease;">
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
                                <i class="fa-solid fa-hand-pointer"></i> Hover over de kaart voor het hover-effect
                            </p>
                        </div>
                    </div>

                </div>
                </div>{{-- /Card Tab --}}

                {{-- ═══════════════════════════════════════ --}}
                {{-- DETAIL TAB --}}
                {{-- ═══════════════════════════════════════ --}}
                <div x-show="$store.designTab.active === 'detail'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" style="display:none;">
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                    {{-- LEFT COLUMN: Detail Settings (3/5) --}}
                    <div class="lg:col-span-3 space-y-6">

                        {{-- ── KLEUREN ── --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-purple-50 text-purple-500">
                                    <i class="fa-solid fa-palette"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Kleuren</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Achtergrond, randen en tekstkleuren</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4 mb-4">
                                @foreach([
                                    ['detail_bg_color', 'Achtergrond', '#ffffff'],
                                    ['detail_border_color', 'Randkleur', '#e5e7eb'],
                                    ['detail_title_color', 'Titelkleur', '#111827'],
                                ] as [$name, $label, $default])
                                <div>
                                    <label class="text-xs font-medium text-[#215558] opacity-70 mb-1.5 block">{{ $label }}</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="{{ $name }}" id="{{ $name }}" value="{{ $s[$name] ?? $default }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="{{ $name }}_text">{{ $s[$name] ?? $default }}</code>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                @foreach([
                                    ['detail_subtitle_color', 'Ondertitel', '#9ca3af'],
                                    ['detail_price_color', 'Prijskleur', $s['primary_color'] ?? '#0F9B9F'],
                                    ['detail_desc_color', 'Beschrijving', '#6b7280'],
                                ] as [$name, $label, $default])
                                <div>
                                    <label class="text-xs font-medium text-[#215558] opacity-70 mb-1.5 block">{{ $label }}</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="{{ $name }}" id="{{ $name }}" value="{{ $s[$name] ?? $default }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="{{ $name }}_text">{{ $s[$name] ?? $default }}</code>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── AFMETINGEN ── --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-indigo-50 text-indigo-500">
                                    <i class="fa-solid fa-ruler-combined"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Afmetingen</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Grootte van tekst, foto's en spacing</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                                @foreach([
                                    ['detail_border_radius', 'Ronde hoeken', 0, 30, 12, 'px'],
                                    ['detail_border_width', 'Randdikte', 0, 4, 1, 'px'],
                                    ['detail_padding', 'Padding', 0, 60, 24, 'px'],
                                    ['detail_title_size', 'Titel grootte', 14, 36, 24, 'px'],
                                    ['detail_price_size', 'Prijs grootte', 14, 42, 24, 'px'],
                                    ['detail_desc_size', 'Beschrijving grootte', 10, 20, 14, 'px'],
                                    ['detail_gallery_height', 'Foto hoogte', 150, 500, 350, 'px'],
                                    ['detail_overlay_opacity', 'Overlay donkerheid', 20, 90, 60, '%'],
                                ] as [$name, $label, $min, $max, $default, $unit])
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-medium text-[#215558] opacity-70">{{ $label }}</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="{{ $name }}_val">{{ $s[$name] ?? $default }}{{ $unit }}</span>
                                    </div>
                                    <input type="range" name="{{ $name }}" id="{{ $name }}" min="{{ $min }}" max="{{ $max }}" value="{{ $s[$name] ?? $default }}">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── SCHADUW ── --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-pink-50 text-pink-500">
                                    <i class="fa-solid fa-clone"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Schaduw</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Schaduweffect van de modal</p>
                                </div>
                            </div>

                            <input type="hidden" name="detail_shadow" id="detail_shadow" value="{{ $s['detail_shadow'] ?? 'lg' }}">
                            <div class="grid grid-cols-4 gap-2" data-selector-group="detail_shadow">
                                @foreach([
                                    ['none', 'Geen', 'none'],
                                    ['sm', 'Klein', '0 4px 12px rgba(0,0,0,0.1)'],
                                    ['md', 'Medium', '0 10px 25px rgba(0,0,0,0.18)'],
                                    ['lg', 'Groot', '0 25px 50px rgba(0,0,0,0.25)'],
                                ] as [$val, $label, $shadow])
                                <div class="opt-card {{ ($s['detail_shadow'] ?? 'lg') === $val ? 'selected' : '' }}" data-value="{{ $val }}">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="shadow-mini" style="box-shadow: {{ $shadow }}; {{ $val === 'none' ? 'border: 1px dashed #d1d5db;' : '' }}"></div>
                                    <span class="text-[10px] font-semibold text-gray-500">{{ $label }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── SPECIFICATIES ── --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-green-50 text-green-500">
                                    <i class="fa-solid fa-table-list"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Specificaties</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Stijl van de specificatierijen</p>
                                </div>
                            </div>

                            <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 block">Kolommen</label>
                            <input type="hidden" name="detail_spec_columns" id="detail_spec_columns" value="{{ $s['detail_spec_columns'] ?? 2 }}">
                            <div class="grid grid-cols-3 gap-2 mb-5" data-selector-group="detail_spec_columns">
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

                            <div class="grid grid-cols-3 gap-4 mb-5">
                                @foreach([
                                    ['detail_spec_bg_color', 'Achtergrond', '#f9fafb'],
                                    ['detail_spec_label_color', 'Labelkleur', '#6b7280'],
                                    ['detail_spec_value_color', 'Waardekleur', '#374151'],
                                ] as [$name, $label, $default])
                                <div>
                                    <label class="text-xs font-medium text-[#215558] opacity-70 mb-1.5 block">{{ $label }}</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="{{ $name }}" id="{{ $name }}" value="{{ $s[$name] ?? $default }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="{{ $name }}_text">{{ $s[$name] ?? $default }}</code>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                                @foreach([
                                    ['detail_spec_radius', 'Ronde hoeken', 0, 16, 6, 'px'],
                                    ['detail_spec_gap', 'Tussenruimte', 0, 16, 6, 'px'],
                                ] as [$name, $label, $min, $max, $default, $unit])
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="text-xs font-medium text-[#215558] opacity-70">{{ $label }}</label>
                                        <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="{{ $name }}_val">{{ $s[$name] ?? $default }}{{ $unit }}</span>
                                    </div>
                                    <input type="range" name="{{ $name }}" id="{{ $name }}" min="{{ $min }}" max="{{ $max }}" value="{{ $s[$name] ?? $default }}">
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ── OPTIES BADGES ── --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-orange-50 text-orange-500">
                                    <i class="fa-solid fa-tags"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Opties</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Hoe worden extra opties weergegeven?</p>
                                </div>
                            </div>

                            <label class="text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2 block">Stijl</label>
                            <input type="hidden" name="detail_badge_style" id="detail_badge_style" value="{{ $s['detail_badge_style'] ?? 'pill' }}">
                            <div class="grid grid-cols-3 gap-2 mb-5" data-selector-group="detail_badge_style">
                                <div class="opt-card {{ ($s['detail_badge_style'] ?? 'pill') === 'pill' ? 'selected' : '' }}" data-value="pill">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="flex justify-center mb-2">
                                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2.5 py-0.5 rounded-full">Airco</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500">Pill</span>
                                </div>
                                <div class="opt-card {{ ($s['detail_badge_style'] ?? 'pill') === 'badge' ? 'selected' : '' }}" data-value="badge">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="flex justify-center mb-2">
                                        <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">Airco</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500">Badge</span>
                                </div>
                                <div class="opt-card {{ ($s['detail_badge_style'] ?? 'pill') === 'outline' ? 'selected' : '' }}" data-value="outline">
                                    <i class="fa-solid fa-check opt-check"></i>
                                    <div class="flex justify-center mb-2">
                                        <span class="text-[10px] border border-gray-400 text-gray-600 px-2 py-0.5 rounded">Airco</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-gray-500">Outline</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-5">
                                @foreach([
                                    ['detail_badge_bg_color', 'Achtergrondkleur', '#f3f4f6'],
                                    ['detail_badge_text_color', 'Tekstkleur', $s['detail_price_color'] ?? ($s['primary_color'] ?? '#0F9B9F')],
                                ] as [$name, $label, $default])
                                <div>
                                    <label class="text-xs font-medium text-[#215558] opacity-70 mb-1.5 block">{{ $label }}</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" name="{{ $name }}" id="{{ $name }}" value="{{ $s[$name] ?? $default }}" class="w-10 h-10 rounded-xl border-2 border-gray-200 hover:border-eazy transition">
                                        <code class="text-[10px] text-gray-400 font-mono" id="{{ $name }}_text">{{ $s[$name] ?? $default }}</code>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-xs font-medium text-[#215558] opacity-70">Ronde hoeken</label>
                                    <span class="text-xs font-bold text-eazy bg-eazy-50 px-2 py-0.5 rounded-full tabular-nums" id="detail_badge_radius_val">{{ $s['detail_badge_radius'] ?? 4 }}px</span>
                                </div>
                                <input type="range" name="detail_badge_radius" id="detail_badge_radius" min="0" max="20" value="{{ $s['detail_badge_radius'] ?? 4 }}">
                            </div>
                        </div>

                        {{-- ── TONEN / VERBERGEN ── --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                            <div class="section-header">
                                <div class="section-icon bg-emerald-50 text-emerald-500">
                                    <i class="fa-solid fa-eye"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-[#215558]">Tonen / verbergen</h4>
                                    <p class="text-xs text-[#215558] opacity-50">Wat is zichtbaar op de detailpagina?</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @foreach([
                                    ['detail_show_subtitle', 'Ondertitel tonen', 'fa-text-height', true],
                                    ['detail_show_specs', 'Specificaties tonen', 'fa-list', true],
                                    ['detail_show_description', 'Beschrijving tonen', 'fa-align-left', true],
                                    ['detail_show_options', 'Opties tonen', 'fa-tags', true],
                                ] as [$name, $label, $icon, $default])
                                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-[#215558]/5' : '' }}">
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

                        {{-- Hidden: keep detail_custom always on --}}
                        <input type="hidden" name="detail_custom" value="1">

                        {{-- Submit (Detail Tab) --}}
                        <div class="flex justify-end">
                            <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-8 py-3 bg-eazy border border-transparent rounded-full font-bold text-sm text-white hover:bg-eazy-dark shadow-lg shadow-eazy/25 hover:shadow-eazy/40 transition-all">
                                <i class="fa-solid fa-floppy-disk"></i> Ontwerp opslaan
                            </button>
                        </div>
                    </div>

                    {{-- RIGHT COLUMN: Detail Live Preview (2/5) --}}
                    <div class="lg:col-span-2">
                        <div class="lg:sticky lg:top-6">
                            <div class="flex items-center gap-2 mb-3">
                                <i class="fa-solid fa-eye text-xs text-[#215558] opacity-50"></i>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Live voorbeeld — Detail</label>
                            </div>
                            <div class="rounded-2xl overflow-hidden" id="detailPreviewContainer">
                                {{-- Detail Preview Mock --}}
                                <div id="detailPreviewModal" style="transition:all 0.2s ease;overflow:hidden;">
                                    {{-- Gallery --}}
                                    <div id="detailPreviewOverlay" style="position:relative;">
                                        <div id="detailPreviewGallery" style="width:100%;background:linear-gradient(135deg,#dbeafe 0%,#bfdbfe 50%,#93c5fd 100%);display:flex;align-items:center;justify-content:center;position:relative;">
                                            <i class="fa-solid fa-images text-blue-300 text-3xl"></i>
                                            <div style="position:absolute;bottom:8px;left:50%;transform:translateX(-50%);display:flex;gap:4px;">
                                                <div style="width:28px;height:20px;background:rgba(255,255,255,0.9);border-radius:4px;border:2px solid white;"></div>
                                                <div style="width:28px;height:20px;background:rgba(255,255,255,0.5);border-radius:4px;"></div>
                                                <div style="width:28px;height:20px;background:rgba(255,255,255,0.5);border-radius:4px;"></div>
                                            </div>
                                            <div style="position:absolute;top:8px;right:8px;width:24px;height:24px;background:rgba(0,0,0,0.4);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa-solid fa-xmark text-white text-[10px]"></i>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Content --}}
                                    <div id="detailPreviewBody">
                                        <div id="detailPreviewTitle" style="font-weight:700;line-height:1.2;margin-bottom:0.25rem;">Volkswagen Golf</div>
                                        <div id="detailPreviewSubtitle" style="font-size:11px;color:#9ca3af;margin-bottom:0.75rem;">2021 &middot; Benzine &middot; 45.230 km</div>
                                        <div id="detailPreviewPrice" style="font-weight:800;margin-bottom:1rem;">€ 24.950</div>

                                        {{-- Specs --}}
                                        <div id="detailPreviewSpecTitle" class="detail-preview-section-title" style="font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Specificaties</div>
                                        <div id="detailPreviewSpecs" style="display:grid;gap:6px;margin-bottom:1rem;">
                                            @foreach([
                                                ['fa-calendar', 'Bouwjaar', '2021'],
                                                ['fa-road', 'Kilometerstand', '45.230 km'],
                                                ['fa-gas-pump', 'Brandstof', 'Benzine'],
                                                ['fa-palette', 'Kleur', 'Zwart'],
                                                ['fa-car', 'Carrosserie', 'Hatchback'],
                                                ['fa-shield-halved', 'APK tot', '12-2025'],
                                            ] as [$icon, $specLabel, $specValue])
                                            <div class="detail-preview-spec" style="display:flex;align-items:center;justify-content:space-between;padding:5px 8px;background:#f9fafb;border-radius:6px;">
                                                <span class="detail-preview-spec-label" style="display:flex;align-items:center;gap:6px;color:#6b7280;font-size:10px;">
                                                    <i class="fa-solid {{ $icon }} detail-preview-spec-icon" style="width:12px;text-align:center;font-size:9px;color:#9ca3af;"></i> {{ $specLabel }}
                                                </span>
                                                <span class="detail-preview-spec-value" style="font-size:10px;font-weight:600;color:#374151;">{{ $specValue }}</span>
                                            </div>
                                            @endforeach
                                        </div>

                                        {{-- Description --}}
                                        <div id="detailPreviewDescTitle" class="detail-preview-section-title" style="font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Beschrijving</div>
                                        <div id="detailPreviewDesc" style="font-size:11px;color:#6b7280;line-height:1.5;margin-bottom:1rem;">
                                            Nette Volkswagen Golf met lage kilometerstand. Voorzien van airco, navigatie en parkeersensoren. Onderhoudshistorie volledig aanwezig.
                                        </div>

                                        {{-- Options --}}
                                        <div id="detailPreviewOptTitle" class="detail-preview-section-title" style="font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Opties</div>
                                        <div id="detailPreviewOptWrap" style="display:flex;flex-wrap:wrap;gap:4px;">
                                            @foreach(['Airco', 'Navigatie', 'Parkeersensoren', 'Cruise control', 'LED'] as $opt)
                                            <span class="detail-preview-badge" style="font-size:9px;padding:3px 8px;border-radius:999px;font-weight:500;">{{ $opt }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-2 text-center flex items-center justify-center gap-1">
                                <i class="fa-solid fa-expand"></i> Zo ziet de detailpagina eruit wanneer een bezoeker op een auto klikt
                            </p>
                        </div>
                    </div>

                </div>
                </div>{{-- /Detail Tab --}}

            </form>

        </div>
    </div>

    <script>
        /* Alpine store for tab switching */
        document.addEventListener('alpine:init', () => {
            Alpine.store('designTab', { active: 'card' });
        });
        window.addEventListener('switch-tab', e => {
            Alpine.store('designTab').active = e.detail.tab;
            setTimeout(() => { updatePreview(); updateDetailPreview(); }, 50);
        });

        const $ = id => document.getElementById(id);
        const SHADOWS = { none: 'none', sm: '0 1px 3px rgba(0,0,0,0.08)', md: '0 4px 12px rgba(0,0,0,0.1)', lg: '0 10px 30px rgba(0,0,0,0.15)' };
        const CURRENCIES = { EUR: '€', USD: '$', GBP: '£', none: '' };
        const SYSTEM_FONT = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";

        /* Card selectors */
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
                    updateDetailPreview();
                });
            });
        });

        /* Live Preview — Card */
        function updatePreview() {
            const card = $('previewCard');
            const body = $('previewBody');
            const title = $('previewTitle');
            const price = $('previewPrice');
            const imgTop = $('previewImgTop');
            const imgBottom = $('previewImgBottom');
            const labels = document.querySelectorAll('.preview-label');

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

            const imgPos = $('image_position').value;
            imgTop.style.display = imgPos === 'top' ? 'block' : 'none';
            imgBottom.style.display = imgPos === 'bottom' ? 'block' : 'none';
            $('previewImgPlaceholder').style.height = imgHeight + 'px';
            $('previewImgPlaceholderBottom').style.height = imgHeight + 'px';

            const fontFamily = $('font_family').value;
            card.style.fontFamily = fontFamily === 'system' ? SYSTEM_FONT : `'${fontFamily}', ${SYSTEM_FONT}`;

            const titleSize = $('title_size').value;
            const titleColor = $('title_color').value;
            title.style.fontSize = titleSize + 'px';
            title.style.color = titleColor;

            const priceSize = $('price_size').value;
            const currency = $('currency').value;
            const symbol = CURRENCIES[currency] || '€';
            price.style.fontSize = priceSize + 'px';
            price.style.color = primaryColor;
            price.textContent = symbol ? `${symbol} 24.950` : '24.950';
            price.style.display = $('show_price').checked ? 'block' : 'none';

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

            $('previewKm').style.display = $('show_km').checked ? 'inline-flex' : 'none';
            $('previewFuel').style.display = $('show_fuel').checked ? 'inline-flex' : 'none';

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

            /* Value Displays */
            $('primary_color_text').textContent = primaryColor;
            $('card_bg_color_text').textContent = bgColor;
            $('card_border_color_text').textContent = borderColor;
            $('title_color_text').textContent = titleColor;
            $('label_bg_color_text').textContent = labelBg;
            $('label_text_color_text').textContent = labelColor;

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

        }

        /* Live Preview — Detail */
        function updateDetailPreview() {
            const modal = $('detailPreviewModal');
            const body = $('detailPreviewBody');
            const title = $('detailPreviewTitle');
            const price = $('detailPreviewPrice');
            const gallery = $('detailPreviewGallery');

            if (!modal) return;

            /* Colors */
            const bgColor = $('detail_bg_color')?.value || '#ffffff';
            const borderColor = $('detail_border_color')?.value || '#e5e7eb';
            const titleColor = $('detail_title_color')?.value || '#111827';
            const subtitleColor = $('detail_subtitle_color')?.value || '#9ca3af';
            const priceColor = $('detail_price_color')?.value || ($('primary_color')?.value || '#0F9B9F');
            const descColor = $('detail_desc_color')?.value || '#6b7280';

            /* Sizes */
            const radius = $('detail_border_radius')?.value || 12;
            const borderWidth = $('detail_border_width')?.value || 1;
            const padding = $('detail_padding')?.value || 24;
            const titleSize = $('detail_title_size')?.value || 24;
            const priceSize = $('detail_price_size')?.value || 24;
            const descSize = $('detail_desc_size')?.value || 14;
            const galleryHeight = $('detail_gallery_height')?.value || 350;
            const overlayOpacity = $('detail_overlay_opacity')?.value || 60;

            /* Shadow */
            const shadowKey = $('detail_shadow')?.value || 'lg';
            const MODAL_SHADOWS = { none: 'none', sm: '0 4px 12px rgba(0,0,0,0.1)', md: '0 10px 25px rgba(0,0,0,0.18)', lg: '0 25px 50px rgba(0,0,0,0.25)' };

            /* Spec styling */
            const specCols = $('detail_spec_columns')?.value || 2;
            const specBg = $('detail_spec_bg_color')?.value || '#f9fafb';
            const specLabelColor = $('detail_spec_label_color')?.value || '#6b7280';
            const specValueColor = $('detail_spec_value_color')?.value || '#374151';
            const specRadius = $('detail_spec_radius')?.value || 6;
            const specGap = $('detail_spec_gap')?.value || 6;

            /* Badge styling */
            const badgeStyle = $('detail_badge_style')?.value || 'pill';
            const badgeBg = $('detail_badge_bg_color')?.value || '#f3f4f6';
            const badgeText = $('detail_badge_text_color')?.value || priceColor;
            const badgeRadius = $('detail_badge_radius')?.value || 4;

            /* Font */
            const fontFamily = $('font_family')?.value || 'system';
            modal.style.fontFamily = fontFamily === 'system' ? SYSTEM_FONT : `'${fontFamily}', ${SYSTEM_FONT}`;

            /* Apply modal styles */
            modal.style.background = bgColor;
            modal.style.borderRadius = radius + 'px';
            modal.style.border = `${borderWidth}px solid ${borderColor}`;
            modal.style.boxShadow = MODAL_SHADOWS[shadowKey] || MODAL_SHADOWS.lg;
            body.style.padding = padding + 'px';

            /* Title */
            title.style.fontSize = titleSize + 'px';
            title.style.color = titleColor;

            /* Subtitle */
            const subtitle = $('detailPreviewSubtitle');
            if (subtitle) {
                subtitle.style.color = subtitleColor;
                subtitle.style.display = $('detail_show_subtitle')?.checked !== false ? 'block' : 'none';
            }

            /* Price */
            price.style.fontSize = priceSize + 'px';
            price.style.color = priceColor;

            /* Gallery */
            gallery.style.height = Math.min(galleryHeight, 250) + 'px';

            /* Specs grid */
            const specsGrid = $('detailPreviewSpecs');
            const showSpecs = $('detail_show_specs')?.checked !== false;
            specsGrid.style.gridTemplateColumns = `repeat(${specCols}, 1fr)`;
            specsGrid.style.gap = specGap + 'px';
            specsGrid.style.display = showSpecs ? 'grid' : 'none';

            const specSectionTitle = $('detailPreviewSpecTitle');
            if (specSectionTitle) {
                specSectionTitle.style.color = subtitleColor;
                specSectionTitle.style.display = showSpecs ? 'block' : 'none';
            }

            /* Individual spec rows */
            document.querySelectorAll('.detail-preview-spec').forEach(spec => {
                spec.style.background = specBg;
                spec.style.borderRadius = specRadius + 'px';
            });
            document.querySelectorAll('.detail-preview-spec-label').forEach(label => {
                label.style.color = specLabelColor;
            });
            document.querySelectorAll('.detail-preview-spec-icon').forEach(icon => {
                icon.style.color = specLabelColor;
            });
            document.querySelectorAll('.detail-preview-spec-value').forEach(val => {
                val.style.color = specValueColor;
            });

            /* Description */
            const showDesc = $('detail_show_description')?.checked !== false;
            const desc = $('detailPreviewDesc');
            const descTitle = $('detailPreviewDescTitle');
            if (desc) {
                desc.style.color = descColor;
                desc.style.fontSize = descSize + 'px';
                desc.style.display = showDesc ? 'block' : 'none';
            }
            if (descTitle) {
                descTitle.style.color = subtitleColor;
                descTitle.style.display = showDesc ? 'block' : 'none';
            }

            /* Options */
            const showOpts = $('detail_show_options')?.checked !== false;
            const optTitle = $('detailPreviewOptTitle');
            const optWrap = $('detailPreviewOptWrap');
            if (optTitle) {
                optTitle.style.color = subtitleColor;
                optTitle.style.display = showOpts ? 'block' : 'none';
            }
            if (optWrap) optWrap.style.display = showOpts ? 'flex' : 'none';

            document.querySelectorAll('.detail-preview-badge').forEach(badge => {
                badge.style.color = badgeText;
                if (badgeStyle === 'pill') {
                    badge.style.background = badgeBg;
                    badge.style.borderRadius = '9999px';
                    badge.style.border = 'none';
                } else if (badgeStyle === 'badge') {
                    badge.style.background = badgeBg;
                    badge.style.borderRadius = badgeRadius + 'px';
                    badge.style.border = 'none';
                } else if (badgeStyle === 'outline') {
                    badge.style.background = 'transparent';
                    badge.style.border = `1px solid ${badgeText}`;
                    badge.style.borderRadius = badgeRadius + 'px';
                }
            });

            /* Section title colors */
            document.querySelectorAll('.detail-preview-section-title').forEach(el => {
                el.style.color = subtitleColor;
            });

            /* Value displays — Colors */
            const colorTexts = {
                'detail_bg_color': bgColor, 'detail_border_color': borderColor,
                'detail_title_color': titleColor, 'detail_subtitle_color': subtitleColor,
                'detail_price_color': priceColor, 'detail_desc_color': descColor,
                'detail_spec_bg_color': specBg, 'detail_spec_label_color': specLabelColor,
                'detail_spec_value_color': specValueColor, 'detail_badge_bg_color': badgeBg,
                'detail_badge_text_color': badgeText,
            };
            for (const [key, val] of Object.entries(colorTexts)) {
                const el = $(key + '_text');
                if (el) el.textContent = val;
            }

            /* Value displays — Sliders */
            const sliderVals = {
                'detail_border_radius': [radius, 'px'], 'detail_border_width': [borderWidth, 'px'],
                'detail_padding': [padding, 'px'], 'detail_title_size': [titleSize, 'px'],
                'detail_price_size': [priceSize, 'px'], 'detail_desc_size': [descSize, 'px'],
                'detail_gallery_height': [galleryHeight, 'px'], 'detail_overlay_opacity': [overlayOpacity, '%'],
                'detail_spec_radius': [specRadius, 'px'], 'detail_spec_gap': [specGap, 'px'],
                'detail_badge_radius': [badgeRadius, 'px'],
            };
            for (const [key, [val, unit]] of Object.entries(sliderVals)) {
                const el = $(key + '_val');
                if (el) el.textContent = val + unit;
            }
        }

        /* Event bindings */
        document.querySelectorAll('#settingsForm input[type="range"], #settingsForm input[type="color"], #settingsForm input[type="checkbox"]').forEach(el => {
            el.addEventListener('input', () => { updatePreview(); updateDetailPreview(); });
            el.addEventListener('change', () => { updatePreview(); updateDetailPreview(); });
        });

        /* Initial render */
        updatePreview();
        updateDetailPreview();
    </script>
</x-app-layout>
