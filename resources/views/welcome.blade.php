<!DOCTYPE html>
<html lang="nl" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EazyAutomotive · Beheer je autovoorraad, bereik meer klanten</title>
    <meta name="description" content="Voeg auto's toe met RDW kentekenlookup, beheer je voorraad en toon je aanbod op je eigen website met één regel code. Speciaal voor autobedrijven.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter-tight:300,400,500,600,700,800,900&family=caveat:400,700" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --eazy: #0F9B9F; --eazy-dark: #0B7A7D; --eazy-darker: #215558; }
        .font-hand { font-family: 'Caveat', cursive; }

        /* ── Animated gradient text (constrained to AA-safe darker stops) ── */
        .text-gradient {
            background: linear-gradient(110deg, #0B7A7D 0%, #0F9B9F 35%, #215558 70%, #0B7A7D 100%);
            background-size: 220% 100%;
            -webkit-background-clip: text; background-clip: text;
            color: transparent;
            animation: panText 8s ease-in-out infinite;
        }
        @keyframes panText { 0%,100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }

        /* ── Floating aurora orbs ── */
        .orb { position: absolute; border-radius: 9999px; filter: blur(60px); opacity: .55; will-change: transform; }
        .orb-1 { animation: float1 14s ease-in-out infinite; }
        .orb-2 { animation: float2 18s ease-in-out infinite; }
        .orb-3 { animation: float3 16s ease-in-out infinite; }
        @keyframes float1 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(40px,-30px) scale(1.12); } }
        @keyframes float2 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(-50px,30px) scale(1.18); } }
        @keyframes float3 { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(30px,40px) scale(1.1); } }

        /* ── Grid backdrop ── */
        .grid-bg {
            background-image: linear-gradient(to right, rgba(33,85,88,.06) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(33,85,88,.06) 1px, transparent 1px);
            background-size: 44px 44px;
            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, #000 40%, transparent 100%);
                    mask-image: radial-gradient(ellipse 70% 60% at 50% 30%, #000 40%, transparent 100%);
        }

        /* ── Soft float for mockups ── */
        .float-slow { animation: floatSlow 7s ease-in-out infinite; }
        @keyframes floatSlow { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }

        /* ── Marquee ── */
        .marquee { display: flex; width: max-content; animation: marquee 32s linear infinite; }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .marquee-wrap:hover .marquee { animation-play-state: paused; }

        /* ── Pulse ring ── */
        .pulse-dot::before {
            content: ''; position: absolute; inset: 0; border-radius: 9999px;
            background: currentColor; opacity: .5; animation: pulseRing 2s ease-out infinite;
        }
        @keyframes pulseRing { 0% { transform: scale(1); opacity: .5; } 100% { transform: scale(2.6); opacity: 0; } }

        /* ── Button shine ── */
        .btn-shine { position: relative; overflow: hidden; }
        .btn-shine::after {
            content: ''; position: absolute; top: 0; left: -120%; width: 60%; height: 100%;
            background: linear-gradient(110deg, transparent, rgba(255,255,255,.35), transparent);
            transform: skewX(-20deg); transition: left .7s ease;
        }
        .btn-shine:hover::after { left: 130%; }

        /* ── Scroll reveal ── */
        [data-reveal] { opacity: 0; transform: translateY(28px); transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1); }
        [data-reveal].reveal-in { opacity: 1; transform: none; }
        [data-delay="1"] { transition-delay: .07s; }
        [data-delay="2"] { transition-delay: .14s; }
        [data-delay="3"] { transition-delay: .21s; }
        [data-delay="4"] { transition-delay: .28s; }
        [data-delay="5"] { transition-delay: .35s; }

        /* ── Dutch license plate ── */
        .plate-input { font-family: 'Inter Tight', sans-serif; letter-spacing: .08em; }
        .plate-input::placeholder { color: rgba(0,0,0,.6); }

        /* ── Spin ── */
        .spin { animation: spin 0.7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        @media (prefers-reduced-motion: reduce) {
            .orb-1,.orb-2,.orb-3,.float-slow,.marquee,.text-gradient,.pulse-dot::before { animation: none !important; }
            [data-reveal] { opacity: 1 !important; transform: none !important; transition: none !important; }
        }
    </style>
</head>
<body class="antialiased bg-[#f4faf9] text-eazy-darker font-sans selection:bg-eazy/20 overflow-x-hidden">

    <!-- ══════════════════ NAVBAR ══════════════════ -->
    <nav x-data="{ scrolled: false, open: false }"
         x-on:scroll.window="scrolled = window.scrollY > 24"
         class="fixed w-full top-0 z-50 transition-all duration-300"
         :class="scrolled ? 'bg-white/80 backdrop-blur-xl border-b border-eazy-darker/5 shadow-sm shadow-eazy-darker/5' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="flex items-center gap-2 group">
                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-eazy to-eazy-dark flex items-center justify-center shadow-lg shadow-eazy/30 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-car-side text-white text-sm" aria-hidden="true"></i>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="text-xl font-black text-eazy-darker tracking-tight">Eazy</span><span class="text-xl font-light text-eazy tracking-tight">Automotive</span>
                    </span>
                </a>

                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm font-semibold text-eazy-darker hover:text-eazy-dark transition">Features</a>
                    <a href="#hoe-het-werkt" class="text-sm font-semibold text-eazy-darker hover:text-eazy-dark transition">Hoe het werkt</a>
                    <a href="#showcase" class="text-sm font-semibold text-eazy-darker hover:text-eazy-dark transition">Demo</a>
                    <a href="#faq" class="text-sm font-semibold text-eazy-darker hover:text-eazy-dark transition">FAQ</a>
                </div>

                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-shine inline-flex items-center gap-2 px-5 py-2.5 bg-eazy-dark text-white text-sm font-bold rounded-full hover:bg-eazy-darker transition shadow-lg shadow-eazy/25">
                            <i class="fa-solid fa-gauge-high text-xs" aria-hidden="true"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-eazy-darker hover:text-eazy-dark transition">Inloggen</a>
                        <a href="{{ route('register') }}" class="btn-shine inline-flex items-center gap-2 px-5 py-2.5 bg-eazy-dark text-white text-sm font-bold rounded-full hover:bg-eazy-darker transition shadow-lg shadow-eazy/25">
                            Gratis starten <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
                        </a>
                    @endauth
                </div>

                <!-- Mobile toggle -->
                <button type="button" x-on:click="open = !open"
                        :aria-expanded="open.toString()" aria-controls="mobile-menu"
                        :aria-label="open ? 'Menu sluiten' : 'Menu openen'"
                        class="md:hidden w-11 h-11 rounded-xl bg-white/70 border border-eazy-darker/10 flex items-center justify-center text-eazy-darker">
                    <i class="fa-solid" :class="open ? 'fa-xmark' : 'fa-bars'" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" x-show="open" x-collapse x-cloak class="md:hidden bg-white/95 backdrop-blur-xl border-b border-eazy-darker/10 px-4 py-4 space-y-1">
            <a href="#features" x-on:click="open=false" class="block px-4 py-3 rounded-xl text-sm font-semibold text-eazy-darker hover:bg-eazy-50">Features</a>
            <a href="#hoe-het-werkt" x-on:click="open=false" class="block px-4 py-3 rounded-xl text-sm font-semibold text-eazy-darker hover:bg-eazy-50">Hoe het werkt</a>
            <a href="#showcase" x-on:click="open=false" class="block px-4 py-3 rounded-xl text-sm font-semibold text-eazy-darker hover:bg-eazy-50">Demo</a>
            <a href="#faq" x-on:click="open=false" class="block px-4 py-3 rounded-xl text-sm font-semibold text-eazy-darker hover:bg-eazy-50">FAQ</a>
            <div class="pt-2 flex flex-col gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-center px-5 py-3 bg-eazy-dark text-white text-sm font-bold rounded-full">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-center px-5 py-3 border border-eazy-darker/10 text-eazy-darker text-sm font-bold rounded-full">Inloggen</a>
                    <a href="{{ route('register') }}" class="text-center px-5 py-3 bg-eazy-dark text-white text-sm font-bold rounded-full">Gratis starten</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- ══════════════════ HERO ══════════════════ -->
    <section class="relative pt-32 pb-20 sm:pt-44 sm:pb-28 overflow-hidden">
        <!-- backdrop -->
        <div class="absolute inset-0 -z-10" aria-hidden="true">
            <div class="absolute inset-0 grid-bg"></div>
            <div class="orb orb-1 w-[32rem] h-[32rem] bg-eazy-300/50 -top-20 -left-24"></div>
            <div class="orb orb-2 w-[28rem] h-[28rem] bg-eazy-200/60 top-10 right-0"></div>
            <div class="orb orb-3 w-[24rem] h-[24rem] bg-eazy-100/70 bottom-0 left-1/3"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/70 backdrop-blur rounded-full border border-eazy-darker/10 text-xs font-bold text-eazy-dark uppercase tracking-wider mb-7 shadow-sm">
                    <span class="relative inline-flex w-1.5 h-1.5 rounded-full text-emerald-600 pulse-dot" aria-hidden="true"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span></span>
                    Gekoppeld aan de RDW-database
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-eazy-darker leading-[1.05] tracking-tight">
                    Je hele voorraad online,<br class="hidden sm:block">
                    <span class="text-gradient">in een paar klikken</span>
                </h1>

                <p class="mt-7 text-lg sm:text-xl text-muted leading-relaxed max-w-2xl mx-auto">
                    Voeg auto's toe met één kenteken, beheer je voorraad moeiteloos en toon je aanbod op je eigen website met <span class="font-semibold text-eazy-darker">één regel code</span>. Speciaal gebouwd voor autobedrijven.
                </p>

                <div class="mt-9 flex flex-col sm:flex-row gap-3.5 justify-center">
                    <a href="{{ route('register') }}" class="btn-shine inline-flex items-center justify-center gap-2 px-8 py-4 bg-eazy-dark text-white font-bold rounded-full hover:bg-eazy-darker transition shadow-xl shadow-eazy/30 text-base">
                        Gratis starten <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                    </a>
                    <a href="#showcase" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/80 backdrop-blur text-eazy-darker font-bold rounded-full border border-eazy-darker/10 hover:border-eazy/40 hover:text-eazy-dark transition text-base">
                        <i class="fa-solid fa-play text-xs" aria-hidden="true"></i> Bekijk live demo
                    </a>
                </div>

                <div class="mt-7 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs font-semibold text-muted">
                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600" aria-hidden="true"></i> Geen creditcard nodig</span>
                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600" aria-hidden="true"></i> In 2 minuten live</span>
                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600" aria-hidden="true"></i> Maandelijks opzegbaar</span>
                </div>
            </div>

            <!-- Interactive RDW kenteken demo -->
            <div class="mt-16 sm:mt-20 max-w-2xl mx-auto"
                 x-data="rdwDemo()" x-init="autoplay()">
                <div class="relative bg-white/80 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl shadow-eazy-darker/10 border border-white">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-eazy-darker text-white text-[10px] font-bold uppercase tracking-widest rounded-full shadow-lg">
                        Probeer het zelf
                    </div>

                    <p id="kenteken-hint" class="text-center text-sm font-semibold text-muted mb-4">Typ een kenteken en haal de voertuiggegevens op</p>

                    <!-- plate + button -->
                    <div class="flex flex-col sm:flex-row items-stretch gap-3">
                        <div class="flex-1 flex items-stretch rounded-2xl overflow-hidden shadow-lg ring-4 ring-eazy-darker/5 select-none">
                            <!-- EU strip -->
                            <div class="w-9 bg-[#0a3fb0] flex flex-col items-center justify-center text-white shrink-0" aria-hidden="true">
                                <span class="text-[10px]">★</span>
                                <span class="text-[11px] font-black leading-none mt-0.5">NL</span>
                            </div>
                            <input type="text" x-model="plate" x-on:input="plate = plate.toUpperCase()" x-on:keydown.enter="lookup()"
                                   maxlength="10" placeholder="XX-123-X" aria-label="Kenteken" aria-describedby="kenteken-hint"
                                   class="plate-input flex-1 w-full bg-[#f4ce00] text-black text-2xl sm:text-3xl font-black text-center uppercase border-0 focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-eazy-darker focus-visible:ring-4 focus-visible:ring-eazy/40 py-3">
                        </div>
                        <button type="button" x-on:click="lookup()" :disabled="loading"
                                class="btn-shine inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-eazy-dark text-white font-bold rounded-full hover:bg-eazy-darker transition shadow-lg shadow-eazy/25 disabled:opacity-80 disabled:cursor-wait disabled:[&::after]:hidden">
                            <i class="fa-solid" :class="loading ? 'fa-spinner spin' : 'fa-magnifying-glass'" aria-hidden="true"></i>
                            <span x-text="loading ? 'Bezig…' : 'Ophalen'"></span>
                        </button>
                    </div>

                    <!-- result -->
                    <div x-show="done" x-cloak x-transition.opacity.duration.500ms class="mt-6" role="status" aria-live="polite">
                        <div class="flex items-center gap-2 mb-4 text-emerald-600">
                            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                            <span class="text-sm font-bold">Gevonden bij de RDW</span>
                            <span class="ml-auto text-xs font-semibold text-muted">Automatisch ingevuld in <span class="text-eazy-dark">0,8s</span></span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <template x-for="field in result" :key="field.label">
                                <div class="bg-eazy-50/70 rounded-xl p-3 border border-eazy-darker/5">
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-muted uppercase tracking-wide">
                                        <i class="fa-solid" :class="field.icon" aria-hidden="true"></i><span x-text="field.label"></span>
                                    </div>
                                    <div class="text-sm font-bold text-eazy-darker mt-1" x-text="field.value"></div>
                                </div>
                            </template>
                        </div>
                        <div class="mt-4 flex items-center justify-between bg-gradient-to-r from-eazy-dark to-eazy-darker rounded-xl px-4 py-3 text-white">
                            <span class="text-sm font-semibold">Verkoopprijs</span>
                            <span class="text-xl font-black" x-text="price"></span>
                        </div>
                    </div>
                </div>
                <p class="text-center text-xs text-muted mt-3">Voorbeeldweergave. In je dashboard worden échte RDW-gegevens opgehaald.</p>
            </div>
        </div>
    </section>

    <!-- ══════════════════ TRUST / MARQUEE ══════════════════ -->
    <section class="py-10 border-y border-eazy-darker/5 bg-white/40">
        <p class="text-center text-xs font-bold uppercase tracking-widest text-muted mb-6">Werkt met elk automerk</p>
        <div class="marquee-wrap relative overflow-hidden"
             style="-webkit-mask-image:linear-gradient(to right,transparent,#000 12%,#000 88%,transparent);mask-image:linear-gradient(to right,transparent,#000 12%,#000 88%,transparent)">
            <div class="marquee gap-12 items-center">
                @php $brands = ['Volkswagen','BMW','Audi','Mercedes-Benz','Toyota','Volvo','Renault','Peugeot','Kia','Ford','Tesla','Opel']; @endphp
                @foreach([false, true] as $copyIndex => $isDuplicate)
                    <div class="flex gap-12 items-center" @if($isDuplicate) aria-hidden="true" @endif>
                        @foreach($brands as $brand)
                            <span class="text-lg font-medium tracking-wide text-eazy-darker/40 whitespace-nowrap">{{ $brand }}</span>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════════════ STATS BAND ══════════════════ -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="relative rounded-3xl bg-gradient-to-br from-eazy-darker via-eazy-dark to-eazy px-6 py-12 sm:px-12 overflow-hidden shadow-xl shadow-eazy-darker/20">
                <div class="absolute -right-10 -top-10 w-72 h-72 bg-white/5 rounded-full" aria-hidden="true"></div>
                <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-black/10 rounded-full" aria-hidden="true"></div>
                <div class="relative grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
                    @foreach([
                        ['text' => '1 regel', 'label' => 'Code om live te gaan', 'icon' => 'fa-code'],
                        ['value' => 98, 'suffix' => '%', 'label' => 'Tot minder typwerk', 'icon' => 'fa-wand-magic-sparkles'],
                        ['value' => 2, 'suffix' => ' min', 'label' => 'Tot je live bent', 'icon' => 'fa-rocket'],
                        ['text' => 'RDW', 'label' => 'Data altijd actueel', 'icon' => 'fa-shield-check'],
                    ] as $stat)
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid {{ $stat['icon'] }} text-eazy-100 text-lg" aria-hidden="true"></i>
                        </div>
                        <div class="text-3xl sm:text-4xl font-black text-white">
                            @isset($stat['value'])<span data-count="{{ $stat['value'] }}" data-suffix="{{ $stat['suffix'] }}">0</span>@else<span>{{ $stat['text'] }}</span>@endisset
                        </div>
                        <div class="text-sm font-medium text-eazy-100/80 mt-1">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════ FEATURES (BENTO) ══════════════════ -->
    <section id="features" class="py-20 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border border-eazy-darker/10 text-xs font-bold text-eazy-dark uppercase tracking-wider mb-4 shadow-sm">
                    <i class="fa-solid fa-bolt text-[10px]" aria-hidden="true"></i> Features
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-eazy-darker tracking-tight">Alles wat je nodig hebt</h2>
                <p class="mt-4 text-lg text-muted">Van kentekenlookup tot embedded widget. Wij regelen het, jij verkoopt.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 auto-rows-fr">

                <!-- Big feature: RDW lookup -->
                <div data-reveal class="md:col-span-2 group relative bg-white rounded-3xl border border-eazy-darker/10 p-8 overflow-hidden hover:shadow-xl hover:shadow-eazy-darker/5 hover:border-eazy/30 transition-all">
                    <div class="absolute -right-8 -top-8 w-40 h-40 bg-eazy-50 rounded-full group-hover:scale-125 transition-transform duration-500" aria-hidden="true"></div>
                    <div class="relative">
                        <div class="w-12 h-12 bg-eazy-50 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-magnifying-glass text-eazy-dark text-lg" aria-hidden="true"></i>
                        </div>
                        <h3 class="text-xl font-black text-eazy-darker mb-2">RDW-kentekenlookup</h3>
                        <p class="text-sm text-muted leading-relaxed max-w-md">Voer een kenteken in en alle voertuiggegevens (merk, model, bouwjaar, brandstof, APK) worden automatisch opgehaald uit de RDW-database. Geen handmatig invoeren meer.</p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            @foreach(['Merk & model','Bouwjaar','Brandstof','APK-datum','Cilinderinhoud'] as $tag)
                                <span class="px-2.5 py-1 bg-eazy-50 text-eazy-dark text-xs font-semibold rounded-lg">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Embed widget with code + copy button -->
                <div data-reveal data-delay="1" x-data="{ copied: false }" class="group relative bg-eazy-darker rounded-3xl p-8 overflow-hidden hover:shadow-xl transition-all">
                    <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mb-5">
                        <i class="fa-solid fa-code text-eazy-200 text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Widget op je site</h3>
                    <p class="text-sm text-eazy-100/70 leading-relaxed mb-4">Eén regel code op je site en je hele voorraad verschijnt, automatisch bijgewerkt.</p>
                    <div class="bg-black/30 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-2 px-3 py-2 border-b border-white/5">
                            <span class="flex gap-1.5" aria-hidden="true">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-400/70"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-yellow-400/70"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-green-400/70"></span>
                            </span>
                            <span class="text-[10px] font-mono text-white/40 ml-1">embed.html</span>
                            <button type="button"
                                    x-on:click="navigator.clipboard && navigator.clipboard.writeText('&lt;script src=&quot;https://app.eazyautomotive.nl/embed.js&quot;&gt;&lt;/script&gt;'); copied = true; setTimeout(() => copied = false, 1500)"
                                    class="ml-auto inline-flex items-center gap-1.5 text-[10px] font-semibold text-eazy-200 hover:text-white transition">
                                <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'" aria-hidden="true"></i>
                                <span x-text="copied ? 'Gekopieerd' : 'Kopieer'"></span>
                            </button>
                        </div>
                        <div class="p-3 font-mono text-[11px] leading-relaxed">
                            <span class="text-eazy-300">&lt;script</span> <span class="text-amber-300">src=</span><span class="text-emerald-300">"…/embed.js"</span><span class="text-eazy-300">&gt;&lt;/script&gt;</span>
                        </div>
                    </div>
                </div>

                @foreach([
                    ['icon' => 'fa-chart-line', 'bg' => 'bg-blue-50', 'color' => 'text-blue-600', 'title' => 'Statistieken', 'desc' => 'Zie hoeveel bezoekers je auto\'s bekijken. Track views per auto en ontdek je toppers.'],
                    ['icon' => 'fa-images', 'bg' => 'bg-amber-50', 'color' => 'text-amber-600', 'title' => 'Fotobeheer', 'desc' => 'Upload meerdere foto\'s, stel een hoofdfoto in en sorteer met drag & drop.'],
                    ['icon' => 'fa-users', 'bg' => 'bg-purple-50', 'color' => 'text-purple-600', 'title' => 'Multi-tenant', 'desc' => 'Elk bedrijf z\'n eigen omgeving. Meerdere medewerkers werken samen.'],
                    ['icon' => 'fa-palette', 'bg' => 'bg-pink-50', 'color' => 'text-pink-600', 'title' => 'Aanpasbare widget', 'desc' => 'Pas kleuren, kolommen en weergave aan zodat de widget bij je huisstijl past.'],
                ] as $i => $feature)
                <div data-reveal data-delay="{{ $i + 1 }}" class="group bg-white rounded-3xl border border-eazy-darker/10 p-7 hover:shadow-xl hover:shadow-eazy-darker/5 hover:border-eazy/20 hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 {{ $feature['bg'] }} rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <i class="fa-solid {{ $feature['icon'] }} {{ $feature['color'] }} text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-base font-black text-eazy-darker mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-muted leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
                @endforeach

                <!-- Publish to platforms -->
                <div data-reveal data-delay="2" class="group bg-gradient-to-br from-eazy-50 to-white rounded-3xl border border-eazy/15 p-7 hover:shadow-xl hover:shadow-eazy/10 hover:-translate-y-1 transition-all">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center mb-5 shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-share-nodes text-eazy-dark text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-base font-black text-eazy-darker mb-2">Publiceren naar platforms</h3>
                    <p class="text-sm text-muted leading-relaxed">Zet je voorraad in één klik live op externe verkoopplatforms. Eén bron, overal up-to-date.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════ HOW IT WORKS ══════════════════ -->
    <section id="hoe-het-werkt" class="py-20 scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="text-center max-w-2xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border border-eazy-darker/10 text-xs font-bold text-eazy-dark uppercase tracking-wider mb-4 shadow-sm">
                    <i class="fa-solid fa-list-ol text-[10px]" aria-hidden="true"></i> Hoe het werkt
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-eazy-darker tracking-tight">In 3 stappen online</h2>
                <p class="mt-4 text-lg text-muted">Van registratie tot een live voorraad op je website.</p>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- connecting line -->
                <div class="hidden md:block absolute top-8 -translate-y-1/2 left-[16.66%] right-[16.66%] h-0.5 bg-gradient-to-r from-eazy-200 via-eazy-300 to-eazy-200" aria-hidden="true"></div>

                @foreach([
                    ['step' => '1', 'icon' => 'fa-user-plus', 'title' => 'Registreer je bedrijf', 'desc' => 'Maak gratis een account aan en vul je bedrijfsgegevens in. Klaar in een minuut.'],
                    ['step' => '2', 'icon' => 'fa-car', 'title' => "Voeg auto's toe", 'desc' => 'Voer een kenteken in en de gegevens worden automatisch ingevuld via de RDW.'],
                    ['step' => '3', 'icon' => 'fa-code', 'title' => 'Widget op je site', 'desc' => 'Kopieer de code, plak hem op je website en je voorraad staat live.'],
                ] as $i => $step)
                <div data-reveal data-delay="{{ $i + 1 }}" class="relative text-center">
                    <div class="relative w-16 h-16 mx-auto mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-eazy to-eazy-dark rounded-2xl flex items-center justify-center shadow-lg shadow-eazy/25 mx-auto ring-8 ring-[#f4faf9]">
                            <i class="fa-solid {{ $step['icon'] }} text-white text-xl" aria-hidden="true"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-7 h-7 bg-white rounded-full border-2 border-eazy flex items-center justify-center shadow-sm">
                            <span class="text-xs font-black text-eazy-dark">{{ $step['step'] }}</span>
                        </div>
                    </div>
                    <h3 class="text-lg font-black text-eazy-darker mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-muted leading-relaxed max-w-xs mx-auto">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════════════ SHOWCASE ══════════════════ -->
    <section id="showcase" class="py-20 scroll-mt-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div data-reveal>
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border border-eazy-darker/10 text-xs font-bold text-eazy-dark uppercase tracking-wider mb-5 shadow-sm">
                        <i class="fa-solid fa-gauge-high text-[10px]" aria-hidden="true"></i> Dashboard
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-black text-eazy-darker tracking-tight leading-tight">Overzicht over je hele voorraad <span class="text-gradient">in één blik</span></h2>
                    <p class="mt-5 text-lg text-muted leading-relaxed">Realtime statistieken, je meest bekeken auto's en snelle acties. Alles wat je nodig hebt om je voorraad slim te beheren, overzichtelijk op één scherm.</p>
                    <ul class="mt-7 space-y-3">
                        @foreach(['Live statistieken per auto en per dag','Direct zien wat het best bekeken wordt','Snel auto\'s toevoegen, bewerken of reserveren'] as $point)
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-eazy-50 flex items-center justify-center shrink-0 mt-0.5" aria-hidden="true"><i class="fa-solid fa-check text-eazy-dark text-xs"></i></span>
                                <span class="text-sm font-medium text-eazy-darker">{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="btn-shine mt-8 inline-flex items-center gap-2 px-7 py-3.5 bg-eazy-dark text-white font-bold rounded-full hover:bg-eazy-darker transition shadow-lg shadow-eazy/25">
                        Gratis starten <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                    </a>
                </div>

                <!-- Dashboard mockup -->
                <div data-reveal data-delay="2" class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-br from-eazy-200/40 to-eazy-100/20 rounded-[2rem] blur-2xl" aria-hidden="true"></div>
                    <div class="relative float-slow bg-white rounded-3xl p-4 shadow-2xl shadow-eazy-darker/15 border border-white">
                        <div class="bg-[#f4faf9] rounded-2xl overflow-hidden border border-eazy-darker/5">
                            <div class="flex items-center gap-2 px-4 py-3 bg-white border-b border-eazy-darker/5">
                                <div class="flex gap-1.5" aria-hidden="true">
                                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                </div>
                                <div class="ml-3 flex-1 bg-[#f4faf9] rounded-full h-7 flex items-center px-4">
                                    <i class="fa-solid fa-lock text-eazy-darker/25 text-[9px] mr-2" aria-hidden="true"></i>
                                    <span class="text-xs text-muted font-medium">app.eazyautomotive.nl/dashboard</span>
                                </div>
                            </div>
                            <div class="p-5">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                                    @foreach([
                                        ['label' => "Totaal", 'value' => '24', 'icon' => 'fa-car', 'bg' => 'bg-blue-50', 'color' => 'text-blue-600', 'iconColor' => 'text-blue-600'],
                                        ['label' => 'Actief', 'value' => '18', 'icon' => 'fa-circle-check', 'bg' => 'bg-emerald-50', 'color' => 'text-emerald-600', 'iconColor' => 'text-emerald-600'],
                                        ['label' => 'Gereserveerd', 'value' => '3', 'icon' => 'fa-clock', 'bg' => 'bg-amber-50', 'color' => 'text-amber-600', 'iconColor' => 'text-amber-600'],
                                        ['label' => 'Views', 'value' => '142', 'icon' => 'fa-eye', 'bg' => 'bg-eazy-50', 'color' => 'text-eazy-dark', 'iconColor' => 'text-eazy-dark'],
                                    ] as $stat)
                                    <div class="bg-white rounded-2xl border border-eazy-darker/10 p-3">
                                        <div class="w-8 h-8 rounded-xl {{ $stat['bg'] }} flex items-center justify-center mb-2">
                                            <i class="fa-solid {{ $stat['icon'] }} {{ $stat['iconColor'] }} text-xs" aria-hidden="true"></i>
                                        </div>
                                        <p class="text-[9px] font-bold text-muted uppercase tracking-wide">{{ $stat['label'] }}</p>
                                        <p class="text-lg font-black {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    @foreach(['BMW 3 Serie' => '€ 24.950', 'VW Golf' => '€ 18.500', 'Audi A4' => '€ 29.750'] as $car => $price)
                                    <div class="bg-white rounded-xl border border-eazy-darker/10 overflow-hidden">
                                        <div class="h-16 bg-gradient-to-br from-eazy-50 to-eazy-100 flex items-center justify-center" aria-hidden="true">
                                            <i class="fa-solid fa-car text-eazy/20 text-2xl"></i>
                                        </div>
                                        <div class="p-2.5">
                                            <p class="font-bold text-xs text-eazy-darker truncate">{{ $car }}</p>
                                            <p class="text-xs font-black text-eazy-dark">{{ $price }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- floating badge -->
                    <div class="absolute -bottom-5 left-2 sm:-left-5 bg-white rounded-2xl shadow-xl shadow-eazy-darker/10 border border-eazy-darker/5 px-4 py-3 flex items-center gap-3 float-slow" style="animation-delay:1.5s">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center" aria-hidden="true"><i class="fa-solid fa-arrow-trend-up text-emerald-600"></i></div>
                        <div>
                            <p class="text-[10px] font-bold text-muted uppercase">Views vandaag</p>
                            <p class="text-sm font-black text-eazy-darker">+142 <span class="text-emerald-600 text-xs"><span aria-hidden="true">↑</span> 23%</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════ USE CASES ══════════════════ -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border border-eazy-darker/10 text-xs font-bold text-eazy-dark uppercase tracking-wider mb-4 shadow-sm">
                    <i class="fa-solid fa-lightbulb text-[10px]" aria-hidden="true"></i> Waarom EazyAutomotive
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-eazy-darker tracking-tight">Gemaakt voor de praktijk</h2>
                <p class="mt-4 text-lg text-muted">Of je nu tijd wilt besparen, je site wilt vullen of op cijfers wilt sturen.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-5">
                @foreach([
                    ['icon' => 'fa-wand-magic-sparkles', 'title' => 'Voor wie veel auto\'s invoert', 'desc' => 'Geen handmatig typewerk meer: plak het kenteken en de RDW-gegevens staan er. Dat scheelt uren per week.'],
                    ['icon' => 'fa-code', 'title' => 'Voor je eigen website', 'desc' => 'Eén keer de widget instellen en je voorraad blijft automatisch up-to-date op je site. Geen dubbel werk.'],
                    ['icon' => 'fa-chart-line', 'title' => 'Voor wie op cijfers stuurt', 'desc' => 'Zie direct welke auto\'s het best bekeken worden en speel daarop in met je aanbod en prijzen.'],
                ] as $i => $u)
                <div data-reveal data-delay="{{ $i + 1 }}" class="bg-white rounded-3xl border border-eazy-darker/10 p-7 hover:shadow-xl hover:shadow-eazy-darker/5 hover:-translate-y-1 transition-all flex flex-col">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-eazy to-eazy-dark flex items-center justify-center mb-5 shadow-lg shadow-eazy/20">
                        <i class="fa-solid {{ $u['icon'] }} text-white text-lg" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-lg font-black text-eazy-darker mb-2">{{ $u['title'] }}</h3>
                    <p class="text-sm text-muted leading-relaxed flex-1">{{ $u['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════════════ FAQ ══════════════════ -->
    <section id="faq" class="py-20 scroll-mt-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="text-center mb-12">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border border-eazy-darker/10 text-xs font-bold text-eazy-dark uppercase tracking-wider mb-4 shadow-sm">
                    <i class="fa-solid fa-circle-question text-[10px]" aria-hidden="true"></i> FAQ
                </div>
                <h2 class="text-3xl sm:text-5xl font-black text-eazy-darker tracking-tight">Veelgestelde vragen</h2>
            </div>

            <div class="space-y-3" x-data="{ active: 0 }">
                @foreach([
                    ['q' => 'Hoe werkt de kentekenlookup precies?', 'a' => 'Je voert een Nederlands kenteken in en wij halen automatisch de officiële voertuiggegevens op uit de RDW-database: merk, model, bouwjaar, brandstof, APK-datum en meer. Je hoeft alleen nog de prijs en foto\'s toe te voegen.'],
                    ['q' => 'Werkt de widget op mijn bestaande website?', 'a' => 'Ja. Je plakt één regel code op je website en je volledige voorraad verschijnt automatisch. Het werkt op vrijwel elk platform: WordPress, Wix, een eigen site, alles.'],
                    ['q' => 'Kan ik de widget aanpassen aan mijn huisstijl?', 'a' => 'Zeker. In het ontwerp-scherm pas je kleuren, het aantal kolommen en de weergave-opties aan zodat de widget naadloos bij je website past.'],
                    ['q' => 'Kunnen meerdere medewerkers samenwerken?', 'a' => 'Ja. Elk autobedrijf krijgt zijn eigen omgeving waarin meerdere medewerkers samen de voorraad kunnen beheren.'],
                    ['q' => 'Wat kost het en zit ik ergens aan vast?', 'a' => 'Je begint volledig gratis, zonder creditcard. Je zit nergens aan vast en kunt maandelijks opzeggen.'],
                ] as $i => $faq)
                <div data-reveal class="bg-white rounded-3xl border border-eazy-darker/10 overflow-hidden">
                    <button type="button" x-on:click="active === {{ $i }} ? active = null : active = {{ $i }}"
                            :aria-expanded="(active === {{ $i }}).toString()" aria-controls="faq-panel-{{ $i }}" id="faq-trigger-{{ $i }}"
                            class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left">
                        <span class="font-bold text-eazy-darker">{{ $faq['q'] }}</span>
                        <i aria-hidden="true" class="fa-solid fa-chevron-down text-eazy-dark text-sm transition-transform duration-300 shrink-0" :class="active === {{ $i }} ? 'rotate-180' : ''"></i>
                    </button>
                    <div id="faq-panel-{{ $i }}" role="region" aria-labelledby="faq-trigger-{{ $i }}" x-show="active === {{ $i }}" x-collapse x-cloak>
                        <p class="px-6 pb-5 text-sm text-muted leading-relaxed">{{ $faq['a'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════════════ FINAL CTA ══════════════════ -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div data-reveal class="relative bg-gradient-to-br from-eazy-darker via-eazy-darker to-eazy-dark rounded-3xl px-8 py-16 sm:px-16 text-center overflow-hidden shadow-2xl shadow-eazy-darker/25">
                <div class="absolute inset-0 grid-bg opacity-30" aria-hidden="true"></div>
                <div class="absolute -right-10 -bottom-10 opacity-[0.07]" aria-hidden="true">
                    <i class="fa-solid fa-car-side text-[220px] text-white"></i>
                </div>
                <div class="relative z-10 max-w-xl mx-auto">
                    <span class="font-hand text-eazy-50 text-2xl">Klaar in 2 minuten</span>
                    <h2 class="text-3xl sm:text-5xl font-black text-white mb-4 mt-1 tracking-tight">Klaar om te beginnen?</h2>
                    <p class="text-lg text-eazy-50 mb-8">Start vandaag met het beheren van je autovoorraad en bereik meer klanten via je eigen website.</p>
                    <div class="flex flex-col sm:flex-row gap-3.5 justify-center">
                        <a href="{{ route('register') }}" class="btn-shine inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-eazy-darker font-bold rounded-full hover:bg-eazy-50 transition shadow-lg text-base">
                            Gratis starten <i class="fa-solid fa-arrow-right text-sm" aria-hidden="true"></i>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 text-white font-bold rounded-full border border-white/20 hover:bg-white/20 transition text-base">
                            Inloggen
                        </a>
                    </div>
                    <div class="mt-7 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs font-semibold text-eazy-50">
                        <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-eazy-200" aria-hidden="true"></i> Geen creditcard nodig</span>
                        <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-eazy-200" aria-hidden="true"></i> In 2 minuten live</span>
                        <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-eazy-200" aria-hidden="true"></i> Maandelijks opzegbaar</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════ FOOTER ══════════════════ -->
    <footer class="bg-eazy-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-2">
                    <a href="/" class="flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-eazy to-eazy-dark flex items-center justify-center"><i class="fa-solid fa-car-side text-white text-sm" aria-hidden="true"></i></span>
                        <span class="flex items-center gap-1"><span class="text-xl font-black tracking-tight">Eazy</span><span class="text-xl font-light tracking-tight text-eazy-300">Automotive</span></span>
                    </a>
                    <p class="text-sm text-white/70 leading-relaxed max-w-xs">Beheer je autovoorraad slim en toon je aanbod op je eigen website met één regel code. Speciaal gebouwd voor autobedrijven.</p>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white/60 mb-4">Product</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#features" class="text-white/70 hover:text-white transition">Features</a></li>
                        <li><a href="#hoe-het-werkt" class="text-white/70 hover:text-white transition">Hoe het werkt</a></li>
                        <li><a href="#showcase" class="text-white/70 hover:text-white transition">Demo</a></li>
                        <li><a href="#faq" class="text-white/70 hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white/60 mb-4">Account</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('login') }}" class="text-white/70 hover:text-white transition">Inloggen</a></li>
                        <li><a href="{{ route('register') }}" class="text-white/70 hover:text-white transition">Gratis starten</a></li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-8 border-t border-white/10">
                <p class="text-sm text-white/70">&copy; {{ date('Y') }} Eazyonline. Alle rechten voorbehouden.</p>
                <div class="flex items-center gap-2 text-xs text-white/70">
                    <i class="fa-solid fa-shield-check text-eazy-400" aria-hidden="true"></i> Gekoppeld aan de officiële RDW-database
                </div>
            </div>
        </div>
    </footer>

    <!-- ══════════════════ SCRIPTS ══════════════════ -->
    <script>
        // Interactive RDW demo (Alpine component)
        function rdwDemo() {
            return {
                plate: '',
                loading: false,
                done: false,
                price: '€ 32.950',
                result: [],
                presets: {
                    'PZ721H': { price: '€ 32.950', fields: [
                        { label: 'Merk', value: 'BMW', icon: 'fa-car' },
                        { label: 'Model', value: '320i M-Sport', icon: 'fa-gauge-high' },
                        { label: 'Bouwjaar', value: '2021', icon: 'fa-calendar' },
                        { label: 'Brandstof', value: 'Benzine', icon: 'fa-gas-pump' },
                        { label: 'Kleur', value: 'Zwart metallic', icon: 'fa-palette' },
                        { label: 'APK tot', value: '14-03-2027', icon: 'fa-clipboard-check' },
                    ]},
                    'XT884N': { price: '€ 18.750', fields: [
                        { label: 'Merk', value: 'Volkswagen', icon: 'fa-car' },
                        { label: 'Model', value: 'Golf 1.5 TSI', icon: 'fa-gauge-high' },
                        { label: 'Bouwjaar', value: '2020', icon: 'fa-calendar' },
                        { label: 'Brandstof', value: 'Benzine', icon: 'fa-gas-pump' },
                        { label: 'Kleur', value: 'Grijs', icon: 'fa-palette' },
                        { label: 'APK tot', value: '02-09-2026', icon: 'fa-clipboard-check' },
                    ]},
                },
                lookup() {
                    if (this.loading) return;
                    this.loading = true;
                    this.done = false;
                    setTimeout(() => {
                        const key = (this.plate || 'PZ-721-H').replace(/[^A-Z0-9]/gi, '').toUpperCase();
                        const data = this.presets[key] || this.presets['PZ721H'];
                        this.result = data.fields;
                        this.price = data.price;
                        this.loading = false;
                        this.done = true;
                    }, 850);
                },
                autoplay() {
                    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                        this.plate = 'PZ-721-H';
                        this.lookup();
                        return;
                    }
                    const target = 'PZ-721-H';
                    let i = 0;
                    const type = () => {
                        if (i <= target.length) {
                            this.plate = target.slice(0, i);
                            i++;
                            setTimeout(type, 90);
                        } else {
                            this.lookup();
                        }
                    };
                    setTimeout(type, 700);
                },
            };
        }

        // Scroll reveal
        (function () {
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const els = document.querySelectorAll('[data-reveal]');
            if (reduce || !('IntersectionObserver' in window)) {
                els.forEach(el => el.classList.add('reveal-in'));
                return;
            }
            const io = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) { e.target.classList.add('reveal-in'); io.unobserve(e.target); }
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
            els.forEach(el => io.observe(el));
        })();

        // Animated counters
        (function () {
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const counters = document.querySelectorAll('[data-count]');
            const format = (n, target) => {
                const decimals = (String(target).split('.')[1] || '').length;
                return n.toLocaleString('nl-NL', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            };
            const run = (el) => {
                const target = parseFloat(el.getAttribute('data-count'));
                const suffix = el.getAttribute('data-suffix') || '';
                if (reduce) { el.textContent = format(target, target) + suffix; return; }
                const dur = 1500;
                let start = null;
                const step = (ts) => {
                    if (!start) start = ts;
                    const p = Math.min((ts - start) / dur, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = format(target * eased, target) + suffix;
                    if (p < 1) requestAnimationFrame(step);
                    else el.textContent = format(target, target) + suffix;
                };
                requestAnimationFrame(step);
            };
            if (!('IntersectionObserver' in window)) { counters.forEach(run); return; }
            const io = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) { run(e.target); io.unobserve(e.target); } });
            }, { threshold: 0.4 });
            counters.forEach(el => io.observe(el));
        })();
    </script>

    <style>[x-cloak]{display:none!important;}</style>
</body>
</html>
