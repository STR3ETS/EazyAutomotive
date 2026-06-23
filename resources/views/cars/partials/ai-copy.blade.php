{{--
    AI-generator voor de verkoopteksten (titel, beschrijving, opties).
    Verwacht $facts: een associatieve array met de RDW-grondfeiten van deze auto
    (merk, model, bouwjaar, brandstof, kleur, carrosserie, vermogen, etc.).
    De live velden (kilometerstand, prijs, opties) en de accenten worden bij het
    genereren uit het formulier gelezen, zodat het werkt op zowel toevoegen als
    bewerken. Vult na afloop de velden #titel, #beschrijving en #extra_opties.

    Premium AI moment: een dunne accent-gradient bovenin, een gradient-icoontegel
    en rustige typografie. Tik-om-toe-te-voegen accent-chips, een skeleton tijdens
    het laden, een korte flash op de gevulde velden na succes, plus knoppen om
    opnieuw te genereren of de vorige teksten terug te zetten. Alle beweging
    respecteert prefers-reduced-motion. AA gecontroleerd.
--}}
<div
    x-data="carCopyGenerator(@js($facts))"
    class="ai-copy-panel relative mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-eazy-50 via-white to-eazy-50/40 ring-1 ring-eazy-darker/10 shadow-sm"
>
    {{-- Dunne accentlijn bovenaan voor een premium afwerking --}}
    <div aria-hidden="true" class="h-1 w-full bg-gradient-to-r from-eazy via-eazy-dark to-eazy-darker"></div>

    {{-- Decoratieve gloed in de hoek, puur sfeer --}}
    <div aria-hidden="true" class="pointer-events-none absolute -top-12 -right-16 h-44 w-44 rounded-full bg-eazy/10 blur-3xl"></div>

    <div class="relative p-5 sm:p-6">
        {{-- Kop --}}
        <div class="flex items-start gap-3.5">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-eazy to-eazy-dark text-white shadow-sm">
                <svg class="h-5 w-5" viewBox="0 0 576 512" fill="currentColor" aria-hidden="true"><path d="M263.4-27L278.2 9.8 315 24.6c3 1.2 5 4.2 5 7.4s-2 6.2-5 7.4L278.2 54.2 263.4 91c-1.2 3-4.2 5-7.4 5s-6.2-2-7.4-5L233.8 54.2 197 39.4c-3-1.2-5-4.2-5-7.4s2-6.2 5-7.4L233.8 9.8 248.6-27c1.2-3 4.2-5 7.4-5s6.2 2 7.4 5zM110.7 41.7l21.5 50.1 50.1 21.5c5.9 2.5 9.7 8.3 9.7 14.7s-3.8 12.2-9.7 14.7l-50.1 21.5-21.5 50.1c-2.5 5.9-8.3 9.7-14.7 9.7s-12.2-3.8-14.7-9.7L59.8 164.2 9.7 142.7C3.8 140.2 0 134.4 0 128s3.8-12.2 9.7-14.7L59.8 91.8 81.3 41.7C83.8 35.8 89.6 32 96 32s12.2 3.8 14.7 9.7zM464 304c6.4 0 12.2 3.8 14.7 9.7l21.5 50.1 50.1 21.5c5.9 2.5 9.7 8.3 9.7 14.7s-3.8 12.2-9.7 14.7l-50.1 21.5-21.5 50.1c-2.5 5.9-8.3 9.7-14.7 9.7s-12.2-3.8-14.7-9.7l-21.5-50.1-50.1-21.5c-5.9-2.5-9.7-8.3-9.7-14.7s3.8-12.2 9.7-14.7l50.1-21.5 21.5-50.1c2.5-5.9 8.3-9.7 14.7-9.7zM460 0c11 0 21.6 4.4 29.5 12.2l42.3 42.3C539.6 62.4 544 73 544 84s-4.4 21.6-12.2 29.5l-88.2 88.2-101.3-101.3 88.2-88.2C438.4 4.4 449 0 460 0zM44.2 398.5L308.4 134.3 409.7 235.6 145.5 499.8C137.6 507.6 127 512 116 512s-21.6-4.4-29.5-12.2L44.2 457.5C36.4 449.6 32 439 32 428s4.4-21.6 12.2-29.5z"/></svg>
            </div>
            <div class="min-w-0">
                <h4 class="text-base font-bold tracking-tight text-eazy-darker">Verkoopteksten met AI</h4>
                <p class="mt-1 text-sm leading-relaxed text-muted">Schrijft in een klik een pakkende titel, een complete en SEO-vriendelijke beschrijving en een opgeschoonde optielijst op basis van de gegevens hierboven. Je past alles daarna gewoon zelf nog aan.</p>
            </div>
        </div>

        {{-- Accenten --}}
        <div class="mt-5">
            <div class="flex items-center justify-between gap-2">
                <label for="ai-accent" class="block text-[11px] font-bold uppercase tracking-wider text-eazy-dark">Extra accenten <span class="font-medium normal-case tracking-normal text-muted">(optioneel)</span></label>
                <span class="hidden text-[11px] font-medium text-muted sm:inline">Tik een kenmerk aan om het toe te voegen</span>
            </div>

            {{-- Snelkeuze-chips: voegen een verkoopargument toe aan het accentveld --}}
            <div class="mt-2 flex flex-wrap gap-1.5">
                <template x-for="chip in chips" :key="chip">
                    <button
                        type="button"
                        @click="toggleChip(chip)"
                        :aria-pressed="hasChip(chip) ? 'true' : 'false'"
                        :aria-label="(hasChip(chip) ? 'Verwijder accent: ' : 'Voeg accent toe: ') + chip"
                        class="group inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-eazy-dark focus-visible:ring-offset-1 focus-visible:ring-offset-white"
                        :class="hasChip(chip)
                            ? 'border-eazy-dark bg-eazy-dark text-white shadow-sm'
                            : 'border-eazy-darker/15 bg-white text-eazy-dark hover:border-eazy-dark hover:bg-eazy-50'"
                    >
                        <svg class="h-3 w-3 transition-transform motion-safe:group-hover:rotate-90" :class="hasChip(chip) ? 'rotate-45' : ''" viewBox="0 0 448 512" fill="currentColor" aria-hidden="true"><path d="M256 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 160-160 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l160 0 0 160c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160 160 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-160 0 0-160z"/></svg>
                        <span x-text="chip"></span>
                    </button>
                </template>
            </div>

            <textarea
                id="ai-accent"
                x-model="accent"
                rows="2"
                placeholder="bijv. pas groot onderhoud gehad, tweede eigenaar, trekhaak af fabriek, nieuwe set winterbanden"
                class="mt-2 block w-full rounded-xl border border-eazy-darker/15 bg-white px-4 py-2.5 text-sm text-eazy-darker placeholder:text-muted focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark focus:outline-none"
            ></textarea>

            <p class="mt-2 flex items-start gap-1.5 text-xs leading-snug text-muted">
                <svg class="mt-px h-3.5 w-3.5 shrink-0 text-eazy-dark" viewBox="0 0 384 512" fill="currentColor" aria-hidden="true"><path d="M292.9 384c7.3-22.3 21.9-42.5 38.4-59.9 32.7-34.4 52.7-80.9 52.7-132.1 0-106-86-192-192-192S0 86 0 192c0 51.2 20 97.7 52.7 132.1 16.5 17.4 31.2 37.6 38.4 59.9l201.7 0zM288 432l-192 0 0 16c0 44.2 35.8 80 80 80l32 0c44.2 0 80-35.8 80-80l0-16zM184 112c-39.8 0-72 32.2-72 72 0 13.3-10.7 24-24 24s-24-10.7-24-24c0-66.3 53.7-120 120-120 13.3 0 24 10.7 24 24s-10.7 24-24 24z"/></svg>
                <span>De AI verzint niets. Alleen wat je hier of hierboven invult wordt gebruikt.</span>
            </p>
        </div>

        {{-- Actiebalk --}}
        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            {{-- Hoofd-CTA --}}
            <button
                type="button"
                @click="generate()"
                :disabled="loading"
                class="ai-cta btn-shine inline-flex h-12 w-full items-center justify-center gap-2.5 rounded-full bg-eazy-dark px-6 text-sm font-bold text-white shadow-sm transition hover:bg-eazy-darker focus:outline-none focus-visible:ring-2 focus-visible:ring-eazy-dark focus-visible:ring-offset-2 focus-visible:ring-offset-white disabled:cursor-default disabled:bg-eazy-darker sm:w-auto"
            >
                <template x-if="!loading">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 576 512" fill="currentColor" aria-hidden="true"><path d="M263.4-27L278.2 9.8 315 24.6c3 1.2 5 4.2 5 7.4s-2 6.2-5 7.4L278.2 54.2 263.4 91c-1.2 3-4.2 5-7.4 5s-6.2-2-7.4-5L233.8 54.2 197 39.4c-3-1.2-5-4.2-5-7.4s2-6.2 5-7.4L233.8 9.8 248.6-27c1.2-3 4.2-5 7.4-5s6.2 2 7.4 5zM110.7 41.7l21.5 50.1 50.1 21.5c5.9 2.5 9.7 8.3 9.7 14.7s-3.8 12.2-9.7 14.7l-50.1 21.5-21.5 50.1c-2.5 5.9-8.3 9.7-14.7 9.7s-12.2-3.8-14.7-9.7L59.8 164.2 9.7 142.7C3.8 140.2 0 134.4 0 128s3.8-12.2 9.7-14.7L59.8 91.8 81.3 41.7C83.8 35.8 89.6 32 96 32s12.2 3.8 14.7 9.7zM464 304c6.4 0 12.2 3.8 14.7 9.7l21.5 50.1 50.1 21.5c5.9 2.5 9.7 8.3 9.7 14.7s-3.8 12.2-9.7 14.7l-50.1 21.5-21.5 50.1c-2.5 5.9-8.3 9.7-14.7 9.7s-12.2-3.8-14.7-9.7l-21.5-50.1-50.1-21.5c-5.9-2.5-9.7-8.3-9.7-14.7s3.8-12.2 9.7-14.7l50.1-21.5 21.5-50.1c2.5-5.9 8.3-9.7 14.7-9.7zM460 0c11 0 21.6 4.4 29.5 12.2l42.3 42.3C539.6 62.4 544 73 544 84s-4.4 21.6-12.2 29.5l-88.2 88.2-101.3-101.3 88.2-88.2C438.4 4.4 449 0 460 0zM44.2 398.5L308.4 134.3 409.7 235.6 145.5 499.8C137.6 507.6 127 512 116 512s-21.6-4.4-29.5-12.2L44.2 457.5C36.4 449.6 32 439 32 428s4.4-21.6 12.2-29.5z"/></svg>
                </template>
                <template x-if="loading">
                    <svg class="h-4.5 w-4.5 motion-safe:animate-spin" viewBox="0 0 512 512" fill="currentColor" aria-hidden="true"><path d="M208 48a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm0 416a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zM48 208a48 48 0 1 1 0 96 48 48 0 1 1 0-96zm368 48a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zM75 369.1A48 48 0 1 1 142.9 437 48 48 0 1 1 75 369.1zM75 75A48 48 0 1 1 142.9 142.9 48 48 0 1 1 75 75zM437 369.1A48 48 0 1 1 369.1 437 48 48 0 1 1 437 369.1z"/></svg>
                </template>
                <span x-text="loading ? 'Bezig met schrijven...' : (hasGenerated ? 'Genereer opnieuw' : 'Genereer verkoopteksten')"></span>
            </button>

            {{-- Opnieuw en ongedaan maken, alleen na een eerste resultaat --}}
            <div x-show="hasGenerated && !loading" x-cloak class="flex items-center gap-2">
                <button
                    type="button"
                    @click="generate()"
                    aria-label="Genereer opnieuw"
                    class="inline-flex h-12 items-center gap-2 rounded-full border border-eazy-darker/15 bg-white px-4 text-sm font-semibold text-eazy-dark transition hover:border-eazy-dark hover:bg-eazy-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-eazy-dark focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                >
                    <svg class="h-4 w-4" viewBox="0 0 512 512" fill="currentColor" aria-hidden="true"><path d="M65.9 228.5c13.3-93 93.4-164.5 190.1-164.5 53 0 101 21.5 135.8 56.2 .2 .2 .4 .4 .6 .6l7.6 7.2-47.9 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-128c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 53.4-11.3-10.7C390.5 28.6 326.5 0 256 0 127 0 20.3 95.4 2.6 219.5 .1 237 12.2 253.2 29.7 255.7s33.7-9.7 36.2-27.1zm443.5 64c2.5-17.5-9.7-33.7-27.1-36.2s-33.7 9.7-36.2 27.1c-13.3 93-93.4 164.5-190.1 164.5-53 0-101-21.5-135.8-56.2-.2-.2-.4-.4-.6-.6l-7.6-7.2 47.9 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L32 320c-8.5 0-16.7 3.4-22.7 9.5S-.1 343.7 0 352.3l1 127c.1 17.7 14.6 31.9 32.3 31.7S65.2 496.4 65 478.7l-.4-51.5 10.7 10.1c46.3 46.1 110.2 74.7 180.7 74.7 129 0 235.7-95.4 253.4-219.5z"/></svg>
                    <span class="hidden sm:inline">Opnieuw</span>
                </button>
                <button
                    type="button"
                    x-show="canUndo"
                    @click="undo()"
                    aria-label="Wijziging ongedaan maken"
                    class="inline-flex h-12 items-center gap-2 rounded-full border border-eazy-darker/15 bg-white px-4 text-sm font-semibold text-muted transition hover:border-eazy-dark hover:bg-eazy-50 hover:text-eazy-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-eazy-dark focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                >
                    <svg class="h-4 w-4" viewBox="0 0 512 512" fill="currentColor" aria-hidden="true"><path d="M256 64c-56.8 0-107.9 24.7-143.1 64l47.1 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 192c-17.7 0-32-14.3-32-32L0 32C0 14.3 14.3 0 32 0S64 14.3 64 32l0 54.7C110.9 33.6 179.5 0 256 0 397.4 0 512 114.6 512 256S397.4 512 256 512c-87 0-163.9-43.4-210.1-109.7-10.1-14.5-6.6-34.4 7.9-44.6s34.4-6.6 44.6 7.9c34.8 49.8 92.4 82.3 157.6 82.3 106 0 192-86 192-192S362 64 256 64z"/></svg>
                    <span class="hidden sm:inline">Ongedaan maken</span>
                </button>
            </div>
        </div>

        {{-- Skeleton tijdens het laden --}}
        <div x-show="loading" x-cloak class="mt-4 space-y-2.5" aria-hidden="true">
            <div class="ai-skel h-4 w-2/3 rounded-full"></div>
            <div class="ai-skel h-3 w-full rounded-full"></div>
            <div class="ai-skel h-3 w-full rounded-full"></div>
            <div class="ai-skel h-3 w-5/6 rounded-full"></div>
        </div>

        {{-- Statusregio: laadmelding en succes (polite) --}}
        <div aria-live="polite">
            {{-- Onzichtbare statusmelding zodat schermlezers het laden horen --}}
            <span class="sr-only" x-text="loading ? 'Bezig met het schrijven van de verkoopteksten' : ''"></span>

            <div
                x-show="note"
                x-cloak
                x-transition.opacity
                class="mt-4 flex items-start gap-2.5 rounded-xl bg-eazy-50 p-3.5 text-sm ring-1 ring-eazy-dark/15"
            >
                <svg class="mt-px h-5 w-5 shrink-0 text-eazy-dark" viewBox="0 0 512 512" fill="currentColor" aria-hidden="true"><path d="M256 512a256 256 0 1 1 0-512 256 256 0 1 1 0 512zM374 145.7c-10.7-7.8-25.7-5.4-33.5 5.3L221.1 315.2 169 263.1c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l72 72c5 5 11.8 7.5 18.8 7s13.4-4.1 17.5-9.8L379.3 179.2c7.8-10.7 5.4-25.7-5.3-33.5z"/></svg>
                <div>
                    <p class="font-semibold text-eazy-darker">Klaar. <span class="font-hand text-base font-normal text-eazy-dark" x-text="noteLead"></span></p>
                    <p class="mt-0.5 text-muted" x-text="note"></p>
                </div>
            </div>
        </div>

        {{-- Foutregio (assertive) --}}
        <div aria-live="assertive">
            <div
                x-show="error"
                x-cloak
                x-transition.opacity
                role="alert"
                class="mt-4 flex items-start gap-2.5 rounded-xl bg-red-50 p-3.5 text-sm ring-1 ring-red-600/20"
            >
                <svg class="mt-px h-5 w-5 shrink-0 text-red-600" viewBox="0 0 512 512" fill="currentColor" aria-hidden="true"><path d="M256 0c14.7 0 28.2 8.1 35.2 21l216 400c6.7 12.4 6.4 27.4-.8 39.5S486.1 480 472 480L40 480c-14.1 0-27.2-7.4-34.4-19.5s-7.5-27.1-.8-39.5l216-400c7-12.9 20.5-21 35.2-21zm0 352a32 32 0 1 0 0 64 32 32 0 1 0 0-64zm0-192c-18.2 0-32.7 15.5-31.4 33.7l7.4 104c.9 12.5 11.4 22.3 23.9 22.3 12.6 0 23-9.7 23.9-22.3l7.4-104c1.3-18.2-13.1-33.7-31.4-33.7z"/></svg>
                <p class="font-medium text-red-700" x-text="error"></p>
            </div>
        </div>
    </div>
</div>

{{-- Scoped stijl: shine op de CTA, shimmer-skeleton en een korte flash op de gevulde velden. Alle beweging valt weg bij prefers-reduced-motion. --}}
<style>
    .ai-copy-panel .btn-shine { position: relative; overflow: hidden; }
    .ai-copy-panel .btn-shine::after {
        content: ''; position: absolute; top: 0; left: -120%; width: 60%; height: 100%;
        background: linear-gradient(110deg, transparent, rgba(255, 255, 255, .35), transparent);
        transform: skewX(-20deg); transition: left .7s ease;
    }
    .ai-copy-panel .btn-shine:hover::after { left: 130%; }

    .ai-copy-panel .ai-skel {
        background: linear-gradient(100deg, #D1F2F3 30%, #F0FAFA 50%, #D1F2F3 70%);
        background-size: 200% 100%;
        animation: aiSkel 1.3s ease-in-out infinite;
    }
    @keyframes aiSkel { from { background-position: 200% 0; } to { background-position: -200% 0; } }

    .ai-field-flash { animation: aiFlash 1.6s cubic-bezier(.22, 1, .36, 1); }
    @keyframes aiFlash {
        0%   { box-shadow: 0 0 0 0 rgba(15, 155, 159, 0); border-color: rgba(15, 155, 159, .9); }
        15%  { box-shadow: 0 0 0 4px rgba(15, 155, 159, .22); border-color: rgba(15, 155, 159, .9); }
        100% { box-shadow: 0 0 0 0 rgba(15, 155, 159, 0); }
    }

    @media (prefers-reduced-motion: reduce) {
        .ai-copy-panel .btn-shine::after { transition: none; }
        .ai-copy-panel .ai-skel { animation: none; background: #D1F2F3; }
        .ai-field-flash { animation: none; }
    }
</style>

<script>
    function carCopyGenerator(baseFacts) {
        return {
            accent: '',
            loading: false,
            error: '',
            note: '',
            noteLead: '',
            hasGenerated: false,
            canUndo: false,
            previous: null,
            chips: [
                'Eerste eigenaar',
                'Onderhoudshistorie aanwezig',
                'Nieuwe APK',
                'Trekhaak',
                'Cruise control',
                'Dealer onderhouden',
                'Rookvrij',
            ],

            // Bepaalt of een chip al in het accentveld staat.
            hasChip(chip) {
                const parts = this.accent.split(',').map(p => p.trim().toLowerCase());
                return parts.includes(chip.toLowerCase());
            },

            // Voegt een chip toe of haalt hem weer weg.
            toggleChip(chip) {
                let parts = this.accent.split(',').map(p => p.trim()).filter(p => p.length);
                const idx = parts.findIndex(p => p.toLowerCase() === chip.toLowerCase());
                if (idx === -1) {
                    parts.push(chip);
                } else {
                    parts.splice(idx, 1);
                }
                this.accent = parts.join(', ');
            },

            // Korte highlight op een veld zodat de gebruiker ziet wat er veranderde.
            flash(el) {
                if (!el) return;
                el.classList.remove('ai-field-flash');
                void el.offsetWidth; // forceer herstart van de animatie
                el.classList.add('ai-field-flash');
                setTimeout(() => el.classList.remove('ai-field-flash'), 1700);
            },

            // Zet de eerdere titel, beschrijving en opties terug.
            undo() {
                if (!this.previous) return;
                const titel = document.getElementById('titel');
                const beschrijving = document.getElementById('beschrijving');
                const optiesEl = document.getElementById('extra_opties');
                if (titel) titel.value = this.previous.titel;
                if (beschrijving) beschrijving.value = this.previous.beschrijving;
                if (optiesEl) optiesEl.value = this.previous.opties;
                this.flash(titel); this.flash(beschrijving); this.flash(optiesEl);
                this.previous = null;
                this.canUndo = false;
                this.noteLead = 'de vorige teksten staan er weer';
                this.note = 'Je kunt opnieuw genereren wanneer je wilt.';
                this.error = '';
            },

            async generate() {
                this.error = '';
                this.note = '';
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

                    // Bewaar de huidige waarden zodat we ze kunnen terugzetten.
                    this.previous = {
                        titel: titel ? titel.value : '',
                        beschrijving: beschrijving ? beschrijving.value : '',
                        opties: optiesEl ? optiesEl.value : '',
                    };

                    if (titel && data.titel) { titel.value = data.titel; this.flash(titel); }
                    if (beschrijving && typeof data.beschrijving === 'string') { beschrijving.value = data.beschrijving; this.flash(beschrijving); }
                    if (optiesEl && Array.isArray(data.opties) && data.opties.length) { optiesEl.value = data.opties.join(', '); this.flash(optiesEl); }

                    this.hasGenerated = true;
                    this.canUndo = true;
                    this.noteLead = 'de velden zijn ingevuld';
                    this.note = 'Controleer de teksten en pas ze eventueel aan voordat je opslaat.';
                } catch (e) {
                    this.error = 'Verbinding mislukt. Probeer het opnieuw.';
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
