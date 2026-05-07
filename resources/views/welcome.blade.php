<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EazyAutomotive - Beheer je autovoorraad, bereik meer klanten</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter-tight:300,400,500,600,700,800&family=caveat:400,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-800 font-sans">

    <!-- Navbar -->
    <nav class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="/" class="flex items-center gap-1.5">
                    <span class="text-xl font-bold text-eazy-darker">Eazy</span>
                    <span class="text-xl font-light text-eazy">Automotive</span>
                </a>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-eazy transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-eazy transition">Inloggen</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 bg-eazy text-white text-sm font-semibold rounded-lg hover:bg-eazy-dark transition shadow-sm">
                            Gratis starten
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-32 pb-20 sm:pt-40 sm:pb-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <p class="text-sm font-semibold text-eazy uppercase tracking-wider mb-4">Voor autobedrijven</p>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    Beheer je voorraad,
                    <span class="text-eazy">bereik meer klanten</span>
                </h1>
                <p class="mt-6 text-lg sm:text-xl text-gray-500 leading-relaxed max-w-2xl mx-auto">
                    Voeg auto's toe met kentekenlookup, beheer je voorraad en toon je aanbod op je eigen website met één regel code.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-eazy text-white font-semibold rounded-lg hover:bg-eazy-dark transition shadow-lg shadow-eazy/20 text-base">
                        Gratis account aanmaken
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center px-8 py-3.5 bg-white text-gray-700 font-semibold rounded-lg border border-gray-200 hover:border-eazy-300 hover:text-eazy transition text-base">
                        Bekijk features
                    </a>
                </div>
            </div>

            <!-- Preview mockup -->
            <div class="mt-16 sm:mt-20 max-w-5xl mx-auto">
                <div class="bg-gradient-to-br from-eazy-50 to-eazy-100 rounded-2xl p-6 sm:p-10 shadow-xl shadow-eazy/5 border border-eazy-200/50">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                        <!-- Fake browser bar -->
                        <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-100">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="ml-4 flex-1 bg-gray-200 rounded-md h-6 flex items-center px-3">
                                <span class="text-xs text-gray-400">app.eazyautomotive.nl/dashboard</span>
                            </div>
                        </div>
                        <!-- Fake dashboard content -->
                        <div class="p-6">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                                <div class="bg-eazy-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-500">Totaal auto's</p>
                                    <p class="text-2xl font-bold text-eazy-darker">24</p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-500">Actief</p>
                                    <p class="text-2xl font-bold text-green-700">18</p>
                                </div>
                                <div class="bg-amber-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-500">Gereserveerd</p>
                                    <p class="text-2xl font-bold text-amber-700">3</p>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <p class="text-xs text-gray-500">Views vandaag</p>
                                    <p class="text-2xl font-bold text-blue-700">142</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                @foreach(['BMW 3 Serie' => '€ 24.950', 'VW Golf' => '€ 18.500', 'Audi A4' => '€ 29.750'] as $car => $price)
                                <div class="border border-gray-100 rounded-lg overflow-hidden">
                                    <div class="h-24 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                                    </div>
                                    <div class="p-3">
                                        <p class="font-semibold text-sm text-gray-800">{{ $car }}</p>
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
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <p class="text-sm font-semibold text-eazy uppercase tracking-wider mb-2">Features</p>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">Alles wat je nodig hebt</h2>
                <p class="mt-4 text-lg text-gray-500">Van kentekenlookup tot embedded widget — wij regelen het.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100 hover:shadow-md hover:border-eazy-200 transition">
                    <div class="w-12 h-12 bg-eazy-50 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-eazy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">RDW Kenteken Lookup</h3>
                    <p class="text-gray-500 leading-relaxed">Voer een kenteken in en alle voertuiggegevens worden automatisch opgehaald via de RDW database. Geen handmatig invoeren meer.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100 hover:shadow-md hover:border-eazy-200 transition">
                    <div class="w-12 h-12 bg-eazy-50 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-eazy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Embed Widget</h3>
                    <p class="text-gray-500 leading-relaxed">Eén stukje code op je website plakken en je volledige autovoorraad wordt getoond. Automatisch bijgewerkt bij wijzigingen.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100 hover:shadow-md hover:border-eazy-200 transition">
                    <div class="w-12 h-12 bg-eazy-50 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-eazy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Statistieken</h3>
                    <p class="text-gray-500 leading-relaxed">Zie hoeveel bezoekers je auto's bekijken. Track views per auto en ontdek welke modellen het populairst zijn.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100 hover:shadow-md hover:border-eazy-200 transition">
                    <div class="w-12 h-12 bg-eazy-50 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-eazy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Foto Beheer</h3>
                    <p class="text-gray-500 leading-relaxed">Upload meerdere foto's per auto, stel een hoofdfoto in en sorteer de volgorde. Alles drag & drop.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100 hover:shadow-md hover:border-eazy-200 transition">
                    <div class="w-12 h-12 bg-eazy-50 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-eazy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Multi-tenant</h3>
                    <p class="text-gray-500 leading-relaxed">Elk autobedrijf krijgt zijn eigen omgeving. Meerdere medewerkers kunnen samenwerken aan het beheer.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100 hover:shadow-md hover:border-eazy-200 transition">
                    <div class="w-12 h-12 bg-eazy-50 rounded-xl flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-eazy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Aanpasbare Widget</h3>
                    <p class="text-gray-500 leading-relaxed">Pas kleuren, aantal kolommen en weergave-opties aan zodat de widget bij je website past.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <p class="text-sm font-semibold text-eazy uppercase tracking-wider mb-2">Hoe het werkt</p>
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900">In 3 stappen online</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-16 h-16 bg-eazy rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-eazy/20">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Registreer je bedrijf</h3>
                    <p class="text-gray-500">Maak gratis een account aan en vul je bedrijfsgegevens in.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-eazy rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-eazy/20">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Voeg auto's toe</h3>
                    <p class="text-gray-500">Voer een kenteken in en de gegevens worden automatisch ingevuld via RDW.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-eazy rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-eazy/20">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Embed op je site</h3>
                    <p class="text-gray-500">Kopieer de embed code en plak het op je website. Klaar!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-eazy-darker via-eazy-700 to-eazy-dark rounded-2xl px-8 py-16 sm:px-16 text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">Klaar om te beginnen?</h2>
                <p class="text-lg text-eazy-200 mb-8 max-w-xl mx-auto">Start vandaag nog met het beheren van je autovoorraad en bereik meer klanten via je website.</p>
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3.5 bg-white text-eazy-darker font-semibold rounded-lg hover:bg-eazy-50 transition shadow-lg text-base">
                    Gratis account aanmaken
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-100 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <span class="text-lg font-bold text-eazy-darker">Eazy</span>
                    <span class="text-lg font-light text-eazy">Automotive</span>
                </div>
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Eazyonline. Alle rechten voorbehouden.</p>
            </div>
        </div>
    </footer>

</body>
</html>
