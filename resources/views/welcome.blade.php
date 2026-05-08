<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EazyAutomotive - Beheer je autovoorraad, bereik meer klanten</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter-tight:300,400,500,600,700,800,900&family=caveat:400,700" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-[#ebf2f2] font-sans">

    <!-- Navbar -->
    <nav class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-md border-b border-[#215558]/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="flex items-center gap-1.5">
                    <span class="text-xl font-black text-[#215558]">Eazy</span>
                    <span class="text-xl font-light text-eazy">Automotive</span>
                </a>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white text-sm font-bold rounded-full hover:bg-eazy-dark transition shadow-lg shadow-eazy/20">
                            <i class="fa-solid fa-gauge text-xs"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-[#215558] hover:text-eazy transition">Inloggen</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white text-sm font-bold rounded-full hover:bg-eazy-dark transition shadow-lg shadow-eazy/20">
                            Gratis starten
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-32 pb-16 sm:pt-40 sm:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border border-[#215558]/10 text-xs font-bold text-eazy uppercase tracking-wider mb-6">
                    <i class="fa-solid fa-car-side text-[10px]"></i> Voor autobedrijven
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-[#215558] leading-tight tracking-tight">
                    Beheer je voorraad,
                    <span class="text-eazy">bereik meer klanten</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-[#215558]/60 leading-relaxed max-w-2xl mx-auto">
                    Voeg auto's toe met kentekenlookup, beheer je voorraad en toon je aanbod op je eigen website met één regel code.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-eazy text-white font-bold rounded-full hover:bg-eazy-dark transition shadow-lg shadow-eazy/20 text-base">
                        Gratis account aanmaken
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-[#215558] font-bold rounded-full border border-[#215558]/10 hover:border-eazy/30 hover:text-eazy transition text-base">
                        <i class="fa-solid fa-sparkles text-sm"></i>
                        Bekijk features
                    </a>
                </div>
            </div>

            <!-- Preview mockup -->
            <div class="mt-16 sm:mt-20 max-w-5xl mx-auto">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-[#215558]/5 border border-[#215558]/10">
                    <div class="bg-[#ebf2f2] rounded-2xl overflow-hidden border border-[#215558]/5">
                        <!-- Fake browser bar -->
                        <div class="flex items-center gap-2 px-4 py-3 bg-white border-b border-[#215558]/5">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="ml-4 flex-1 bg-[#ebf2f2] rounded-full h-7 flex items-center px-4">
                                <span class="text-xs text-[#215558]/40 font-medium">app.eazyautomotive.nl/dashboard</span>
                            </div>
                        </div>
                        <!-- Fake dashboard content -->
                        <div class="p-6">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                                @foreach([
                                    ['label' => "Totaal auto's", 'value' => '24', 'icon' => 'fa-car', 'bg' => 'bg-blue-50', 'color' => 'text-blue-600', 'iconColor' => 'text-blue-500'],
                                    ['label' => 'Actief', 'value' => '18', 'icon' => 'fa-circle-check', 'bg' => 'bg-emerald-50', 'color' => 'text-emerald-600', 'iconColor' => 'text-emerald-500'],
                                    ['label' => 'Gereserveerd', 'value' => '3', 'icon' => 'fa-clock', 'bg' => 'bg-amber-50', 'color' => 'text-amber-600', 'iconColor' => 'text-amber-500'],
                                    ['label' => 'Views vandaag', 'value' => '142', 'icon' => 'fa-eye', 'bg' => 'bg-eazy-50', 'color' => 'text-eazy', 'iconColor' => 'text-eazy'],
                                ] as $stat)
                                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ $stat['bg'] }} flex items-center justify-center shrink-0">
                                        <i class="fa-solid {{ $stat['icon'] }} {{ $stat['iconColor'] }} text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-[#215558] opacity-50 uppercase tracking-wide">{{ $stat['label'] }}</p>
                                        <p class="text-xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @foreach(['BMW 3 Serie' => '€ 24.950', 'VW Golf' => '€ 18.500', 'Audi A4' => '€ 29.750'] as $car => $price)
                                <div class="bg-white rounded-xl border border-[#215558]/10 overflow-hidden">
                                    <div class="h-24 bg-gradient-to-br from-[#ebf2f2] to-[#d5e8e8] flex items-center justify-center">
                                        <i class="fa-solid fa-car text-[#215558]/10 text-3xl"></i>
                                    </div>
                                    <div class="p-3">
                                        <p class="font-bold text-sm text-[#215558]">{{ $car }}</p>
                                        <p class="text-sm font-bold text-eazy">{{ $price }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border border-[#215558]/10 text-xs font-bold text-eazy uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-bolt text-[10px]"></i> Features
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-[#215558]">Alles wat je nodig hebt</h2>
                <p class="mt-4 text-lg text-[#215558]/50">Van kentekenlookup tot embedded widget — wij regelen het.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @foreach([
                    ['icon' => 'fa-magnifying-glass', 'bg' => 'bg-eazy-50', 'color' => 'text-eazy', 'title' => 'RDW Kenteken Lookup', 'desc' => 'Voer een kenteken in en alle voertuiggegevens worden automatisch opgehaald via de RDW database. Geen handmatig invoeren meer.'],
                    ['icon' => 'fa-code', 'bg' => 'bg-indigo-50', 'color' => 'text-indigo-500', 'title' => 'Embed Widget', 'desc' => 'Eén stukje code op je website plakken en je volledige autovoorraad wordt getoond. Automatisch bijgewerkt bij wijzigingen.'],
                    ['icon' => 'fa-chart-line', 'bg' => 'bg-blue-50', 'color' => 'text-blue-500', 'title' => 'Statistieken', 'desc' => 'Zie hoeveel bezoekers je auto\'s bekijken. Track views per auto en ontdek welke modellen het populairst zijn.'],
                    ['icon' => 'fa-images', 'bg' => 'bg-amber-50', 'color' => 'text-amber-500', 'title' => 'Foto Beheer', 'desc' => 'Upload meerdere foto\'s per auto, stel een hoofdfoto in en sorteer de volgorde. Alles drag & drop.'],
                    ['icon' => 'fa-users', 'bg' => 'bg-purple-50', 'color' => 'text-purple-500', 'title' => 'Multi-tenant', 'desc' => 'Elk autobedrijf krijgt zijn eigen omgeving. Meerdere medewerkers kunnen samenwerken aan het beheer.'],
                    ['icon' => 'fa-palette', 'bg' => 'bg-pink-50', 'color' => 'text-pink-500', 'title' => 'Aanpasbare Widget', 'desc' => 'Pas kleuren, aantal kolommen en weergave-opties aan zodat de widget bij je website past.'],
                ] as $feature)
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-7 hover:shadow-lg hover:border-eazy/20 transition-all group">
                    <div class="w-12 h-12 {{ $feature['bg'] }} rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                        <i class="fa-solid {{ $feature['icon'] }} {{ $feature['color'] }} text-lg"></i>
                    </div>
                    <h3 class="text-base font-bold text-[#215558] mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-[#215558]/50 leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white rounded-full border border-[#215558]/10 text-xs font-bold text-eazy uppercase tracking-wider mb-4">
                    <i class="fa-solid fa-list-ol text-[10px]"></i> Hoe het werkt
                </div>
                <h2 class="text-3xl sm:text-4xl font-black text-[#215558]">In 3 stappen online</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['step' => '1', 'icon' => 'fa-user-plus', 'title' => 'Registreer je bedrijf', 'desc' => 'Maak gratis een account aan en vul je bedrijfsgegevens in.'],
                    ['step' => '2', 'icon' => 'fa-car', 'title' => 'Voeg auto\'s toe', 'desc' => 'Voer een kenteken in en de gegevens worden automatisch ingevuld via RDW.'],
                    ['step' => '3', 'icon' => 'fa-code', 'title' => 'Embed op je site', 'desc' => 'Kopieer de embed code en plak het op je website. Klaar!'],
                ] as $step)
                <div class="text-center">
                    <div class="relative w-18 h-18 mx-auto mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-eazy to-eazy-dark rounded-2xl flex items-center justify-center shadow-lg shadow-eazy/20 mx-auto">
                            <i class="fa-solid {{ $step['icon'] }} text-white text-xl"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-7 h-7 bg-white rounded-full border-2 border-eazy flex items-center justify-center">
                            <span class="text-xs font-black text-eazy">{{ $step['step'] }}</span>
                        </div>
                    </div>
                    <h3 class="text-base font-bold text-[#215558] mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-[#215558]/50">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-[#215558] via-eazy-dark to-eazy rounded-3xl px-8 py-16 sm:px-16 text-center relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 opacity-[0.07]">
                    <i class="fa-solid fa-car-side text-[200px] text-white"></i>
                </div>
                <div class="relative z-10">
                    <h2 class="text-3xl sm:text-4xl font-black text-white mb-4">Klaar om te beginnen?</h2>
                    <p class="text-lg text-eazy-200 mb-8 max-w-xl mx-auto">Start vandaag nog met het beheren van je autovoorraad en bereik meer klanten via je website.</p>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-white text-[#215558] font-bold rounded-full hover:bg-eazy-50 transition shadow-lg text-base">
                        Gratis account aanmaken
                        <i class="fa-solid fa-arrow-right text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-[#215558]/5 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <span class="text-lg font-black text-[#215558]">Eazy</span>
                    <span class="text-lg font-light text-eazy">Automotive</span>
                </div>
                <p class="text-sm text-[#215558]/30 font-medium">&copy; {{ date('Y') }} Eazyonline. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>

</body>
</html>
