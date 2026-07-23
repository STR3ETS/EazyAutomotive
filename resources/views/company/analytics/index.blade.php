<x-app-layout>
    @php
        $eur = fn ($c) => '€ ' . number_format(((int) $c) / 100, 0, ',', '.');
        $k = $kpis;

        $trendBadge = function (?int $t) {
            if ($t === null) return ['—', 'text-gray-400', 'bg-gray-50', 'fa-minus'];
            if ($t > 0) return ['+' . $t . '%', 'text-emerald-600', 'bg-emerald-50', 'fa-arrow-trend-up'];
            if ($t < 0) return [$t . '%', 'text-red-500', 'bg-red-50', 'fa-arrow-trend-down'];
            return ['0%', 'text-gray-400', 'bg-gray-50', 'fa-minus'];
        };

        $statusHex = ['emerald' => '#10b981', 'amber' => '#f59e0b', 'blue' => '#3b82f6', 'gray' => '#9ca3af'];
        $funnelHex = ['nieuw' => '#3b82f6', 'contact' => '#f59e0b', 'afspraak' => '#0F9B9F', 'gewonnen' => '#10b981', 'verloren' => '#ef4444'];
    @endphp

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-black text-[#215558]">Inzichten</h1>
                <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Hoe je voorraad, marketing en verkoop presteren</p>
            </div>

            {{-- KPI-kaarten --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                {{-- Actieve voorraad --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl bg-eazy-50 flex items-center justify-center"><i class="fa-solid fa-car text-eazy text-sm"></i></div>
                    </div>
                    <div class="text-2xl font-black text-[#215558]">{{ $k['active_cars'] }}</div>
                    <div class="text-xs text-[#215558] opacity-50 font-medium mt-0.5">Actieve voorraad</div>
                    <div class="text-xs text-[#215558] opacity-70 font-semibold mt-2">{{ $eur($k['stock_value']) }} <span class="opacity-50 font-medium">voorraadwaarde</span></div>
                </div>

                {{-- Weergaven 30d --}}
                @php [$vt, $vtc, $vtb, $vti] = $trendBadge($k['views_trend']); @endphp
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-eye text-blue-500 text-sm"></i></div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $vtc }} {{ $vtb }}"><i class="fa-solid {{ $vti }} text-[9px]"></i> {{ $vt }}</span>
                    </div>
                    <div class="text-2xl font-black text-[#215558]">{{ number_format($k['views_30'], 0, ',', '.') }}</div>
                    <div class="text-xs text-[#215558] opacity-50 font-medium mt-0.5">Weergaven (30 dagen)</div>
                    <div class="text-xs text-[#215558] opacity-50 font-medium mt-2">t.o.v. vorige 30 dagen</div>
                </div>

                {{-- Leads 30d --}}
                @php [$lt, $ltc, $ltb, $lti] = $trendBadge($k['leads_trend']); @endphp
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center"><i class="fa-solid fa-inbox text-indigo-500 text-sm"></i></div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $ltc }} {{ $ltb }}"><i class="fa-solid {{ $lti }} text-[9px]"></i> {{ $lt }}</span>
                    </div>
                    <div class="text-2xl font-black text-[#215558]">{{ $k['leads_30'] }}</div>
                    <div class="text-xs text-[#215558] opacity-50 font-medium mt-0.5">Nieuwe leads (30 dagen)</div>
                    <div class="text-xs text-[#215558] opacity-70 font-semibold mt-2">{{ $k['conversion'] === null ? '—' : $k['conversion'] . '%' }} <span class="opacity-50 font-medium">conversie</span></div>
                </div>

                {{-- Omzet dit jaar --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center"><i class="fa-solid fa-euro-sign text-emerald-500 text-sm"></i></div>
                    </div>
                    <div class="text-2xl font-black text-[#215558]">{{ $eur($k['revenue_year']) }}</div>
                    <div class="text-xs text-[#215558] opacity-50 font-medium mt-0.5">Omzet {{ now()->year }}</div>
                    <div class="text-xs text-amber-600 font-semibold mt-2">{{ $eur($k['outstanding']) }} <span class="text-[#215558] opacity-50 font-medium">openstaand</span></div>
                </div>
            </div>

            {{-- Secundaire stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-[#215558]/10 px-5 py-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0"><i class="fa-solid fa-handshake text-blue-500 text-sm"></i></div>
                    <div><div class="text-lg font-black text-[#215558] leading-none">{{ $k['sold_year'] }}</div><div class="text-xs text-[#215558] opacity-50 font-medium mt-1">Verkocht in {{ now()->year }}</div></div>
                </div>
                <div class="bg-white rounded-2xl border border-[#215558]/10 px-5 py-4 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0"><i class="fa-solid fa-calendar-check text-emerald-500 text-sm"></i></div>
                    <div><div class="text-lg font-black text-[#215558] leading-none">{{ $k['sold_month'] }}</div><div class="text-xs text-[#215558] opacity-50 font-medium mt-1">Verkocht deze maand</div></div>
                </div>
                <div class="bg-white rounded-2xl border border-[#215558]/10 px-5 py-4 flex items-center gap-3 col-span-2 sm:col-span-1">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center shrink-0"><i class="fa-solid fa-hourglass-half text-amber-500 text-sm"></i></div>
                    <div><div class="text-lg font-black text-[#215558] leading-none">{{ $k['avg_stock_age'] === null ? '—' : $k['avg_stock_age'] . ' dgn' }}</div><div class="text-xs text-[#215558] opacity-50 font-medium mt-1">Gem. standtijd voorraad</div></div>
                </div>
            </div>

            {{-- Grafieken: weergaven + omzet --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                {{-- Weergaven 30 dagen --}}
                @php $maxViews = max(1, collect($views_series)->max('value')); $anyViews = collect($views_series)->sum('value') > 0; @endphp
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-chart-column text-blue-500 text-sm"></i></div>
                        <div><h3 class="text-sm font-bold text-[#215558]">Weergaven per dag</h3><p class="text-xs text-[#215558] opacity-50">Laatste 30 dagen</p></div>
                    </div>
                    @if($anyViews)
                        <div class="flex items-end gap-[3px] h-36">
                            @foreach($views_series as $d)
                                <div class="flex-1 h-full flex items-end" title="{{ $d['label'] }}: {{ $d['value'] }} weergaven">
                                    <div class="w-full rounded-t transition-all hover:opacity-80" style="height: {{ $d['value'] > 0 ? max(3, round($d['value'] / $maxViews * 100)) : 1 }}%; background: #0F9B9F;"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between mt-2 text-[10px] text-[#215558] opacity-40 font-medium">
                            <span>{{ $views_series[0]['label'] }}</span>
                            <span>{{ $views_series[count($views_series) - 1]['label'] }}</span>
                        </div>
                    @else
                        <div class="h-36 flex items-center justify-center text-sm text-[#215558] opacity-40">Nog geen weergaven in deze periode</div>
                    @endif
                </div>

                {{-- Omzet per maand --}}
                @php $maxRev = max(1, collect($revenue_series)->max('value')); $anyRev = collect($revenue_series)->sum('value') > 0; @endphp
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center"><i class="fa-solid fa-chart-line text-emerald-500 text-sm"></i></div>
                        <div><h3 class="text-sm font-bold text-[#215558]">Omzet per maand</h3><p class="text-xs text-[#215558] opacity-50">Laatste 12 maanden</p></div>
                    </div>
                    @if($anyRev)
                        <div class="flex items-end gap-1.5 h-36">
                            @foreach($revenue_series as $m)
                                <div class="flex-1 h-full flex items-end" title="{{ $m['label'] }}: {{ $eur($m['value']) }}">
                                    <div class="w-full rounded-t transition-all hover:opacity-80" style="height: {{ $m['value'] > 0 ? max(3, round($m['value'] / $maxRev * 100)) : 1 }}%; background: #0F9B9F;"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between gap-1.5 mt-2">
                            @foreach($revenue_series as $m)
                                <div class="flex-1 text-center text-[9px] text-[#215558] opacity-40 font-medium">{{ $m['short'] }}</div>
                            @endforeach
                        </div>
                    @else
                        <div class="h-36 flex items-center justify-center text-sm text-[#215558] opacity-40">Nog geen definitieve facturen</div>
                    @endif
                </div>
            </div>

            {{-- Voorraad-samenstelling + top merken --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                {{-- Status-verdeling --}}
                @php $stockTotal = max(1, collect($stock_by_status)->sum('count')); @endphp
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-xl bg-eazy-50 flex items-center justify-center"><i class="fa-solid fa-layer-group text-eazy text-sm"></i></div>
                        <div><h3 class="text-sm font-bold text-[#215558]">Voorraad-samenstelling</h3><p class="text-xs text-[#215558] opacity-50">Auto's per status</p></div>
                    </div>
                    <div class="space-y-3">
                        @foreach($stock_by_status as $s)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-semibold text-[#215558]">{{ $s['label'] }}</span>
                                    <span class="text-[#215558] opacity-50 font-medium">{{ $s['count'] }}</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-[#215558]/5 overflow-hidden">
                                    <div class="h-full rounded-full" style="width: {{ round($s['count'] / $stockTotal * 100) }}%; background: {{ $statusHex[$s['color']] ?? '#9ca3af' }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Top merken --}}
                @php $maxBrand = max(1, collect($top_brands)->max('aantal') ?? 0); @endphp
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center"><i class="fa-solid fa-ranking-star text-violet-500 text-sm"></i></div>
                        <div><h3 class="text-sm font-bold text-[#215558]">Top merken</h3><p class="text-xs text-[#215558] opacity-50">In actieve voorraad</p></div>
                    </div>
                    @if(count($top_brands))
                        <div class="space-y-3">
                            @foreach($top_brands as $bnd)
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1">
                                        <span class="font-semibold text-[#215558]">{{ $bnd['merk'] }}</span>
                                        <span class="text-[#215558] opacity-50 font-medium">{{ $bnd['aantal'] }}</span>
                                    </div>
                                    <div class="h-2.5 rounded-full bg-[#215558]/5 overflow-hidden">
                                        <div class="h-full rounded-full" style="width: {{ round($bnd['aantal'] / $maxBrand * 100) }}%; background: #0F9B9F;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="h-24 flex items-center justify-center text-sm text-[#215558] opacity-40">Nog geen actieve auto's</div>
                    @endif
                </div>
            </div>

            {{-- Leads-trechter + top auto's --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Trechter --}}
                @php $maxStage = max(1, collect($funnel['stages'])->max('count') ?? 0); @endphp
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center"><i class="fa-solid fa-filter text-indigo-500 text-sm"></i></div>
                        <div><h3 class="text-sm font-bold text-[#215558]">Leads-trechter</h3><p class="text-xs text-[#215558] opacity-50">{{ $funnel['total'] }} leads totaal</p></div>
                    </div>
                    <div class="space-y-3">
                        @foreach($funnel['stages'] as $stage)
                            <div class="flex items-center gap-3">
                                <div class="w-24 flex items-center gap-2 shrink-0">
                                    <i class="fa-solid {{ $stage['icon'] }} text-[11px]" style="color: {{ $funnelHex[$stage['key']] ?? '#9ca3af' }};"></i>
                                    <span class="text-xs font-semibold text-[#215558]">{{ $stage['label'] }}</span>
                                </div>
                                <div class="flex-1 h-5 rounded-md bg-[#215558]/5 overflow-hidden">
                                    <div class="h-full rounded-md flex items-center justify-end px-2" style="width: {{ max($stage['count'] > 0 ? 8 : 0, round($stage['count'] / $maxStage * 100)) }}%; background: {{ $funnelHex[$stage['key']] ?? '#9ca3af' }};">
                                        @if($stage['count'] > 0)<span class="text-[10px] font-bold text-white">{{ $stage['count'] }}</span>@endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Top auto's --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center"><i class="fa-solid fa-fire text-amber-500 text-sm"></i></div>
                        <div><h3 class="text-sm font-bold text-[#215558]">Best bekeken auto's</h3><p class="text-xs text-[#215558] opacity-50">Meeste weergaven</p></div>
                    </div>
                    @if(count($top_cars))
                        <div class="space-y-1">
                            @foreach($top_cars as $i => $car)
                                <a href="{{ route('cars.show', $car['id']) }}" class="flex items-center gap-3 p-2 -mx-2 rounded-xl hover:bg-[#215558]/5 transition">
                                    <span class="w-6 h-6 rounded-lg bg-[#215558]/5 flex items-center justify-center text-xs font-bold text-[#215558] shrink-0">{{ $i + 1 }}</span>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-[#215558] truncate">{{ $car['titel'] }}</div>
                                        <div class="text-xs text-[#215558] opacity-50">{{ $car['prijs'] ? $eur($car['prijs']) : 'Prijs op aanvraag' }}</div>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs shrink-0">
                                        <span class="flex items-center gap-1 text-[#215558] opacity-60" title="Weergaven"><i class="fa-solid fa-eye text-[10px]"></i> {{ $car['views'] }}</span>
                                        <span class="flex items-center gap-1 text-[#215558] opacity-60" title="Leads"><i class="fa-solid fa-inbox text-[10px]"></i> {{ $car['leads'] }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="h-24 flex items-center justify-center text-sm text-[#215558] opacity-40">Nog geen actieve auto's</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
