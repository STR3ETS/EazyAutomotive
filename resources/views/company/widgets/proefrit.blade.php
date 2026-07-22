<x-app-layout>
    @php $s = $company->embed_settings ?? []; @endphp
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i><span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('widgets.index') }}" class="w-9 h-9 rounded-lg bg-white border border-[#215558]/10 flex items-center justify-center text-[#215558] hover:border-eazy transition"><i class="fa-solid fa-arrow-left text-sm"></i></a>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Proefrit-widget</h1>
                    <p class="text-sm text-[#215558] opacity-50">Laat bezoekers een proefrit aanvragen. Aanvragen komen bij Proefritten.</p>
                </div>
            </div>

            {{-- Teksten & opties --}}
            <form method="POST" action="{{ route('widgets.proefrit.update') }}" class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-5">
                @csrf @method('PUT')
                <h3 class="text-sm font-black text-[#215558] mb-4">Teksten &amp; opties</h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Titel</label>
                            <input type="text" name="proefrit_titel" maxlength="80" value="{{ $s['proefrit_titel'] ?? '' }}" placeholder="Plan een proefrit" class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#374151] mb-1.5">Knoptekst</label>
                            <input type="text" name="proefrit_knop" maxlength="50" value="{{ $s['proefrit_knop'] ?? '' }}" placeholder="Aanvraag versturen" class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Introtekst</label>
                        <input type="text" name="proefrit_intro" maxlength="300" value="{{ $s['proefrit_intro'] ?? '' }}" placeholder="Kies een auto en een moment dat jou uitkomt." class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Bedanktekst (na versturen)</label>
                        <input type="text" name="proefrit_bedankt" maxlength="300" value="{{ $s['proefrit_bedankt'] ?? '' }}" placeholder="Bedankt! We nemen snel contact met je op." class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#374151] mb-1.5">Privacytekst (optioneel)</label>
                        <input type="text" name="proefrit_privacy_tekst" maxlength="300" value="{{ $s['proefrit_privacy_tekst'] ?? '' }}" placeholder="Je gegevens worden alleen gebruikt voor deze aanvraag." class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                    </div>

                    <div class="flex flex-wrap gap-4 pt-2">
                        @foreach(['proefrit_toon_datum'=>'Datumkeuze tonen','proefrit_toon_bericht'=>'Berichtveld tonen','proefrit_auto_verplicht'=>'Auto verplicht','proefrit_privacy'=>'Privacytekst tonen'] as $k=>$lbl)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="{{ $k }}" value="1" @checked($s[$k] ?? in_array($k,['proefrit_toon_datum','proefrit_toon_bericht'])) class="rounded border-[#215558]/20 text-eazy focus:ring-eazy">
                                <span class="text-[13px] text-[#215558] opacity-80">{{ $lbl }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-[#215558]/5 flex items-center gap-3">
                    <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition"><i class="fa-solid fa-floppy-disk text-xs"></i> Opslaan</button>
                    <span class="text-xs text-[#215558] opacity-40">Kleur en lettertype volgen je <a href="{{ route('widgets.theme') }}" class="hover:underline">Huisstijl</a>.</span>
                </div>
            </form>

            {{-- Insluitcode --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-5">
                <h3 class="text-sm font-black text-[#215558] mb-3">Insluitcode</h3>
                <p class="text-xs text-[#215558] opacity-60 mb-3 leading-relaxed">Aan een specifieke auto koppelen? Voeg <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">data-car-id="ID"</code> toe.</p>
                <div class="relative">
                    <pre class="bg-gray-900 text-green-400 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed" id="code">&lt;!-- EazyAutomotive Proefrit-widget --&gt;
&lt;div id="eazy-proefrit"&gt;&lt;/div&gt;
&lt;script
  src="{{ url('/embed/v1/proefrit.js') }}"
  data-api-key="{{ $company->api_key }}"
  data-base-url="{{ url('/') }}"
  defer&gt;
&lt;/script&gt;</pre>
                    <button type="button" onclick="copyCode('code','btn')" class="cursor-pointer absolute top-3 right-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 text-gray-300 rounded-lg text-xs hover:bg-gray-600 transition" id="btn">
                        <i class="fa-regular fa-copy"></i> Kopieer
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm text-[#215558] opacity-60">
                <i class="fa-solid fa-calendar-check text-eazy"></i>
                <span>Aanvragen verschijnen bij <a href="{{ route('proefritten') }}" class="text-eazy font-semibold hover:underline">Proefritten</a>.</span>
            </div>
        </div>
    </div>

    <script>
        function copyCode(preId, btnId) {
            const code = document.getElementById(preId).textContent.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
            navigator.clipboard.writeText(code);
            const btn = document.getElementById(btnId);
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Gekopieerd!';
            setTimeout(() => btn.innerHTML = '<i class="fa-regular fa-copy"></i> Kopieer', 2000);
        }
    </script>
</x-app-layout>
