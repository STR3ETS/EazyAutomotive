<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

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
                    <h1 class="text-2xl font-black text-[#215558]">Integratie</h1>
                    <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Plaats de autowidget op je eigen website</p>
                </div>
                <a href="{{ route('ontwerpen') }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-[#215558] text-white rounded-full text-sm font-bold hover:bg-eazy-darker transition">
                    <i class="fa-solid fa-palette text-xs"></i> Widget aanpassen
                </a>
            </div>

            {{-- Intro Banner --}}
            <div class="bg-gradient-to-br from-eazy-darker via-eazy-dark to-eazy rounded-2xl p-6 text-white mb-8 relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-lg font-bold mb-1">Je autoaanbod op je eigen website</h2>
                    <p class="text-eazy-100/80 text-sm max-w-lg">Met een klein stukje code toon je jouw volledige autoaanbod op elke website. Volg de 3 stappen hieronder en je bent klaar.</p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-[0.07]">
                    <i class="fa-solid fa-code text-[120px]"></i>
                </div>
            </div>

            {{-- Step-by-step Guide --}}
            <div class="space-y-6 mb-8">

                {{-- Step 1 --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-eazy flex items-center justify-center shrink-0">
                            <span class="text-white font-bold text-sm">1</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-[#215558] mb-1">Kopieer de code</h3>
                            <p class="text-xs text-[#215558] opacity-50 mb-4">Kopieer onderstaande code. Dit is alles wat je nodig hebt.</p>

                            <div class="relative">
                                <pre class="bg-gray-900 text-green-400 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed" id="embedCode">&lt;!-- EazyAutomotive Widget --&gt;
&lt;div id="eazy-automotive-widget"&gt;&lt;/div&gt;
&lt;script
  src="{{ url('/embed/v1/widget.js') }}"
  data-api-key="{{ $company->api_key }}"
  data-base-url="{{ url('/') }}"
  defer&gt;
&lt;/script&gt;</pre>
                                <button onclick="copyEmbedCode()" class="cursor-pointer absolute top-3 right-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 text-gray-300 rounded-lg text-xs hover:bg-gray-600 transition" id="copyBtn">
                                    <i class="fa-regular fa-copy"></i> Kopieer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-eazy flex items-center justify-center shrink-0">
                            <span class="text-white font-bold text-sm">2</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-[#215558] mb-1">Plak op je website</h3>
                            <p class="text-xs text-[#215558] opacity-50 mb-4">Plak de code op de plek waar je de auto's wilt tonen. Dit kan op verschillende manieren:</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div class="border border-[#215558]/10 rounded-xl p-4">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center mb-2">
                                        <i class="fa-brands fa-wordpress text-blue-500 text-sm"></i>
                                    </div>
                                    <h4 class="text-xs font-bold text-[#215558] mb-1">WordPress</h4>
                                    <p class="text-[11px] text-[#215558] opacity-50 leading-relaxed">Maak een nieuwe pagina aan, klik op "+" en kies "Aangepaste HTML". Plak de code daarin.</p>
                                </div>
                                <div class="border border-[#215558]/10 rounded-xl p-4">
                                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center mb-2">
                                        <i class="fa-solid fa-file-code text-purple-500 text-sm"></i>
                                    </div>
                                    <h4 class="text-xs font-bold text-[#215558] mb-1">Eigen website</h4>
                                    <p class="text-[11px] text-[#215558] opacity-50 leading-relaxed">Open je HTML-bestand en plak de code op de gewenste plek, bijvoorbeeld in een &lt;div&gt; op je voorraadpagina.</p>
                                </div>
                                <div class="border border-[#215558]/10 rounded-xl p-4">
                                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center mb-2">
                                        <i class="fa-solid fa-puzzle-piece text-orange-500 text-sm"></i>
                                    </div>
                                    <h4 class="text-xs font-bold text-[#215558] mb-1">Websitebouwer</h4>
                                    <p class="text-[11px] text-[#215558] opacity-50 leading-relaxed">Bij Wix, Squarespace of Webflow: gebruik een "Embed" of "Custom Code" blok en plak de code daarin.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-eazy flex items-center justify-center shrink-0">
                            <span class="text-white font-bold text-sm">3</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-[#215558] mb-1">Klaar!</h3>
                            <p class="text-xs text-[#215558] opacity-50 mb-4">Je autoaanbod wordt nu automatisch geladen op je website. Elke auto die je toevoegt of bijwerkt, verschijnt direct.</p>

                            <div class="flex flex-wrap gap-3">
                                <div class="inline-flex items-center gap-2 px-3 py-2 bg-emerald-50 rounded-xl">
                                    <i class="fa-solid fa-arrows-rotate text-emerald-500 text-xs"></i>
                                    <span class="text-xs font-medium text-emerald-700">Automatisch bijgewerkt</span>
                                </div>
                                <div class="inline-flex items-center gap-2 px-3 py-2 bg-blue-50 rounded-xl">
                                    <i class="fa-solid fa-mobile-screen text-blue-500 text-xs"></i>
                                    <span class="text-xs font-medium text-blue-700">Werkt op mobiel</span>
                                </div>
                                <div class="inline-flex items-center gap-2 px-3 py-2 bg-purple-50 rounded-xl">
                                    <i class="fa-solid fa-palette text-purple-500 text-xs"></i>
                                    <span class="text-xs font-medium text-purple-700">Volledig aanpasbaar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- API Key Section --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden mb-6">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[#215558]/5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fa-solid fa-key text-amber-500 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#215558]">API Key</h3>
                        <p class="text-xs text-[#215558] opacity-50">Je unieke sleutel die jouw autoaanbod koppelt</p>
                    </div>
                </div>
                <code class="block bg-gray-50 px-4 py-3 rounded-xl text-xs font-mono text-gray-500 break-all mb-4 border border-gray-100">{{ $company->api_key }}</code>
                <form method="POST" action="{{ route('settings.regenerate-key') }}" onsubmit="return confirm('Weet je het zeker? Als je een nieuwe sleutel genereert, stopt de widget op je website met werken totdat je de code vervangt.')">
                    @csrf
                    <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-full text-xs font-semibold hover:bg-red-100 transition">
                        <i class="fa-solid fa-rotate text-xs"></i> Nieuwe sleutel genereren
                    </button>
                </form>
            </div>

            {{-- FAQ / Help --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[#215558]/5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-circle-question text-blue-500 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#215558]">Veelgestelde vragen</h3>
                        <p class="text-xs text-[#215558] opacity-50">Antwoorden op de meest voorkomende vragen</p>
                    </div>
                </div>

                <div x-data="{ open: null }" class="space-y-2">
                    @foreach([
                        ['Moet ik iets updaten als ik een auto toevoeg?', 'Nee! Zodra je een auto toevoegt, bewerkt of verwijdert in EazyAutomotive, wordt je widget automatisch bijgewerkt. Je hoeft niets aan je website te veranderen.'],
                        ['Kan ik het uiterlijk van de widget aanpassen?', 'Ja, ga naar de pagina <strong>Ontwerpen</strong> in het menu. Daar pas je kleuren, lettertype, layout en meer aan. Wijzigingen zijn direct zichtbaar op je website.'],
                        ['Werkt het op mobiele telefoons?', 'Ja, de widget past zich automatisch aan op elk schermformaat. Op mobiel wordt het overzicht in 1 kolom getoond, op tablet in 2 kolommen en op desktop in het aantal dat je hebt ingesteld.'],
                        ['Wat als mijn website een websitebouwer gebruikt?', 'De meeste websitebouwers (WordPress, Wix, Squarespace, Webflow) bieden een "Custom HTML" of "Embed Code" blok. Plak daar de code in en het werkt direct.'],
                        ['Is de widget veilig?', 'Ja. De widget laadt alleen je publieke autogegevens via een beveiligde verbinding (HTTPS). Er worden geen persoonlijke gegevens gedeeld.'],
                    ] as $i => [$question, $answer])
                    <div class="border border-[#215558]/5 rounded-xl overflow-hidden">
                        <button @click="open = open === {{ $i }} ? null : {{ $i }}" class="cursor-pointer w-full flex items-center justify-between px-4 py-3 text-left hover:bg-gray-50 transition">
                            <span class="text-sm font-medium text-[#215558]">{{ $question }}</span>
                            <i class="fa-solid fa-chevron-down text-[#215558]/30 text-xs transition-transform duration-200" :class="{ 'rotate-180': open === {{ $i }} }"></i>
                        </button>
                        <div x-show="open === {{ $i }}" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                            <div class="px-4 pb-3 text-xs text-[#215558] opacity-60 leading-relaxed">{!! $answer !!}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <script>
        function copyEmbedCode() {
            const code = document.getElementById('embedCode').textContent;
            navigator.clipboard.writeText(code.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&'));
            const btn = document.getElementById('copyBtn');
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Gekopieerd!';
            setTimeout(() => btn.innerHTML = '<i class="fa-regular fa-copy"></i> Kopieer', 2000);
        }
    </script>
</x-app-layout>
