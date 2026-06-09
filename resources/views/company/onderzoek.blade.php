<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-black text-[#215558]">Voertuigonderzoek</h1>
                <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Vraag een rapport op met specificaties, historie, milieu en een waardeschatting.</p>
            </div>

            {{-- Search form --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-8">
                <form method="GET" action="{{ route('onderzoek') }}" class="flex flex-col sm:flex-row items-stretch gap-3">
                    <div class="flex-1">
                        <label for="kenteken" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Kenteken</label>
                        <div class="flex items-stretch rounded-xl overflow-hidden shadow-sm ring-1 ring-[#215558]/10 max-w-xs">
                            <div class="w-8 bg-[#0a3fb0] flex flex-col items-center justify-center text-white shrink-0" aria-hidden="true">
                                <span class="text-[9px] leading-none">★</span>
                                <span class="text-[10px] font-black leading-none mt-0.5">NL</span>
                            </div>
                            <input id="kenteken" type="text" name="kenteken" value="{{ $kenteken }}" maxlength="10"
                                placeholder="XX-123-X" autocomplete="off"
                                style="text-transform:uppercase;letter-spacing:.08em"
                                class="flex-1 w-full bg-[#f4ce00] text-black text-xl font-black text-center uppercase border-0 focus:ring-0 focus:outline-none py-2.5">
                        </div>
                    </div>
                    <div class="sm:w-56">
                        <label for="km" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Kilometerstand <span class="opacity-50 normal-case font-medium">(optioneel)</span></label>
                        <input id="km" type="number" name="km" value="{{ $km }}" min="0" step="500" placeholder="bijv. 84500"
                            class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full sm:w-auto cursor-pointer inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition">
                            <i class="fa-solid fa-magnifying-glass-chart text-xs"></i> Rapport opvragen
                        </button>
                    </div>
                </form>
            </div>

            @if($kenteken && !$report)
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-circle-question text-red-400 text-2xl"></i>
                    </div>
                    <p class="text-[#215558] font-bold mb-1">Geen voertuig gevonden</p>
                    <p class="text-sm text-[#215558] opacity-50">We konden geen RDW-gegevens vinden voor kenteken <strong>{{ strtoupper($kenteken) }}</strong>. Controleer het kenteken en probeer het opnieuw.</p>
                </div>
            @elseif(!$report)
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-eazy-50 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-magnifying-glass-chart text-eazy text-2xl"></i>
                    </div>
                    <p class="text-[#215558] font-bold mb-1">Voer een kenteken in</p>
                    <p class="text-sm text-[#215558] opacity-50">Vul hierboven een kenteken in om een volledig voertuigrapport op te vragen.</p>
                </div>
            @else
                {{-- ============ REPORT ============ --}}

                {{-- Summary header --}}
                <div class="bg-gradient-to-br from-eazy-darker via-eazy-dark to-eazy-dark rounded-2xl p-6 text-white mb-6 relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-8 opacity-[0.08]" aria-hidden="true"><i class="fa-solid fa-car-side text-[150px]"></i></div>
                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="inline-flex items-stretch rounded-lg overflow-hidden shadow-md ring-2 ring-black/10 w-max">
                            <div class="w-7 bg-[#0a3fb0] flex flex-col items-center justify-center text-white shrink-0" aria-hidden="true">
                                <span class="text-[8px] leading-none">★</span>
                                <span class="text-[9px] font-black leading-none mt-0.5">NL</span>
                            </div>
                            <span class="bg-[#f4ce00] text-black text-xl font-black px-3 py-1.5 tracking-wider">{{ $report['kenteken'] }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-xl font-black">{{ $report['titel'] }}</h2>
                            <p class="text-eazy-100/80 text-xs font-medium">Rapport opgehaald op {{ $report['opgehaald_op'] }} via de RDW</p>
                        </div>
                    </div>
                </div>

                {{-- Attention points --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-6">
                    <h3 class="text-sm font-bold text-[#215558] mb-4 flex items-center gap-2"><i class="fa-solid fa-clipboard-check text-eazy"></i> Samenvatting</h3>
                    <div class="grid sm:grid-cols-2 gap-2.5">
                        @foreach($report['aandachtspunten'] as $point)
                            <div class="flex items-start gap-2.5 px-3 py-2.5 rounded-xl {{ $point['type'] === 'ok' ? 'bg-emerald-50' : 'bg-amber-50' }}">
                                <i class="fa-solid {{ $point['type'] === 'ok' ? 'fa-circle-check text-emerald-500' : 'fa-triangle-exclamation text-amber-500' }} text-sm mt-0.5"></i>
                                <span class="text-xs font-medium {{ $point['type'] === 'ok' ? 'text-emerald-700' : 'text-amber-700' }}">{{ $point['tekst'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Value estimate --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-6">
                    <h3 class="text-sm font-bold text-[#215558] mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-tags text-eazy"></i> Indicatieve waardeschatting
                        @if($report['waarde']['beschikbaar'] && ($report['waarde']['ruw'] ?? false))
                            <span class="ml-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-wide">Ruwe indicatie</span>
                        @endif
                    </h3>
                    @if($report['waarde']['beschikbaar'])
                        <div class="flex items-end justify-center gap-6 sm:gap-12 py-2">
                            <div class="text-center">
                                <div class="text-[10px] font-bold text-[#215558] opacity-40 uppercase tracking-wide mb-1">Vanaf</div>
                                <div class="text-lg font-bold text-[#215558] opacity-70">{{ $report['waarde']['onder'] }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-[10px] font-bold text-eazy uppercase tracking-wide mb-1">Schatting</div>
                                <div class="text-3xl font-black text-eazy">{{ $report['waarde']['midden'] }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-[10px] font-bold text-[#215558] opacity-40 uppercase tracking-wide mb-1">Tot</div>
                                <div class="text-lg font-bold text-[#215558] opacity-70">{{ $report['waarde']['boven'] }}</div>
                            </div>
                        </div>
                        <p class="text-center text-xs text-[#215558] opacity-50 mt-4 max-w-lg mx-auto">{{ $report['waarde']['toelichting'] }}</p>
                        @unless($report['waarde']['km_gebruikt'])
                            <p class="text-center text-xs text-eazy font-semibold mt-2"><i class="fa-solid fa-circle-info text-[10px]"></i> Vul een kilometerstand in voor een nauwkeurigere schatting.</p>
                        @endunless
                    @else
                        <p class="text-sm text-[#215558] opacity-50 text-center py-4">{{ $report['waarde']['toelichting'] }}</p>
                    @endif
                </div>

                {{-- Detail grids --}}
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    @foreach([
                        ['Kerngegevens', 'fa-circle-info', $report['kerngegevens']],
                        ['Motor & prestaties', 'fa-gauge-high', $report['motor']],
                        ['Afmetingen & gewicht', 'fa-ruler-combined', $report['afmetingen']],
                        ['Milieu & verbruik', 'fa-leaf', $report['milieu']],
                        ['Fiscaal', 'fa-euro-sign', $report['fiscaal']],
                    ] as [$title, $icon, $rows])
                        @if(!empty($rows))
                            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                                <h3 class="text-sm font-bold text-[#215558] mb-4 flex items-center gap-2"><i class="fa-solid {{ $icon }} text-eazy"></i> {{ $title }}</h3>
                                <dl class="divide-y divide-[#215558]/5">
                                    @foreach($rows as $label => $value)
                                        <div class="flex items-center justify-between gap-4 py-2">
                                            <dt class="text-xs text-[#215558] opacity-50 font-medium">{{ $label }}</dt>
                                            <dd class="text-sm font-semibold text-[#215558] text-right">{{ $value }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Status --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-6">
                    <h3 class="text-sm font-bold text-[#215558] mb-4 flex items-center gap-2"><i class="fa-solid fa-shield-check text-eazy"></i> Status &amp; registratie</h3>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach($report['status'] as $label => $item)
                            <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl bg-[#ebf2f2]/50">
                                <span class="text-xs font-semibold text-[#215558]">{{ $label }}</span>
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold {{ $item['ok'] ? 'text-emerald-600' : 'text-amber-600' }}">
                                    <i class="fa-solid {{ $item['ok'] ? 'fa-circle-check' : 'fa-circle-exclamation' }} text-xs"></i>
                                    {{ $item['value'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- APK defect history --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <h3 class="text-sm font-bold text-[#215558] mb-4 flex items-center gap-2"><i class="fa-solid fa-screwdriver-wrench text-eazy"></i> APK-gebrekenhistorie</h3>
                    @if(empty($report['gebreken']))
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                            <span class="text-sm font-medium text-emerald-700">Geen geconstateerde gebreken bekend bij de RDW.</span>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($report['gebreken'] as $visit)
                                <div class="relative pl-6 border-l-2 border-[#215558]/10">
                                    <span class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-amber-400 ring-2 ring-white"></span>
                                    <div class="text-xs font-bold text-[#215558] mb-2">Keuring {{ $visit['datum'] }}</div>
                                    <ul class="space-y-1.5">
                                        @foreach($visit['items'] as $item)
                                            <li class="flex items-start gap-2 text-xs text-[#215558] opacity-70">
                                                <i class="fa-solid fa-wrench text-amber-400 text-[10px] mt-0.5"></i>
                                                <span>{{ $item['omschrijving'] }}@if($item['aantal'] > 1) <span class="opacity-50">(x{{ $item['aantal'] }})</span>@endif</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <p class="text-center text-xs text-[#215558] opacity-30 mt-6">Gegevens afkomstig uit de open data van de RDW. De waardeschatting is een indicatie en geen officiële taxatie.</p>
            @endif

        </div>
    </div>
</x-app-layout>
