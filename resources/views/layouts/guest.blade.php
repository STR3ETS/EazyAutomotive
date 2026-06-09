<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EazyAutomotive') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter-tight:300,400,500,600,700,800,900&family=caveat:400,700" rel="stylesheet" />

        <!-- FontAwesome -->
        <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-hand { font-family: 'Caveat', cursive; }

            /* Aurora orbs */
            .orb { position: absolute; border-radius: 9999px; filter: blur(60px); will-change: transform; }
            .orb-1 { animation: floatA 16s ease-in-out infinite; }
            .orb-2 { animation: floatB 20s ease-in-out infinite; }
            @keyframes floatA { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(30px,-24px) scale(1.15); } }
            @keyframes floatB { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(-34px,26px) scale(1.12); } }

            /* Grid backdrop */
            .grid-bg {
                background-image: linear-gradient(to right, rgba(255,255,255,.06) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(255,255,255,.06) 1px, transparent 1px);
                background-size: 40px 40px;
            }
            .grid-bg-dark {
                background-image: linear-gradient(to right, rgba(33,85,88,.06) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(33,85,88,.06) 1px, transparent 1px);
                background-size: 40px 40px;
                -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 40%, #000 30%, transparent 100%);
                        mask-image: radial-gradient(ellipse 80% 70% at 50% 40%, #000 30%, transparent 100%);
            }

            /* Card entrance */
            .auth-card { animation: cardIn .55s cubic-bezier(.22,1,.36,1) both; }
            @keyframes cardIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }

            /* Button shine */
            .btn-shine { position: relative; overflow: hidden; }
            .btn-shine::after {
                content: ''; position: absolute; top: 0; left: -120%; width: 60%; height: 100%;
                background: linear-gradient(110deg, transparent, rgba(255,255,255,.35), transparent);
                transform: skewX(-20deg); transition: left .7s ease;
            }
            .btn-shine:hover::after { left: 130%; }

            @media (prefers-reduced-motion: reduce) {
                .orb-1,.orb-2,.auth-card { animation: none !important; }
            }
        </style>
    </head>
    <body class="font-sans antialiased text-eazy-darker">
        <div class="min-h-screen lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)]">

            {{-- ░░░ Brand panel (desktop) ░░░ --}}
            <aside class="hidden lg:flex relative flex-col justify-between p-12 xl:p-16 bg-gradient-to-br from-eazy-darker via-eazy-dark to-eazy-dark text-white overflow-hidden">
                <div class="absolute inset-0 grid-bg opacity-70" aria-hidden="true"></div>
                <div class="orb orb-1 w-96 h-96 bg-white/10 -top-20 -left-16" aria-hidden="true"></div>
                <div class="orb orb-2 w-[28rem] h-[28rem] bg-black/15 -bottom-24 -right-16" aria-hidden="true"></div>
                <div class="absolute -right-12 -bottom-12 opacity-[0.06]" aria-hidden="true">
                    <i class="fa-solid fa-car-side text-[260px]"></i>
                </div>

                {{-- Logo --}}
                <a href="/" class="relative z-10 inline-flex items-center gap-2.5 w-max">
                    <span class="w-9 h-9 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">
                        <i class="fa-solid fa-car-side text-white text-base" aria-hidden="true"></i>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="text-2xl font-black tracking-tight">Eazy</span><span class="text-2xl font-light tracking-tight text-eazy-300">Automotive</span>
                    </span>
                </a>

                {{-- Value proposition --}}
                <div class="relative z-10 max-w-md">
                    <span class="font-hand text-eazy-100 text-2xl">Speciaal voor autobedrijven</span>
                    <h2 class="text-4xl xl:text-5xl font-black leading-[1.1] tracking-tight mt-2 mb-8">
                        Je hele voorraad online, in een paar klikken.
                    </h2>
                    <ul class="space-y-5">
                        @foreach([
                            ['icon' => 'fa-magnifying-glass', 'title' => 'RDW-kentekenlookup', 'desc' => "Voeg auto's toe met één kenteken. Gegevens automatisch ingevuld."],
                            ['icon' => 'fa-code', 'title' => 'Widget op je site', 'desc' => 'Toon je voorraad met één regel code, altijd up-to-date.'],
                            ['icon' => 'fa-chart-line', 'title' => 'Inzicht in cijfers', 'desc' => 'Zie direct welke auto\'s het best bekeken worden.'],
                        ] as $prop)
                        <li class="flex items-start gap-4">
                            <span class="w-11 h-11 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center shrink-0">
                                <i class="fa-solid {{ $prop['icon'] }} text-eazy-100" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="font-bold">{{ $prop['title'] }}</p>
                                <p class="text-sm text-eazy-50 leading-relaxed">{{ $prop['desc'] }}</p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Footer --}}
                <div class="relative z-10 flex items-center gap-2 text-xs font-semibold text-eazy-50">
                    <i class="fa-solid fa-shield-check text-eazy-200" aria-hidden="true"></i>
                    Gekoppeld aan de officiële RDW-database
                </div>
            </aside>

            {{-- ░░░ Form panel ░░░ --}}
            <main class="relative flex flex-col items-center justify-center px-5 py-10 sm:px-8 bg-[#f4faf9] overflow-hidden min-h-screen">
                {{-- Mobile/ambient backdrop --}}
                <div class="absolute inset-0 grid-bg-dark" aria-hidden="true"></div>
                <div class="orb orb-1 w-72 h-72 bg-eazy-200/40 -top-16 -right-10 lg:hidden" aria-hidden="true"></div>
                <div class="orb orb-2 w-72 h-72 bg-eazy-100/50 -bottom-16 -left-10 lg:hidden" aria-hidden="true"></div>

                <div class="relative z-10 w-full max-w-md">
                    {{-- Mobile logo --}}
                    <a href="/" class="lg:hidden flex items-center justify-center gap-2 mb-6">
                        <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-eazy to-eazy-dark flex items-center justify-center shadow-lg shadow-eazy/30">
                            <i class="fa-solid fa-car-side text-white text-sm" aria-hidden="true"></i>
                        </span>
                        <span class="flex items-center gap-1">
                            <span class="text-xl font-black text-eazy-darker tracking-tight">Eazy</span><span class="text-xl font-light text-eazy-dark tracking-tight">Automotive</span>
                        </span>
                    </a>

                    <div class="auth-card bg-white rounded-3xl shadow-xl shadow-eazy-darker/5 border border-eazy-darker/10 p-7 sm:p-9">
                        {{ $slot }}
                    </div>

                    <div class="mt-6 text-center">
                        <a href="/" class="inline-flex items-center gap-1.5 text-xs font-semibold text-muted hover:text-eazy-dark transition">
                            <i class="fa-solid fa-arrow-left text-[10px]" aria-hidden="true"></i> Terug naar de website
                        </a>
                        <p class="mt-3 text-xs text-muted">&copy; {{ date('Y') }} Eazyonline. Alle rechten voorbehouden.</p>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
