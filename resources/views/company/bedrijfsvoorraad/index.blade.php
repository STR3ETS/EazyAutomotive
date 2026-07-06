<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-eazy-50 flex items-center justify-center">
                    <i class="fa-solid fa-file-signature text-eazy"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Vrijwaring &amp; bedrijfsvoorraad</h1>
                    <p class="text-sm text-[#215558] opacity-50">Neem een gekocht voertuig op in de bedrijfsvoorraad (vrijwaart de vorige eigenaar) of geef het weer uit bij verkoop.</p>
                </div>
            </div>

            {{-- Mode-banner --}}
            @if($mode === 'sandbox')
                <div class="mb-6 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-flask text-lg mt-0.5"></i>
                    <div class="text-sm">
                        <p class="font-bold">Testmodus (sandbox)</p>
                        <p class="opacity-90">Mutaties worden gesimuleerd en gaan <span class="font-semibold">niet</span> naar de RDW. Zo test je de volledige flow zonder erkenning of certificaat. Zet <code class="text-[12px] bg-white/60 px-1 rounded">RDW_ORV_MODE=soap</code> met een geldig certificaat en erkenning om echt te gaan muteren.</p>
                    </div>
                </div>
            @elseif(! $configured)
                <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-plug-circle-xmark text-lg mt-0.5"></i>
                    <div class="text-sm">
                        <p class="font-bold">RDW-koppeling niet compleet</p>
                        <p class="opacity-90">Modus staat op live (soap), maar WSDL, certificaat of erkenningsnummer ontbreekt. Vul de RDW_ORV_* instellingen aan.</p>
                    </div>
                </div>
            @else
                <div class="mb-6 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-shield-halved text-lg mt-0.5"></i>
                    <div class="text-sm">
                        <p class="font-bold">Live gekoppeld met de RDW (ORV)</p>
                        <p class="opacity-90">Mutaties worden echt in het kentekenregister verwerkt. Controleer kenteken en tenaamstellingscode goed voordat je bevestigt.</p>
                    </div>
                </div>
            @endif

            {{-- Uitleg tenaamstellingscode --}}
            <div class="mb-6 flex items-start gap-3 bg-[#215558]/[0.04] border border-[#215558]/10 text-[#215558] px-5 py-3.5 rounded-xl">
                <i class="fa-solid fa-circle-info text-lg mt-0.5 text-eazy"></i>
                <p class="text-sm opacity-80">De <span class="font-semibold">tenaamstellingscode</span> (meldcode) staat op de kentekencard of het tenaamstellingsverslag: 4 tot 12 cijfers. Die code hebben we nodig om de mutatie te doen, maar we slaan hem nooit op.</p>
            </div>

            {{-- Twee mutatie-formulieren --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
                {{-- Vrijwaren --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-arrow-right-to-bracket text-eazy"></i>
                        <h2 class="text-sm font-black text-[#215558]">In bedrijfsvoorraad (vrijwaren)</h2>
                    </div>
                    <p class="text-[12px] text-[#215558] opacity-50 mb-4">Voor een auto die je hebt ingekocht. De vorige eigenaar wordt gevrijwaard en je krijgt een vrijwaringsbewijs.</p>

                    <form method="POST" action="{{ route('bedrijfsvoorraad.vrijwaren') }}" x-data="{ busy: false }" @submit="busy = true">
                        @csrf
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Kenteken</label>
                        <input type="text" name="kenteken" required maxlength="12" placeholder="AB-123-C"
                               class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm uppercase tracking-widest font-semibold focus:border-eazy focus:ring-eazy mb-4">

                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Tenaamstellingscode</label>
                        <input type="text" name="tenaamstellingscode" required inputmode="numeric" maxlength="20" placeholder="123456789"
                               class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm tracking-widest focus:border-eazy focus:ring-eazy mb-4">

                        <button type="submit" :disabled="busy"
                            class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark disabled:opacity-50 disabled:cursor-default transition">
                            <i class="fa-solid" :class="busy ? 'fa-spinner fa-spin' : 'fa-file-signature'"></i>
                            <span x-text="busy ? 'Bezig...' : 'Vrijwaren'"></span>
                        </button>
                    </form>
                </div>

                {{-- Uit bedrijfsvoorraad --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-arrow-right-from-bracket text-[#215558]"></i>
                        <h2 class="text-sm font-black text-[#215558]">Uit bedrijfsvoorraad</h2>
                    </div>
                    <p class="text-[12px] text-[#215558] opacity-50 mb-4">Bij verkoop: het voertuig wordt uit je bedrijfsvoorraad gegeven en op naam van de koper gezet.</p>

                    <form method="POST" action="{{ route('bedrijfsvoorraad.uit') }}" x-data="{ busy: false }" @submit="busy = true">
                        @csrf
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Kenteken</label>
                        <input type="text" name="kenteken" required maxlength="12" placeholder="AB-123-C"
                               class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm uppercase tracking-widest font-semibold focus:border-eazy focus:ring-eazy mb-4">

                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Tenaamstellingscode</label>
                        <input type="text" name="tenaamstellingscode" required inputmode="numeric" maxlength="20" placeholder="123456789"
                               class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm tracking-widest focus:border-eazy focus:ring-eazy mb-4">

                        <button type="submit" :disabled="busy"
                            class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-[#215558] text-white rounded-full text-sm font-bold hover:opacity-90 disabled:opacity-50 disabled:cursor-default transition">
                            <i class="fa-solid" :class="busy ? 'fa-spinner fa-spin' : 'fa-arrow-right-from-bracket'"></i>
                            <span x-text="busy ? 'Bezig...' : 'Uit voorraad geven'"></span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Historie --}}
            <h3 class="text-sm font-black text-[#215558] mb-3">Recente mutaties</h3>
            @if($mutaties->count() > 0)
                <div class="bg-white rounded-2xl border border-[#215558]/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[10px] font-bold text-[#215558] opacity-40 uppercase tracking-wider border-b border-[#215558]/10">
                                    <th class="px-5 py-3">Datum</th>
                                    <th class="px-5 py-3">Type</th>
                                    <th class="px-5 py-3">Kenteken</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Vrijwaringsbewijs</th>
                                    <th class="px-5 py-3">Bron</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#215558]/5">
                                @foreach($mutaties as $mutatie)
                                    <tr>
                                        <td class="px-5 py-3 text-[#215558] opacity-70 whitespace-nowrap">{{ $mutatie->created_at->format('d-m-Y H:i') }}</td>
                                        <td class="px-5 py-3 text-[#215558]">{{ $mutatie->typeLabel() }}</td>
                                        <td class="px-5 py-3 font-semibold text-[#215558] uppercase tracking-wider">{{ $mutatie->kenteken }}</td>
                                        <td class="px-5 py-3">
                                            @if($mutatie->isGeslaagd())
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-wide"><i class="fa-solid fa-circle-check text-[9px]"></i> Geslaagd</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 text-red-500 text-[10px] font-bold uppercase tracking-wide" title="{{ $mutatie->foutmelding }}"><i class="fa-solid fa-circle-xmark text-[9px]"></i> Mislukt</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-[#215558] opacity-70">{{ $mutatie->vrijwaringsbewijs ?: '-' }}</td>
                                        <td class="px-5 py-3">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $mutatie->mode === 'soap' ? 'bg-eazy/10 text-eazy' : 'bg-amber-50 text-amber-600' }} uppercase tracking-wide">{{ $mutatie->mode === 'soap' ? 'RDW' : 'Sandbox' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center py-12 text-[#215558] opacity-40 text-sm bg-white rounded-2xl border border-[#215558]/10">Nog geen mutaties. Vrijwaar hierboven je eerste voertuig.</div>
            @endif

        </div>
    </div>
</x-app-layout>
