<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Proefritten</h1>
                    <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Aanvragen die via de proefrit-widget op je website binnenkomen.</p>
                </div>
                <a href="{{ route('integratie') }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-[#215558] text-white rounded-full text-sm font-bold hover:bg-eazy-darker transition">
                    <i class="fa-solid fa-code text-xs"></i> Widget-code
                </a>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach([
                    ['label' => 'Nieuw', 'key' => 'nieuw', 'icon' => 'fa-circle-dot', 'bg' => 'bg-blue-50', 'color' => 'text-blue-500'],
                    ['label' => 'Ingepland', 'key' => 'ingepland', 'icon' => 'fa-calendar-check', 'bg' => 'bg-eazy-50', 'color' => 'text-eazy-dark'],
                    ['label' => 'Afgerond', 'key' => 'afgerond', 'icon' => 'fa-circle-check', 'bg' => 'bg-emerald-50', 'color' => 'text-emerald-500'],
                    ['label' => 'Totaal', 'key' => 'totaal', 'icon' => 'fa-inbox', 'bg' => 'bg-gray-100', 'color' => 'text-gray-500'],
                ] as $stat)
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl {{ $stat['bg'] }} flex items-center justify-center">
                                <i class="fa-solid {{ $stat['icon'] }} {{ $stat['color'] }} text-sm"></i>
                            </div>
                            <div>
                                <div class="text-xl font-black text-[#215558]">{{ $stats[$stat['key']] }}</div>
                                <div class="text-[11px] text-[#215558] opacity-50 font-medium">{{ $stat['label'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Status filter --}}
            <div class="flex flex-wrap gap-1.5 mb-5">
                @php $current = request('status'); @endphp
                <a href="{{ route('proefritten') }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition {{ !$current ? 'bg-eazy text-white' : 'bg-white border border-[#215558]/10 text-[#215558]/60 hover:text-[#215558]' }}">Alle</a>
                @foreach(['nieuw' => 'Nieuw', 'gecontacteerd' => 'Gecontacteerd', 'ingepland' => 'Ingepland', 'afgerond' => 'Afgerond', 'geannuleerd' => 'Geannuleerd'] as $val => $label)
                    <a href="{{ route('proefritten', ['status' => $val]) }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition {{ $current === $val ? 'bg-eazy text-white' : 'bg-white border border-[#215558]/10 text-[#215558]/60 hover:text-[#215558]' }}">{{ $label }}</a>
                @endforeach
            </div>

            {{-- List --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 overflow-hidden">
                @forelse($aanvragen as $a)
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4 px-5 py-4 border-b border-[#215558]/5 last:border-0 hover:bg-[#ebf2f2]/40 transition-colors">
                        {{-- Contact --}}
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-[#215558]">{{ $a->naam }}</div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-xs text-[#215558] opacity-60">
                                <a href="mailto:{{ $a->email }}" class="inline-flex items-center gap-1.5 hover:text-eazy-dark hover:opacity-100"><i class="fa-solid fa-envelope text-[10px]"></i> {{ $a->email }}</a>
                                <a href="tel:{{ $a->telefoon }}" class="inline-flex items-center gap-1.5 hover:text-eazy-dark hover:opacity-100"><i class="fa-solid fa-phone text-[10px]"></i> {{ $a->telefoon }}</a>
                            </div>
                            @if($a->bericht)
                                <p class="text-xs text-[#215558] opacity-50 mt-1.5 italic">&ldquo;{{ $a->bericht }}&rdquo;</p>
                            @endif
                        </div>

                        {{-- Car + date --}}
                        <div class="lg:w-56 shrink-0 text-xs">
                            <div class="flex items-center gap-1.5 text-[#215558] font-semibold">
                                <i class="fa-solid fa-car text-[10px] opacity-40"></i>
                                {{ $a->car?->display_title ?? 'Algemeen' }}
                            </div>
                            <div class="flex items-center gap-1.5 text-[#215558] opacity-50 mt-1">
                                <i class="fa-solid fa-calendar-day text-[10px]"></i>
                                {{ $a->gewenste_datum ? 'Voorkeur: ' . $a->gewenste_datum->format('d-m-Y') : 'Geen datumvoorkeur' }}
                            </div>
                            <div class="text-[10px] text-[#215558] opacity-30 mt-1">Aangevraagd {{ $a->created_at->diffForHumans() }}</div>
                        </div>

                        {{-- Status --}}
                        <div class="lg:w-44 shrink-0">
                            @php $badge = $a->status_badge; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }} mb-2">
                                <i class="fa-solid {{ $badge['icon'] }} text-[9px]"></i> {{ $badge['label'] }}
                            </span>
                            <form method="POST" action="{{ route('proefritten.status', $a) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()"
                                    class="block w-full px-3 py-1.5 rounded-lg border-[#215558]/10 text-xs focus:border-eazy focus:ring-eazy">
                                    @foreach(['nieuw' => 'Nieuw', 'gecontacteerd' => 'Gecontacteerd', 'ingepland' => 'Ingepland', 'afgerond' => 'Afgerond', 'geannuleerd' => 'Geannuleerd'] as $val => $label)
                                        <option value="{{ $val }}" @selected($a->status === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-[#ebf2f2] flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-calendar-check text-[#215558]/20 text-2xl"></i>
                        </div>
                        <p class="text-[#215558] font-bold mb-1">Nog geen proefrit-aanvragen</p>
                        <p class="text-sm text-[#215558] opacity-50 mb-4">Plaats de proefrit-widget op je website zodat klanten een proefrit kunnen aanvragen.</p>
                        <a href="{{ route('integratie') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition">
                            <i class="fa-solid fa-code"></i> Widget-code ophalen
                        </a>
                    </div>
                @endforelse

                @if($aanvragen->hasPages())
                    <div class="px-5 py-4 border-t border-[#215558]/5">
                        {{ $aanvragen->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
