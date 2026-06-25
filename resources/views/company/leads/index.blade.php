<x-app-layout>
    <div class="py-8" x-data="{ addOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Leads</h1>
                    <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Al je leads op één plek: proefrit, contact, inruil, financiering en handmatig.</p>
                </div>
                <button type="button" @click="addOpen = true" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition">
                    <i class="fa-solid fa-plus text-xs"></i> Lead toevoegen
                </button>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach([
                    ['label' => 'Nieuw', 'key' => 'nieuw', 'icon' => 'fa-circle-dot', 'bg' => 'bg-blue-50', 'color' => 'text-blue-500'],
                    ['label' => 'Openstaand', 'key' => 'open', 'icon' => 'fa-clock', 'bg' => 'bg-amber-50', 'color' => 'text-amber-600'],
                    ['label' => 'Gewonnen', 'key' => 'gewonnen', 'icon' => 'fa-trophy', 'bg' => 'bg-emerald-50', 'color' => 'text-emerald-500'],
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

            {{-- Filters --}}
            <div class="space-y-3 mb-5">
                @php $curStatus = request('status'); $curType = request('type'); @endphp
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-[11px] font-bold text-[#215558] opacity-40 uppercase tracking-wider mr-1">Status</span>
                    <a href="{{ route('leads.index', array_filter(['type' => $curType, 'search' => request('search')])) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition {{ !$curStatus ? 'bg-eazy text-white' : 'bg-white border border-[#215558]/10 text-[#215558]/60 hover:text-[#215558]' }}">Alle</a>
                    @foreach(\App\Models\Lead::STATUSES as $val => $meta)
                        <a href="{{ route('leads.index', array_filter(['status' => $val, 'type' => $curType, 'search' => request('search')])) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition {{ $curStatus === $val ? 'bg-eazy text-white' : 'bg-white border border-[#215558]/10 text-[#215558]/60 hover:text-[#215558]' }}">{{ $meta['label'] }}</a>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-[11px] font-bold text-[#215558] opacity-40 uppercase tracking-wider mr-1">Type</span>
                    <a href="{{ route('leads.index', array_filter(['status' => $curStatus, 'search' => request('search')])) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition {{ !$curType ? 'bg-eazy text-white' : 'bg-white border border-[#215558]/10 text-[#215558]/60 hover:text-[#215558]' }}">Alle</a>
                    @foreach(\App\Models\Lead::TYPES as $val => $meta)
                        <a href="{{ route('leads.index', array_filter(['type' => $val, 'status' => $curStatus, 'search' => request('search')])) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition {{ $curType === $val ? 'bg-eazy text-white' : 'bg-white border border-[#215558]/10 text-[#215558]/60 hover:text-[#215558]' }}"><i class="fa-solid {{ $meta['icon'] }} text-[9px] mr-1"></i>{{ $meta['label'] }}</a>
                    @endforeach
                </div>
                <form method="GET" action="{{ route('leads.index') }}" class="relative max-w-xs">
                    @if($curStatus)<input type="hidden" name="status" value="{{ $curStatus }}">@endif
                    @if($curType)<input type="hidden" name="type" value="{{ $curType }}">@endif
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/30 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op naam, e-mail of telefoon" class="block w-full pl-8 pr-4 py-2 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                </form>
            </div>

            {{-- List --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 overflow-hidden">
                @forelse($leads as $lead)
                    <div class="flex flex-col lg:flex-row lg:items-center gap-4 px-5 py-4 border-b border-[#215558]/5 last:border-0 hover:bg-[#ebf2f2]/40 transition-colors">
                        {{-- Contact --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('leads.show', $lead) }}" class="text-sm font-bold text-[#215558] hover:text-eazy-dark transition">{{ $lead->naam }}</a>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-[#ebf2f2] text-[#215558]/70"><i class="fa-solid {{ $lead->type_meta['icon'] }} text-[8px]"></i> {{ $lead->type_meta['label'] }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-xs text-[#215558] opacity-60">
                                @if($lead->email)<a href="mailto:{{ $lead->email }}" class="inline-flex items-center gap-1.5 hover:text-eazy-dark hover:opacity-100"><i class="fa-solid fa-envelope text-[10px]"></i> {{ $lead->email }}</a>@endif
                                @if($lead->telefoon)<a href="tel:{{ $lead->telefoon }}" class="inline-flex items-center gap-1.5 hover:text-eazy-dark hover:opacity-100"><i class="fa-solid fa-phone text-[10px]"></i> {{ $lead->telefoon }}</a>@endif
                            </div>
                            @if($lead->bericht)
                                <p class="text-xs text-[#215558] opacity-50 mt-1.5 italic line-clamp-1">&ldquo;{{ $lead->bericht }}&rdquo;</p>
                            @endif
                        </div>

                        {{-- Car + meta --}}
                        <div class="lg:w-52 shrink-0 text-xs">
                            <div class="flex items-center gap-1.5 text-[#215558] font-semibold">
                                <i class="fa-solid fa-car text-[10px] opacity-40"></i> {{ $lead->car?->display_title ?? 'Algemeen' }}
                            </div>
                            @if($lead->assignedUser)
                                <div class="flex items-center gap-1.5 text-[#215558] opacity-50 mt-1"><i class="fa-solid fa-user text-[10px]"></i> {{ $lead->assignedUser->name }}</div>
                            @endif
                            <div class="text-[10px] text-[#215558] opacity-30 mt-1">{{ $lead->created_at->diffForHumans() }}</div>
                        </div>

                        {{-- Status --}}
                        <div class="lg:w-44 shrink-0">
                            @php $badge = $lead->status_badge; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }} mb-2">
                                <i class="fa-solid {{ $badge['icon'] }} text-[9px]"></i> {{ $badge['label'] }}
                            </span>
                            <form method="POST" action="{{ route('leads.status', $lead) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="block w-full px-3 py-1.5 rounded-lg border-[#215558]/10 text-xs focus:border-eazy focus:ring-eazy">
                                    @foreach(\App\Models\Lead::STATUSES as $val => $meta)
                                        <option value="{{ $val }}" @selected($lead->status === $val)>{{ $meta['label'] }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>

                        <a href="{{ route('leads.show', $lead) }}" class="shrink-0 text-[#215558]/30 hover:text-eazy-dark transition hidden lg:block"><i class="fa-solid fa-chevron-right"></i></a>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-[#ebf2f2] flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-inbox text-[#215558]/20 text-2xl"></i>
                        </div>
                        <p class="text-[#215558] font-bold mb-1">Nog geen leads</p>
                        <p class="text-sm text-[#215558] opacity-50 mb-4">Leads uit je proefrit- en contactwidget komen hier binnen. Je kunt er ook handmatig een toevoegen.</p>
                        <button type="button" @click="addOpen = true" class="inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition">
                            <i class="fa-solid fa-plus"></i> Lead toevoegen
                        </button>
                    </div>
                @endforelse

                @if($leads->hasPages())
                    <div class="px-5 py-4 border-t border-[#215558]/5">{{ $leads->links() }}</div>
                @endif
            </div>

        </div>

        {{-- Add lead modal --}}
        <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
            <div class="absolute inset-0 bg-black/40" @click="addOpen = false"></div>
            <div class="relative bg-white rounded-2xl w-full max-w-lg p-6 shadow-xl" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-black text-[#215558]">Lead toevoegen</h3>
                    <button type="button" @click="addOpen = false" class="cursor-pointer w-8 h-8 flex items-center justify-center rounded-lg text-[#215558]/40 hover:bg-[#ebf2f2]"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" action="{{ route('leads.store') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1">Naam *</label>
                            <input type="text" name="naam" required maxlength="150" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1">Type</label>
                            <select name="type" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                @foreach(\App\Models\Lead::TYPES as $val => $meta)
                                    <option value="{{ $val }}" @selected($val === 'contact')>{{ $meta['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1">E-mail</label>
                            <input type="email" name="email" maxlength="190" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1">Telefoon</label>
                            <input type="tel" name="telefoon" maxlength="40" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1">Auto (optioneel)</label>
                        <select name="car_id" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            <option value="">Geen specifieke auto</option>
                            @foreach($cars as $car)
                                <option value="{{ $car->id }}">{{ $car->display_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1">Bericht / notitie</label>
                        <textarea name="bericht" rows="2" maxlength="2000" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy resize-none"></textarea>
                    </div>
                    <p class="text-[11px] text-[#215558] opacity-40">Vul minstens een e-mail of telefoonnummer in.</p>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="addOpen = false" class="cursor-pointer px-4 py-2 rounded-full text-sm font-semibold text-[#215558]/60 hover:bg-[#ebf2f2]">Annuleren</button>
                        <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition"><i class="fa-solid fa-plus text-xs"></i> Toevoegen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
