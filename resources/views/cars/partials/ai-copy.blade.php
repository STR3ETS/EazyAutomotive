{{--
    AI-generator voor de verkoopteksten (titel, beschrijving, opties).
    Verwacht $facts: een associatieve array met de RDW-grondfeiten van deze auto
    (merk, model, bouwjaar, brandstof, kleur, carrosserie, vermogen, etc.).
    De live velden (kilometerstand, prijs, opties) en de accenten worden bij het
    genereren uit het formulier gelezen, zodat het werkt op zowel toevoegen als
    bewerken. Vult na afloop de velden #titel, #beschrijving en #extra_opties.
--}}
<div x-data="carCopyGenerator(@js($facts))" class="mb-6 rounded-2xl border border-eazy/30 bg-gradient-to-br from-eazy-50/70 to-white p-5">
    <div class="flex items-start gap-3 mb-3">
        <div class="w-9 h-9 rounded-xl bg-eazy/10 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-wand-magic-sparkles text-eazy text-sm"></i>
        </div>
        <div>
            <h4 class="text-sm font-bold text-[#215558]">Verkoopteksten met AI</h4>
            <p class="text-xs text-[#215558] opacity-60 mt-0.5">Maakt een pakkende titel, een complete en SEO-vriendelijke beschrijving en een opgeschoonde optielijst op basis van de gegevens hierboven. Je kunt alles daarna nog aanpassen.</p>
        </div>
    </div>

    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Extra accenten (optioneel)</label>
    <textarea x-model="accent" rows="2"
        placeholder="bijv. pas groot onderhoud gehad, tweede eigenaar, trekhaak af fabriek, nieuwe set winterbanden"
        class="block w-full px-4 py-2.5 rounded-2xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25 bg-white/70"></textarea>
    <p class="mt-1 text-[11px] text-[#215558] opacity-40">De AI verzint niets. Alleen wat je hier of hierboven invult wordt gebruikt.</p>

    <div class="mt-3 flex flex-wrap items-center gap-3">
        <button type="button" @click="generate()" :disabled="loading"
            class="cursor-pointer inline-flex items-center justify-center gap-2 px-5 h-[42px] bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark disabled:opacity-50 disabled:cursor-default transition whitespace-nowrap">
            <i class="fa-solid" :class="loading ? 'fa-spinner fa-spin' : 'fa-wand-magic-sparkles'"></i>
            <span x-text="loading ? 'Bezig met schrijven...' : 'Genereer verkoopteksten'"></span>
        </button>
        <p x-show="note" x-text="note" class="text-xs font-medium text-emerald-600"></p>
        <p x-show="error" x-text="error" class="text-xs font-medium text-red-600"></p>
    </div>
</div>

<script>
    function carCopyGenerator(baseFacts) {
        return {
            accent: '', loading: false, error: '', note: '',
            async generate() {
                this.error = ''; this.note = '';
                this.loading = true;
                try {
                    const facts = Object.assign({}, baseFacts);
                    const km = document.getElementById('kilometerstand');
                    const prijs = document.getElementById('prijs');
                    const opties = document.getElementById('extra_opties');
                    if (km && km.value.trim()) facts.kilometerstand = km.value.trim();
                    if (prijs && prijs.value.trim()) facts.prijs = prijs.value.trim();
                    if (opties && opties.value.trim()) facts.opties = opties.value.trim();
                    if (this.accent.trim()) facts.accent = this.accent.trim();

                    const res = await fetch('{{ route('cars.ai-copy') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify(facts),
                    });
                    const data = await res.json();
                    if (!res.ok) { this.error = data.error || 'Genereren mislukt. Probeer het opnieuw.'; return; }

                    const titel = document.getElementById('titel');
                    const beschrijving = document.getElementById('beschrijving');
                    const optiesEl = document.getElementById('extra_opties');
                    if (titel && data.titel) titel.value = data.titel;
                    if (beschrijving && typeof data.beschrijving === 'string') beschrijving.value = data.beschrijving;
                    if (optiesEl && Array.isArray(data.opties) && data.opties.length) optiesEl.value = data.opties.join(', ');

                    this.note = 'Klaar. Controleer de teksten en pas ze eventueel aan voordat je opslaat.';
                } catch (e) {
                    this.error = 'Verbinding mislukt. Probeer het opnieuw.';
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
