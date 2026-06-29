<x-app-layout>
    @php
        $initLines = [];
        if ($invoice) {
            foreach ($invoice->lines as $l) {
                $initLines[] = [
                    'description' => $l->description,
                    'quantity' => (float) $l->quantity,
                    'unit_price' => round($l->unit_price / 100, 2),
                    'vat_rate' => (int) $l->vat_rate,
                    'purchase_price' => $l->purchase_price !== null ? round($l->purchase_price / 100, 2) : '',
                    'car_id' => $l->car_id ?? '',
                ];
            }
        } elseif ($prefill) {
            $initLines[] = ['description' => $prefill['description'], 'quantity' => 1, 'unit_price' => $prefill['unit_price'], 'vat_rate' => 21, 'purchase_price' => '', 'car_id' => $prefill['car_id']];
        }
        if (empty($initLines)) {
            $initLines[] = ['description' => '', 'quantity' => 1, 'unit_price' => 0, 'vat_rate' => 21, 'purchase_price' => '', 'car_id' => ''];
        }
        $carsJs = $cars->map(fn ($c) => ['id' => $c->id, 'label' => trim($c->display_title . ($c->kenteken ? ' (' . $c->kenteken . ')' : '')), 'price' => $c->prijs ? round($c->prijs / 100, 2) : 0])->values();
        $initData = [
            'vatScheme' => $invoice?->vat_scheme ?? 'marge',
            'pricesInclVat' => $invoice ? (bool) $invoice->prices_include_vat : true,
            'lines' => $initLines,
            'cars' => $carsJs,
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <a href="{{ $invoice ? route('invoices.show', $invoice) : route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#215558] opacity-50 hover:opacity-100 transition mb-3">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Terug
            </a>
            <h1 class="text-2xl font-black text-[#215558] mb-6">{{ $invoice ? 'Factuur bewerken' : 'Nieuwe factuur' }}</h1>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ $invoice ? route('invoices.update', $invoice) : route('invoices.store') }}" x-data="invoiceForm(@js($initData))" class="space-y-6">
                @csrf
                @if($invoice) @method('PUT') @endif

                {{-- Klant + datums --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Klant</label>
                        <select name="customer_id" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            <option value="">-- kies bestaande klant of vul hieronder een nieuwe in --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" @selected(($invoice?->customer_id ?? old('customer_id')) == $c->id)>{{ $c->label }}{{ $c->plaats ? ', ' . $c->plaats : '' }}</option>
                            @endforeach
                        </select>
                        <details class="mt-2 text-sm" x-data="{}">
                            <summary class="cursor-pointer text-eazy font-semibold text-xs">+ Nieuwe klant toevoegen</summary>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3 p-3 rounded-xl bg-eazy-50/40">
                                <select name="new_customer_type" class="px-3 py-2 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    <option value="particulier">Particulier</option>
                                    <option value="zakelijk">Zakelijk</option>
                                </select>
                                <input type="text" name="new_customer_naam" placeholder="Naam" class="px-3 py-2 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <input type="email" name="new_customer_email" placeholder="E-mail" class="px-3 py-2 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <input type="text" name="new_customer_adres" placeholder="Adres" class="px-3 py-2 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <input type="text" name="new_customer_postcode" placeholder="Postcode" class="px-3 py-2 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <input type="text" name="new_customer_plaats" placeholder="Plaats" class="px-3 py-2 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <input type="text" name="new_customer_btw" placeholder="BTW-nummer (zakelijk)" class="px-3 py-2 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy sm:col-span-2">
                            </div>
                        </details>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Factuurdatum</label>
                        <input type="date" name="date" value="{{ $invoice?->date?->format('Y-m-d') ?? $defaultDate }}" required class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Vervaldatum (optioneel)</label>
                        <input type="date" name="due_date" value="{{ optional($invoice?->due_date)->format('Y-m-d') }}" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                    </div>
                </div>

                {{-- BTW-regeling --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2">Regeling</label>
                    <div class="flex flex-wrap gap-2 mb-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="vat_scheme" value="marge" x-model="vatScheme" class="sr-only peer">
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold border peer-checked:bg-eazy peer-checked:text-white peer-checked:border-eazy border-[#215558]/15 text-[#215558]">Margeregeling (gebruikte auto)</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="vat_scheme" value="btw" x-model="vatScheme" class="sr-only peer">
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold border peer-checked:bg-eazy peer-checked:text-white peer-checked:border-eazy border-[#215558]/15 text-[#215558]">Normale BTW (21%)</span>
                        </label>
                    </div>
                    <p class="text-[11px] text-[#215558] opacity-50" x-show="vatScheme === 'marge'">Bij margeregeling wordt geen BTW apart vermeld. Vul per regel de inkoopprijs in voor je BTW-aangifte over de marge.</p>
                    <label class="flex items-center gap-2 mt-2 cursor-pointer" x-show="vatScheme === 'btw'">
                        <input type="checkbox" name="prices_include_vat" value="1" x-model="pricesInclVat" class="rounded border-[#215558]/20 text-eazy focus:ring-eazy">
                        <span class="text-xs text-[#215558]">Ingevoerde bedragen zijn inclusief BTW</span>
                    </label>
                </div>

                {{-- Regels --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-3">Regels</label>
                    <div class="space-y-3">
                        <template x-for="(line, i) in lines" :key="i">
                            <div class="rounded-xl border border-[#215558]/10 p-3 space-y-2">
                                <div class="flex gap-2">
                                    <input type="text" :name="`lines[${i}][description]`" x-model="line.description" placeholder="Omschrijving" required class="flex-1 px-3 py-2 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    <button type="button" @click="removeLine(i)" class="cursor-pointer w-9 h-9 shrink-0 text-red-400 hover:bg-red-50 rounded-lg transition"><i class="fa-solid fa-trash text-xs"></i></button>
                                </div>
                                <div class="flex flex-wrap gap-2 items-end">
                                    <div class="w-full sm:w-56">
                                        <span class="block text-[10px] text-[#215558] opacity-50 mb-1">Auto koppelen (optioneel)</span>
                                        <select :name="`lines[${i}][car_id]`" x-model="line.car_id" @change="onCar(line)" class="w-full px-2 py-1.5 rounded-lg border-[#215558]/10 text-xs focus:border-eazy focus:ring-eazy">
                                            <option value="">Geen</option>
                                            <template x-for="c in cars" :key="c.id"><option :value="c.id" x-text="c.label"></option></template>
                                        </select>
                                    </div>
                                    <div class="w-16">
                                        <span class="block text-[10px] text-[#215558] opacity-50 mb-1">Aantal</span>
                                        <input type="number" step="0.01" min="0.01" :name="`lines[${i}][quantity]`" x-model.number="line.quantity" class="w-full px-2 py-1.5 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    </div>
                                    <div class="w-28">
                                        <span class="block text-[10px] text-[#215558] opacity-50 mb-1" x-text="vatScheme === 'btw' && pricesInclVat ? 'Prijs incl.' : 'Prijs'"></span>
                                        <input type="number" step="0.01" :name="`lines[${i}][unit_price]`" x-model.number="line.unit_price" class="w-full px-2 py-1.5 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    </div>
                                    <div class="w-20" x-show="vatScheme === 'btw'">
                                        <span class="block text-[10px] text-[#215558] opacity-50 mb-1">BTW</span>
                                        <select :name="`lines[${i}][vat_rate]`" x-model.number="line.vat_rate" class="w-full px-2 py-1.5 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                            <option :value="21">21%</option>
                                            <option :value="9">9%</option>
                                            <option :value="0">0%</option>
                                        </select>
                                    </div>
                                    <div class="w-28" x-show="vatScheme === 'marge'">
                                        <span class="block text-[10px] text-[#215558] opacity-50 mb-1">Inkoopprijs</span>
                                        <input type="number" step="0.01" :name="`lines[${i}][purchase_price]`" x-model="line.purchase_price" placeholder="0,00" class="w-full px-2 py-1.5 rounded-lg border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    </div>
                                    <div class="ml-auto text-right">
                                        <span class="block text-[10px] text-[#215558] opacity-50 mb-1">Regeltotaal</span>
                                        <span class="text-sm font-bold text-[#215558]" x-text="eur(lineGross(line))"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="addLine()" class="cursor-pointer mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-eazy hover:underline"><i class="fa-solid fa-plus text-xs"></i> Regel toevoegen</button>
                </div>

                {{-- Totalen + notities --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Notitie (op factuur)</label>
                        <textarea name="notes" rows="4" maxlength="2000" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy resize-none">{{ $invoice?->notes ?? old('notes') }}</textarea>
                    </div>
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between" x-show="vatScheme === 'btw'"><span class="text-[#215558] opacity-60">Subtotaal</span><span class="font-semibold text-[#215558]" x-text="eur(subtotal)"></span></div>
                            <div class="flex justify-between" x-show="vatScheme === 'btw'"><span class="text-[#215558] opacity-60">BTW</span><span class="font-semibold text-[#215558]" x-text="eur(vatTotal)"></span></div>
                            <div class="flex justify-between pt-2 border-t border-[#215558]/10"><span class="font-bold text-[#215558]">Totaal</span><span class="text-lg font-black text-[#215558]" x-text="eur(total)"></span></div>
                            <p class="text-[11px] text-[#215558] opacity-50 pt-1" x-show="vatScheme === 'marge'">Margeregeling: geen BTW apart op de factuur.</p>
                        </div>
                        <button type="submit" class="cursor-pointer w-full mt-4 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition">
                            <i class="fa-solid fa-floppy-disk text-xs"></i> {{ $invoice ? 'Wijzigingen opslaan' : 'Opslaan als concept' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function invoiceForm(initial) {
            return {
                vatScheme: initial.vatScheme,
                pricesInclVat: initial.pricesInclVat,
                cars: initial.cars,
                lines: initial.lines.map(l => ({ ...l })),
                addLine() { this.lines.push({ description: '', quantity: 1, unit_price: 0, vat_rate: 21, purchase_price: '', car_id: '' }); },
                removeLine(i) { this.lines.splice(i, 1); if (!this.lines.length) this.addLine(); },
                onCar(line) {
                    const c = this.cars.find(x => String(x.id) === String(line.car_id));
                    if (c) { line.description = c.label; line.unit_price = c.price; }
                },
                lineNet(l) {
                    const base = (parseFloat(l.unit_price) || 0) * (parseFloat(l.quantity) || 0);
                    if (this.vatScheme === 'marge') return base;
                    return this.pricesInclVat ? base / (1 + (+l.vat_rate) / 100) : base;
                },
                lineVat(l) {
                    if (this.vatScheme === 'marge') return 0;
                    return this.lineNet(l) * (+l.vat_rate) / 100;
                },
                lineGross(l) {
                    if (this.vatScheme === 'marge') return (parseFloat(l.unit_price) || 0) * (parseFloat(l.quantity) || 0);
                    return this.lineNet(l) + this.lineVat(l);
                },
                get subtotal() { return this.lines.reduce((s, l) => s + this.lineNet(l), 0); },
                get vatTotal() { return this.lines.reduce((s, l) => s + this.lineVat(l), 0); },
                get total() { return this.lines.reduce((s, l) => s + this.lineGross(l), 0); },
                eur(v) { return '€ ' + (v || 0).toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
            };
        }
    </script>
</x-app-layout>
