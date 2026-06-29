<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Boekhouding</h1>
                    <p class="text-sm text-[#215558] opacity-50">Financieel overzicht en BTW-aangifte per periode.</p>
                </div>
                <div class="flex items-end gap-2">
                    <form method="GET" class="flex items-end gap-2">
                        <select name="jaar" onchange="this.form.submit()" class="px-3 py-2 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            @foreach($years as $y)
                                <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                        <select name="kwartaal" onchange="this.form.submit()" class="px-3 py-2 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            @foreach([1 => 'Q1', 2 => 'Q2', 3 => 'Q3', 4 => 'Q4'] as $q => $lbl)
                                <option value="{{ $q }}" @selected($quarter !== 'jaar' && (int) $quarter === $q)>{{ $lbl }}</option>
                            @endforeach
                            <option value="jaar" @selected($quarter === 'jaar')>Heel jaar</option>
                        </select>
                    </form>
                    <a href="{{ route('bookkeeping.export', ['jaar' => $year, 'kwartaal' => $quarter]) }}" class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-[#215558]/15 text-[#215558] rounded-full text-sm font-semibold hover:border-eazy transition"><i class="fa-solid fa-file-csv text-xs"></i> Export CSV</a>
                </div>
            </div>

            {{-- KPI's --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @php
                    $kpis = [
                        ['Verkoop', \App\Models\Invoice::eur($verkoopTotaal), 'fa-arrow-trend-up', 'text-emerald-600'],
                        ['Inkoop & kosten', \App\Models\Invoice::eur($kostenIncl), 'fa-arrow-trend-down', 'text-amber-600'],
                        ['Resultaat', \App\Models\Invoice::eur($resultaat), 'fa-scale-balanced', $resultaat >= 0 ? 'text-emerald-600' : 'text-red-500'],
                        ['Openstaand', \App\Models\Invoice::eur($openstaand), 'fa-hourglass-half', 'text-[#215558]'],
                    ];
                @endphp
                @foreach($kpis as $k)
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                        <div class="flex items-center gap-2 text-[11px] font-bold text-[#215558] opacity-50 uppercase tracking-wider mb-1"><i class="fa-solid {{ $k[2] }} {{ $k[3] }}"></i> {{ $k[0] }}</div>
                        <div class="text-xl font-black text-[#215558]">{{ $k[1] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- BTW-aangifte --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                <h3 class="text-sm font-bold text-[#215558] mb-1">BTW-aangifte</h3>
                <p class="text-xs text-[#215558] opacity-50 mb-5">{{ $quarter === 'jaar' ? "Heel {$year}" : "Kwartaal {$quarter} van {$year}" }}. Indicatief overzicht; controleer altijd met je boekhouder.</p>

                <div class="divide-y divide-[#215558]/5 text-sm">
                    <div class="grid grid-cols-12 gap-2 py-2 text-[11px] font-bold text-[#215558] opacity-50 uppercase tracking-wider">
                        <div class="col-span-7">Rubriek</div>
                        <div class="col-span-3 text-right">Bedrag (excl.)</div>
                        <div class="col-span-2 text-right">BTW</div>
                    </div>
                    <div class="grid grid-cols-12 gap-2 py-2.5">
                        <div class="col-span-7 text-[#215558]">1a. Leveringen belast met hoog tarief (21%)</div>
                        <div class="col-span-3 text-right text-[#215558]">{{ \App\Models\Invoice::eur($omzetHoog) }}</div>
                        <div class="col-span-2 text-right text-[#215558]">{{ \App\Models\Invoice::eur($btwHoog) }}</div>
                    </div>
                    <div class="grid grid-cols-12 gap-2 py-2.5">
                        <div class="col-span-7 text-[#215558]">1b. Leveringen belast met laag tarief (9%)</div>
                        <div class="col-span-3 text-right text-[#215558]">{{ \App\Models\Invoice::eur($omzetLaag) }}</div>
                        <div class="col-span-2 text-right text-[#215558]">{{ \App\Models\Invoice::eur($btwLaag) }}</div>
                    </div>
                    @if($omzetNul > 0)
                        <div class="grid grid-cols-12 gap-2 py-2.5">
                            <div class="col-span-7 text-[#215558]">1e. Leveringen belast met 0% of vrijgesteld</div>
                            <div class="col-span-3 text-right text-[#215558]">{{ \App\Models\Invoice::eur($omzetNul) }}</div>
                            <div class="col-span-2 text-right text-[#215558] opacity-40">-</div>
                        </div>
                    @endif
                    <div class="grid grid-cols-12 gap-2 py-2.5 bg-[#ebf2f2]/40 -mx-2 px-2 rounded-lg">
                        <div class="col-span-7 text-[#215558]">Margeregeling (BTW over de marge, 21/121)</div>
                        <div class="col-span-3 text-right text-[#215558]">{{ \App\Models\Invoice::eur($margeGrondslag) }}</div>
                        <div class="col-span-2 text-right text-[#215558]">{{ \App\Models\Invoice::eur($margeBtw) }}</div>
                    </div>
                    <div class="grid grid-cols-12 gap-2 py-2.5 font-semibold">
                        <div class="col-span-7 text-[#215558]">5a. Verschuldigde omzetbelasting</div>
                        <div class="col-span-3"></div>
                        <div class="col-span-2 text-right text-[#215558]">{{ \App\Models\Invoice::eur($verschuldigd) }}</div>
                    </div>
                    <div class="grid grid-cols-12 gap-2 py-2.5">
                        <div class="col-span-7 text-[#215558]">5b. Voorbelasting (BTW op inkoop & kosten)</div>
                        <div class="col-span-3"></div>
                        <div class="col-span-2 text-right text-[#215558]">{{ \App\Models\Invoice::eur($voorbelasting) }}</div>
                    </div>
                    <div class="grid grid-cols-12 gap-2 py-3 border-t-2 border-[#215558]/10">
                        <div class="col-span-7 font-black text-[#215558]">{{ $saldo >= 0 ? 'Te betalen aan Belastingdienst' : 'Terug te vragen' }}</div>
                        <div class="col-span-3"></div>
                        <div class="col-span-2 text-right text-lg font-black {{ $saldo >= 0 ? 'text-[#215558]' : 'text-emerald-600' }}">{{ \App\Models\Invoice::eur(abs($saldo)) }}</div>
                    </div>
                </div>
            </div>

            <p class="text-[11px] text-[#215558] opacity-40 mt-4">Tip: gebruik "Export CSV" voor je boekhouder. Grootboek en journaalposten volgen in de volgende stap.</p>
        </div>
    </div>
</x-app-layout>
