<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg mt-0.5"></i>
                    <ul class="text-sm font-medium list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Publiceren</h1>
                    <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Publiceer je auto's op externe platformen</p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                            <i class="fa-solid fa-plug text-blue-500 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-[#215558]">{{ $stats['connected_platforms'] }}</div>
                            <div class="text-[11px] text-[#215558] opacity-50 font-medium">Verbonden</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-[#215558]">{{ $stats['total_published'] }}</div>
                            <div class="text-[11px] text-[#215558] opacity-50 font-medium">Gepubliceerd</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                            <i class="fa-solid fa-clock text-amber-500 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-[#215558]">{{ $stats['total_pending'] }}</div>
                            <div class="text-[11px] text-[#215558] opacity-50 font-medium">Wachtend</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center">
                            <i class="fa-solid fa-circle-exclamation text-red-500 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xl font-black text-[#215558]">{{ $stats['total_failed'] }}</div>
                            <div class="text-[11px] text-[#215558] opacity-50 font-medium">Mislukt</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Switcher --}}
            <div class="flex gap-1 bg-white rounded-full border border-[#215558]/10 p-1 mb-6 w-fit" x-data>
                <button type="button" @click="$dispatch('switch-pub-tab', { tab: 'platforms' })"
                    class="cursor-pointer inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold transition-all"
                    :class="$store.pubTab.active === 'platforms' ? 'bg-eazy text-white shadow-md shadow-eazy/20' : 'text-[#215558]/60 hover:text-[#215558] hover:bg-[#ebf2f2]/50'">
                    <i class="fa-solid fa-plug text-xs"></i> Platformen
                </button>
                <button type="button" @click="$dispatch('switch-pub-tab', { tab: 'cars' })"
                    class="cursor-pointer inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold transition-all"
                    :class="$store.pubTab.active === 'cars' ? 'bg-eazy text-white shadow-md shadow-eazy/20' : 'text-[#215558]/60 hover:text-[#215558] hover:bg-[#ebf2f2]/50'">
                    <i class="fa-solid fa-car text-xs"></i> Auto's
                </button>
                <button type="button" @click="$dispatch('switch-pub-tab', { tab: 'logs' })"
                    class="cursor-pointer inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold transition-all"
                    :class="$store.pubTab.active === 'logs' ? 'bg-eazy text-white shadow-md shadow-eazy/20' : 'text-[#215558]/60 hover:text-[#215558] hover:bg-[#ebf2f2]/50'">
                    <i class="fa-solid fa-clock-rotate-left text-xs"></i> Logboek
                </button>
            </div>

            {{-- ============================================================ --}}
            {{-- TAB 1: Platformen                                            --}}
            {{-- ============================================================ --}}
            <div x-show="$store.pubTab.active === 'platforms'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="grid grid-cols-1 gap-6">
                    @foreach($platforms as $slug => $platform)
                        @php
                            $conn = $connections[$slug] ?? null;
                            $isConnected = $conn && $conn->isConnected();
                            $hasError = $conn && $conn->status === 'error';
                        @endphp
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden" x-data="{ open: false }">
                            {{-- Header --}}
                            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[#215558]/5">
                                <div class="w-9 h-9 rounded-xl bg-{{ $platform['color'] }}-50 flex items-center justify-center">
                                    <i class="{{ str_contains($platform['icon'], 'fa-brands') ? '' : 'fa-solid ' }}{{ $platform['icon'] }} text-{{ $platform['color'] }}-500 text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-bold text-[#215558]">{{ $platform['name'] }}</h3>
                                    <p class="text-xs text-[#215558] opacity-50">{{ $platform['description'] }}</p>
                                </div>
                                @if($isConnected)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-600">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Verbonden
                                    </span>
                                @elseif($hasError)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-red-50 text-red-500">
                                        <i class="fa-solid fa-circle-exclamation text-[9px]"></i> Fout
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-gray-100 text-gray-500">
                                        <i class="fa-solid fa-circle-minus text-[9px]"></i> Niet verbonden
                                    </span>
                                @endif
                            </div>

                            {{-- Error message --}}
                            @if($hasError && $conn->last_error)
                                <div class="mb-4 flex items-center gap-2 bg-red-50 text-red-600 px-3 py-2 rounded-lg text-xs font-medium">
                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                    {{ $conn->last_error }}
                                </div>
                            @endif

                            {{-- Connected state --}}
                            @if($isConnected)
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-[#215558] opacity-40 font-medium">
                                        <i class="fa-solid fa-calendar text-[10px] mr-1"></i>
                                        Verbonden op {{ $conn->connected_at?->format('d-m-Y H:i') }}
                                    </div>
                                    <form method="POST" action="{{ route('publiceren.disconnect', $slug) }}"
                                          onsubmit="return confirm('Weet je zeker dat je {{ $platform['name'] }} wilt loskoppelen?')">
                                        @csrf
                                        <button type="submit" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-full text-[11px] font-semibold hover:bg-red-100 transition">
                                            <i class="fa-solid fa-unlink text-[10px]"></i> Loskoppelen
                                        </button>
                                    </form>
                                </div>
                            @else
                                {{-- Connect toggle --}}
                                <button @click="open = !open" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-eazy text-white rounded-full text-xs font-bold hover:bg-eazy-dark transition">
                                    <i class="fa-solid fa-plug text-[10px]"></i>
                                    <span x-text="open ? 'Annuleren' : 'Verbinden'"></span>
                                </button>

                                {{-- Connect form --}}
                                <div x-show="open" x-transition class="mt-4 pt-4 border-t border-[#215558]/5">
                                    {{-- Help section --}}
                                    @if(!empty($platform['help']))
                                        <div class="mb-5 bg-[#ebf2f2]/60 rounded-xl p-4">
                                            <div class="flex items-center gap-2 mb-2.5">
                                                <i class="fa-solid fa-circle-question text-eazy text-sm"></i>
                                                <h4 class="text-xs font-bold text-[#215558]">{{ $platform['help']['title'] }}</h4>
                                            </div>
                                            <ol class="space-y-2 ml-1">
                                                @foreach($platform['help']['steps'] as $i => $step)
                                                    <li class="flex gap-2.5 text-xs text-[#215558]/70 leading-relaxed">
                                                        <span class="shrink-0 w-5 h-5 rounded-full bg-eazy/10 text-eazy text-[10px] font-bold flex items-center justify-center mt-0.5">{{ $i + 1 }}</span>
                                                        <span>{!! $step !!}</span>
                                                    </li>
                                                @endforeach
                                            </ol>
                                            @if(!empty($platform['help']['note']))
                                                <div class="mt-3 pt-3 border-t border-[#215558]/5 flex gap-2 text-[11px] text-[#215558]/50 leading-relaxed">
                                                    <i class="fa-solid fa-lightbulb text-amber-400 text-[10px] mt-0.5 shrink-0"></i>
                                                    <span>{!! $platform['help']['note'] !!}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('publiceren.connect', $slug) }}">
                                        @csrf
                                        @foreach($platform['fields'] as $field)
                                            <div class="mb-3">
                                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">
                                                    {{ $field['label'] }} @if($field['required'])<span class="text-red-400">*</span>@endif
                                                </label>
                                                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}"
                                                    class="block w-full px-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy"
                                                    placeholder="{{ $field['label'] }}"
                                                    {{ $field['required'] ? 'required' : '' }}>
                                            </div>
                                        @endforeach
                                        <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition-all">
                                            <i class="fa-solid fa-link text-xs"></i> Verbinding opslaan
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- TAB 2: Auto's publiceren                                     --}}
            {{-- ============================================================ --}}
            <div x-show="$store.pubTab.active === 'cars'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">

                @php
                    $connectedPlatforms = $connections->where('status', 'connected');
                @endphp

                @if($connectedPlatforms->isEmpty())
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-[#ebf2f2] flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-plug text-[#215558]/20 text-2xl"></i>
                        </div>
                        <p class="text-[#215558] font-bold mb-1">Geen platformen verbonden</p>
                        <p class="text-sm text-[#215558] opacity-50 mb-4">Verbind eerst een platform via het "Platformen" tabblad.</p>
                        <button type="button" @click="$dispatch('switch-pub-tab', { tab: 'platforms' })"
                            class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition">
                            <i class="fa-solid fa-plug"></i> Platformen bekijken
                        </button>
                    </div>
                @else
                    @php
                        // Group cars for kanban view
                        $kanbanGroups = [
                            'none' => ['label' => 'Niet gepubliceerd', 'icon' => 'fa-circle-minus', 'color' => 'gray', 'cars' => []],
                            'published' => ['label' => 'Gepubliceerd', 'icon' => 'fa-circle-check', 'color' => 'emerald', 'cars' => []],
                            'pending' => ['label' => 'Wachtend', 'icon' => 'fa-clock', 'color' => 'amber', 'cars' => []],
                            'failed' => ['label' => 'Mislukt', 'icon' => 'fa-circle-exclamation', 'color' => 'red', 'cars' => []],
                        ];
                        foreach ($cars as $car) {
                            $pubs = $car->publications->whereIn('platform_connection_id', $connectedPlatforms->pluck('id'));
                            if ($pubs->isEmpty()) {
                                $kanbanGroups['none']['cars'][] = $car;
                            } elseif ($pubs->where('status', 'failed')->isNotEmpty() && $pubs->where('status', 'published')->isEmpty()) {
                                $kanbanGroups['failed']['cars'][] = $car;
                            } elseif ($pubs->where('status', 'published')->isNotEmpty()) {
                                $kanbanGroups['published']['cars'][] = $car;
                            } else {
                                $kanbanGroups['pending']['cars'][] = $car;
                            }
                        }
                    @endphp
                    <div x-data="publishManager()">
                        {{-- Filters --}}
                        <div class="bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden mb-6">
                            <form method="GET" action="{{ route('publiceren') }}" class="flex flex-wrap gap-3 items-end">
                                <input type="hidden" name="tab" value="cars">
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Zoeken</label>
                                    <div class="relative">
                                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/30 text-sm"></i>
                                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Merk, model of kenteken..."
                                            class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25">
                                    </div>
                                </div>
                                <div class="w-44" x-data="{
                                    open: false,
                                    selected: '{{ request('status', '') }}',
                                    options: [
                                        { value: '', label: 'Alle', icon: 'fa-layer-group' },
                                        { value: 'draft', label: 'Concept', icon: 'fa-pencil' },
                                        { value: 'active', label: 'Actief', icon: 'fa-circle-check' },
                                        { value: 'reserved', label: 'Gereserveerd', icon: 'fa-clock' },
                                        { value: 'sold', label: 'Verkocht', icon: 'fa-flag-checkered' },
                                    ],
                                    get selectedLabel() { return this.options.find(o => o.value === this.selected)?.label || 'Alle' },
                                    get selectedIcon() { return this.options.find(o => o.value === this.selected)?.icon || 'fa-layer-group' },
                                }" @click.away="open = false">
                                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Status</label>
                                    <input type="hidden" name="status" :value="selected">
                                    <button type="button" @click="open = !open" class="relative w-full flex items-center gap-2 pl-9 pr-10 py-2.5 rounded-full border border-[#215558]/10 bg-white text-sm text-left focus:border-eazy focus:ring-1 focus:ring-eazy transition cursor-pointer">
                                        <i class="fa-solid absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm" :class="selectedIcon"></i>
                                        <span class="truncate text-[#215558]" x-text="selectedLabel"></span>
                                        <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-[10px] transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                    </button>
                                    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-1" class="absolute z-50 mt-1.5 w-44 bg-white rounded-2xl border border-[#215558]/10 shadow-lg shadow-black/5 py-1.5 overflow-hidden" style="display: none;">
                                        <template x-for="option in options" :key="option.value">
                                            <button type="button" @click="selected = option.value; open = false" class="cursor-pointer w-full flex items-center gap-2.5 px-4 py-2 text-sm text-left hover:bg-[#ebf2f2]/70 transition-colors" :class="{ 'bg-[#ebf2f2]/50 font-semibold text-eazy': selected === option.value, 'text-[#215558]': selected !== option.value }">
                                                <i class="fa-solid text-xs w-4 text-center" :class="option.icon + ' ' + (selected === option.value ? 'text-eazy' : 'text-[#215558]/30')"></i>
                                                <span x-text="option.label"></span>
                                                <i x-show="selected === option.value" class="fa-solid fa-check ml-auto text-eazy text-[10px]"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 bg-[#215558] text-white rounded-full text-sm font-bold hover:bg-eazy-darker transition">
                                        <i class="fa-solid fa-filter text-xs"></i> Filter
                                    </button>
                                    @if(request('search') || request('status'))
                                        <a href="{{ route('publiceren') }}?tab=cars" class="cursor-pointer inline-flex items-center gap-1 px-3 py-2.5 text-sm text-[#215558] opacity-60 hover:opacity-100 rounded-full hover:bg-white transition">
                                            <i class="fa-solid fa-xmark text-xs"></i> Reset
                                        </a>
                                    @endif

                                    {{-- View toggle --}}
                                    <div class="flex items-center border border-[#215558]/10 rounded-full overflow-hidden ml-1">
                                        <button type="button" @click="viewMode = 'table'" class="cursor-pointer w-9 h-9 flex items-center justify-center transition-all"
                                            :class="viewMode === 'table' ? 'bg-[#215558] text-white' : 'bg-white text-[#215558]/30 hover:text-[#215558]/60'">
                                            <i class="fa-solid fa-table-list text-xs"></i>
                                        </button>
                                        <button type="button" @click="viewMode = 'kanban'" class="cursor-pointer w-9 h-9 flex items-center justify-center transition-all"
                                            :class="viewMode === 'kanban' ? 'bg-[#215558] text-white' : 'bg-white text-[#215558]/30 hover:text-[#215558]/60'">
                                            <i class="fa-solid fa-columns text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- ==================== TABLE VIEW ==================== --}}
                        <div x-show="viewMode === 'table'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <div class="bg-white rounded-2xl border border-[#215558]/10 relative overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="border-b border-[#215558]/5">
                                                <th class="px-5 py-3.5 text-left w-10">
                                                    <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 text-eazy focus:ring-eazy cursor-pointer">
                                                </th>
                                                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">Auto</th>
                                                <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">Prijs</th>
                                                @foreach($connectedPlatforms as $conn)
                                                    <th class="px-5 py-3.5 text-center text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">
                                                        @php $pConf = $platforms[$conn->platform] ?? null; @endphp
                                                        @if($pConf)
                                                            <i class="{{ str_contains($pConf['icon'], 'fa-brands') ? '' : 'fa-solid ' }}{{ $pConf['icon'] }} mr-1"></i>
                                                        @endif
                                                        {{ $pConf['name'] ?? $conn->platform }}
                                                    </th>
                                                @endforeach
                                                <th class="px-5 py-3.5"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($cars as $car)
                                                <tr class="border-b border-[#215558]/5 last:border-0 hover:bg-[#ebf2f2]/50 transition-colors">
                                                    <td class="px-5 py-3.5">
                                                        <input type="checkbox" value="{{ $car->id }}" x-model="selectedCars" class="rounded border-gray-300 text-eazy focus:ring-eazy cursor-pointer">
                                                    </td>
                                                    <td class="px-5 py-3.5">
                                                        <div class="flex items-center gap-3">
                                                            @if($car->primaryImage)
                                                                <img src="{{ $car->primaryImage->url }}" alt="" class="w-14 h-10 object-cover rounded-lg">
                                                            @else
                                                                <div class="w-14 h-10 bg-[#ebf2f2] rounded-lg flex items-center justify-center">
                                                                    <i class="fa-solid fa-image text-[#215558]/20 text-xs"></i>
                                                                </div>
                                                            @endif
                                                            <div>
                                                                <div class="text-sm font-semibold text-[#215558]">{{ $car->display_title }}</div>
                                                                <code class="text-[10px] font-mono text-[#215558] opacity-50">{{ $car->kenteken }}</code>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-5 py-3.5 text-sm font-bold text-[#215558]">{{ $car->formatted_price }}</td>
                                                    @foreach($connectedPlatforms as $conn)
                                                        <td class="px-5 py-3.5 text-center">
                                                            @php
                                                                $pub = $car->publications->where('platform_connection_id', $conn->id)->first();
                                                            @endphp
                                                            @if($pub)
                                                                @php $badge = $pub->status_badge; @endphp
                                                                <div class="inline-flex flex-col items-center gap-1">
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }}">
                                                                        <i class="fa-solid {{ $badge['icon'] }} text-[8px]"></i> {{ $badge['label'] }}
                                                                    </span>
                                                                    @if($pub->status === 'published')
                                                                        <form method="POST" action="{{ route('publiceren.unpublish', $pub) }}" class="inline">
                                                                            @csrf
                                                                            <button type="submit" class="cursor-pointer text-[9px] text-red-400 hover:text-red-600 font-medium transition">
                                                                                Verwijderen
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                    @if($pub->status === 'failed' && $pub->error_message)
                                                                        <span class="text-[9px] text-red-400 max-w-[120px] truncate" title="{{ $pub->error_message }}">
                                                                            {{ $pub->error_message }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-[10px] text-gray-300">&mdash;</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                    <td class="px-5 py-3.5"></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ 4 + $connectedPlatforms->count() }}" class="px-6 py-16 text-center">
                                                        <div class="w-16 h-16 rounded-full bg-[#ebf2f2] flex items-center justify-center mx-auto mb-4">
                                                            <i class="fa-solid fa-car text-[#215558]/20 text-2xl"></i>
                                                        </div>
                                                        <p class="text-[#215558] font-bold mb-1">Geen auto's gevonden</p>
                                                        <p class="text-sm text-[#215558] opacity-50">Voeg eerst auto's toe om te publiceren.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Bulk action bar --}}
                                <div x-show="selectedCars.length > 0" x-transition
                                     class="sticky bottom-0 bg-white border-t border-[#215558]/10 px-5 py-4">
                                    <form method="POST" action="{{ route('publiceren.publish') }}" class="flex flex-wrap items-center justify-between gap-4">
                                        @csrf
                                        <template x-for="carId in selectedCars" :key="carId">
                                            <input type="hidden" name="car_ids[]" :value="carId">
                                        </template>

                                        <span class="text-sm font-semibold text-[#215558]">
                                            <span x-text="selectedCars.length"></span> auto('s) geselecteerd
                                        </span>

                                        <div class="flex items-center gap-3">
                                            @foreach($connectedPlatforms as $conn)
                                                @php $pConf = $platforms[$conn->platform] ?? null; @endphp
                                                <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#ebf2f2] cursor-pointer hover:bg-eazy-50 transition">
                                                    <input type="checkbox" name="platform_ids[]" value="{{ $conn->id }}" checked class="rounded border-gray-300 text-eazy focus:ring-eazy cursor-pointer">
                                                    <span class="text-xs font-semibold text-[#215558]">{{ $pConf['name'] ?? $conn->platform }}</span>
                                                </label>
                                            @endforeach

                                            <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition-all">
                                                <i class="fa-solid fa-share-nodes"></i> Publiceren
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                @if($cars->hasPages())
                                    <div class="px-5 py-4 border-t border-[#215558]/5">
                                        {{ $cars->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ==================== KANBAN VIEW ==================== --}}
                        <div x-show="viewMode === 'kanban'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                                @foreach($kanbanGroups as $groupKey => $group)
                                    <div class="flex flex-col min-h-[200px]">
                                        {{-- Column header --}}
                                        <div class="flex items-center gap-2 mb-3 px-1">
                                            <div class="w-6 h-6 rounded-lg bg-{{ $group['color'] }}-50 flex items-center justify-center">
                                                <i class="fa-solid {{ $group['icon'] }} text-{{ $group['color'] }}-500 text-[10px]"></i>
                                            </div>
                                            <span class="text-xs font-bold text-[#215558]">{{ $group['label'] }}</span>
                                            <span class="ml-auto text-[10px] font-bold text-[#215558] opacity-30 bg-[#ebf2f2] px-1.5 py-0.5 rounded-full">{{ count($group['cars']) }}</span>
                                        </div>

                                        {{-- Column body --}}
                                        <div class="flex-1 bg-[#ebf2f2]/50 rounded-2xl border border-[#215558]/5 p-2.5 space-y-2.5 overflow-y-auto max-h-[600px]">
                                            @forelse($group['cars'] as $car)
                                                <div class="bg-white rounded-xl border border-[#215558]/10 p-3 hover:shadow-md hover:shadow-black/5 transition-all">
                                                    {{-- Car image + checkbox --}}
                                                    <div class="flex items-start gap-2 mb-2">
                                                        <input type="checkbox" value="{{ $car->id }}" x-model="selectedCars" class="rounded border-gray-300 text-eazy focus:ring-eazy cursor-pointer mt-0.5 shrink-0">
                                                        @if($car->primaryImage)
                                                            <img src="{{ $car->primaryImage->url }}" alt="" class="w-full h-24 object-cover rounded-lg">
                                                        @else
                                                            <div class="w-full h-24 bg-[#ebf2f2] rounded-lg flex items-center justify-center">
                                                                <i class="fa-solid fa-image text-[#215558]/15 text-xl"></i>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Car info --}}
                                                    <div class="mb-2">
                                                        <div class="text-xs font-semibold text-[#215558] leading-tight mb-0.5">{{ $car->display_title }}</div>
                                                        <div class="text-[11px] font-bold text-eazy">{{ $car->formatted_price }}</div>
                                                    </div>

                                                    {{-- Platform badges --}}
                                                    @php
                                                        $carPubs = $car->publications->whereIn('platform_connection_id', $connectedPlatforms->pluck('id'));
                                                    @endphp
                                                    @if($carPubs->isNotEmpty())
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach($carPubs as $pub)
                                                                @php
                                                                    $pConf = $platforms[$pub->platformConnection->platform] ?? null;
                                                                    $badge = $pub->status_badge;
                                                                @endphp
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }}" title="{{ $pConf['name'] ?? '' }}: {{ $badge['label'] }}">
                                                                    @if($pConf)
                                                                        <i class="{{ str_contains($pConf['icon'], 'fa-brands') ? '' : 'fa-solid ' }}{{ $pConf['icon'] }} text-[8px]"></i>
                                                                    @endif
                                                                    {{ $badge['label'] }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="flex items-center gap-1 text-[10px] text-[#215558] opacity-30">
                                                            <i class="fa-solid fa-circle-minus text-[8px]"></i> Niet gepubliceerd
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <div class="flex items-center justify-center h-20 text-[11px] text-[#215558] opacity-30 font-medium">
                                                    Geen auto's
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Bulk action bar (kanban) --}}
                            <div x-show="selectedCars.length > 0" x-transition
                                 class="mt-4 bg-white rounded-2xl border border-[#215558]/10 px-5 py-4">
                                <form method="POST" action="{{ route('publiceren.publish') }}" class="flex flex-wrap items-center justify-between gap-4">
                                    @csrf
                                    <template x-for="carId in selectedCars" :key="carId">
                                        <input type="hidden" name="car_ids[]" :value="carId">
                                    </template>

                                    <span class="text-sm font-semibold text-[#215558]">
                                        <span x-text="selectedCars.length"></span> auto('s) geselecteerd
                                    </span>

                                    <div class="flex items-center gap-3">
                                        @foreach($connectedPlatforms as $conn)
                                            @php $pConf = $platforms[$conn->platform] ?? null; @endphp
                                            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#ebf2f2] cursor-pointer hover:bg-eazy-50 transition">
                                                <input type="checkbox" name="platform_ids[]" value="{{ $conn->id }}" checked class="rounded border-gray-300 text-eazy focus:ring-eazy cursor-pointer">
                                                <span class="text-xs font-semibold text-[#215558]">{{ $pConf['name'] ?? $conn->platform }}</span>
                                            </label>
                                        @endforeach

                                        <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition-all">
                                            <i class="fa-solid fa-share-nodes"></i> Publiceren
                                        </button>
                                    </div>
                                </form>
                            </div>

                            @if($cars->hasPages())
                                <div class="mt-4 bg-white rounded-2xl border border-[#215558]/10 px-5 py-4">
                                    {{ $cars->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- TAB 3: Logboek                                               --}}
            {{-- ============================================================ --}}
            <div x-show="$store.pubTab.active === 'logs'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <div class="bg-white rounded-2xl border border-[#215558]/10 relative overflow-hidden">
                    @if($recentLogs->isEmpty())
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 rounded-full bg-[#ebf2f2] flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-clock-rotate-left text-[#215558]/20 text-2xl"></i>
                            </div>
                            <p class="text-[#215558] font-bold mb-1">Nog geen activiteit</p>
                            <p class="text-sm text-[#215558] opacity-50">Zodra je platformen verbindt of auto's publiceert verschijnt hier het logboek.</p>
                        </div>
                    @else
                        <div class="divide-y divide-[#215558]/5">
                            @foreach($recentLogs as $log)
                                @php
                                    $actionConfig = match($log->action) {
                                        'publish' => ['icon' => 'fa-arrow-up-from-bracket', 'color' => 'blue'],
                                        'unpublish' => ['icon' => 'fa-arrow-down', 'color' => 'orange'],
                                        'connect' => ['icon' => 'fa-plug', 'color' => 'emerald'],
                                        'disconnect' => ['icon' => 'fa-unlink', 'color' => 'red'],
                                        default => ['icon' => 'fa-circle-info', 'color' => 'gray'],
                                    };
                                    $statusColor = $log->status === 'success' ? 'emerald' : 'red';
                                    $platformConfig = $platforms[$log->platform] ?? null;
                                @endphp
                                <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-[#ebf2f2]/30 transition-colors">
                                    {{-- Action icon --}}
                                    <div class="w-8 h-8 rounded-lg bg-{{ $actionConfig['color'] }}-50 flex items-center justify-center shrink-0">
                                        <i class="fa-solid {{ $actionConfig['icon'] }} text-{{ $actionConfig['color'] }}-500 text-xs"></i>
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm text-[#215558] font-medium">
                                            {{ $log->message ?? ucfirst($log->action) }}
                                        </div>
                                        <div class="flex items-center gap-2 mt-0.5 text-[11px] text-[#215558] opacity-40 font-medium">
                                            @if($log->car)
                                                <span>{{ $log->car->display_title }}</span>
                                                <span>&middot;</span>
                                            @endif
                                            @if($platformConfig)
                                                <span>{{ $platformConfig['name'] }}</span>
                                                <span>&middot;</span>
                                            @endif
                                            @if($log->user)
                                                <span>{{ $log->user->name }}</span>
                                                <span>&middot;</span>
                                            @endif
                                            <span>{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    {{-- Status badge --}}
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-{{ $statusColor }}-50 text-{{ $statusColor }}-600 shrink-0">
                                        <i class="fa-solid {{ $log->status === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' }} text-[8px]"></i>
                                        {{ $log->status === 'success' ? 'Gelukt' : 'Mislukt' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('pubTab', {
                active: new URLSearchParams(window.location.search).get('tab') || 'platforms'
            });
        });
        window.addEventListener('switch-pub-tab', e => {
            Alpine.store('pubTab').active = e.detail.tab;
        });

        function publishManager() {
            return {
                selectedCars: [],
                viewMode: 'table',
                toggleAll(event) {
                    if (event.target.checked) {
                        const scope = this.viewMode === 'table' ? 'table tbody' : '[x-show*="kanban"]';
                        this.selectedCars = [...this.$el.querySelectorAll('input[type=checkbox][value]')].map(c => c.value);
                    } else {
                        this.selectedCars = [];
                    }
                },
            };
        }
    </script>
</x-app-layout>
