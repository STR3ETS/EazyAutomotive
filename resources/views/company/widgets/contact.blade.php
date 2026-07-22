<x-app-layout>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('widgets.index') }}" class="w-9 h-9 rounded-lg bg-white border border-[#215558]/10 flex items-center justify-center text-[#215558] hover:border-eazy transition"><i class="fa-solid fa-arrow-left text-sm"></i></a>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Contact- &amp; inruil-widget</h1>
                    <p class="text-sm text-[#215558] opacity-50">Een formulier voor contact, inruil of financiering. Inzendingen komen binnen bij Leads.</p>
                </div>
            </div>

            <div class="ctl-info mb-5 flex items-start gap-3 bg-[#215558]/[0.04] border border-[#215558]/10 text-[#215558] px-5 py-3.5 rounded-xl">
                <i class="fa-solid fa-palette text-lg mt-0.5 text-eazy"></i>
                <p class="text-sm opacity-80">Het uiterlijk (kleur, lettertype, hoeken en schaduw) neemt deze widget automatisch over van je <a href="{{ route('widgets.theme') }}" class="text-eazy font-semibold hover:underline">Huisstijl</a>, zodat alles bij elkaar past.</p>
            </div>

            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-5">
                <h3 class="text-sm font-black text-[#215558] mb-3">Insluitcode</h3>
                <p class="text-xs text-[#215558] opacity-60 mb-3 leading-relaxed">Kies het type met <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">data-type</code>: <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">contact</code>, <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">inruil</code> of <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">financiering</code>. Koppelen aan een auto? Voeg <code class="bg-gray-100 px-1 py-0.5 rounded text-[11px]">data-car-id="ID"</code> toe.</p>
                <div class="relative">
                    <pre class="bg-gray-900 text-green-400 rounded-xl p-4 text-xs overflow-x-auto leading-relaxed" id="code">&lt;!-- EazyAutomotive Contact-widget --&gt;
&lt;div id="eazy-lead"&gt;&lt;/div&gt;
&lt;script
  src="{{ url('/embed/v1/lead.js') }}"
  data-api-key="{{ $company->api_key }}"
  data-base-url="{{ url('/') }}"
  data-type="contact"
  defer&gt;
&lt;/script&gt;</pre>
                    <button type="button" onclick="copyCode('code','btn')" class="cursor-pointer absolute top-3 right-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-700 text-gray-300 rounded-lg text-xs hover:bg-gray-600 transition" id="btn">
                        <i class="fa-regular fa-copy"></i> Kopieer
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm text-[#215558] opacity-60">
                <i class="fa-solid fa-arrow-right-to-bracket text-eazy"></i>
                <span>Inzendingen verschijnen automatisch bij <a href="{{ route('leads.index') }}" class="text-eazy font-semibold hover:underline">Leads</a>.</span>
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
