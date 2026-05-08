<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Auto's</h1>
                    <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Beheer je complete autoaanbod</p>
                </div>
                <a href="{{ route('cars.create') }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 hover:shadow-eazy/30 transition-all">
                    <i class="fa-solid fa-plus"></i> Nieuwe auto
                </a>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden mb-6">
                <form method="GET" action="{{ route('cars.index') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Zoeken</label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/30 text-sm"></i>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Merk, model of kenteken..."
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
                            <a href="{{ route('cars.index', ['view' => $viewMode]) }}" class="cursor-pointer inline-flex items-center gap-1 px-3 py-2.5 text-sm text-[#215558] opacity-60 hover:opacity-100 rounded-full hover:bg-white transition">
                                <i class="fa-solid fa-xmark text-xs"></i> Reset
                            </a>
                        @endif
                    </div>

                    {{-- View mode toggle --}}
                    <div class="flex items-center gap-1 bg-[#ebf2f2] rounded-full p-1">
                        <a href="{{ route('cars.index', array_merge(request()->except('view'), ['view' => 'table'])) }}"
                           class="cursor-pointer w-9 h-9 flex items-center justify-center rounded-full transition {{ $viewMode === 'table' ? 'bg-white text-[#215558] shadow-sm' : 'text-[#215558]/40 hover:text-[#215558]/70' }}"
                           title="Tabel weergave">
                            <i class="fa-solid fa-table-list text-sm"></i>
                        </a>
                        <a href="{{ route('cars.index', array_merge(request()->except('view'), ['view' => 'kanban'])) }}"
                           class="cursor-pointer w-9 h-9 flex items-center justify-center rounded-full transition {{ $viewMode === 'kanban' ? 'bg-white text-[#215558] shadow-sm' : 'text-[#215558]/40 hover:text-[#215558]/70' }}"
                           title="Kolom weergave">
                            <i class="fa-solid fa-columns text-sm"></i>
                        </a>
                    </div>
                </form>
            </div>

            @if($viewMode === 'table')
                {{-- Cars Table --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 relative overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-[#215558]/5">
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">Auto</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">Kenteken</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">Prijs</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">Status</th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">
                                        <i class="fa-solid fa-eye mr-0.5"></i> Views
                                    </th>
                                    <th class="px-5 py-3.5 text-left text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider">Datum</th>
                                    <th class="px-5 py-3.5"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cars as $car)
                                    <tr class="border-b border-[#215558]/5 last:border-0 hover:bg-[#ebf2f2]/50 transition-colors">
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
                                                    <div class="text-xs text-[#215558] opacity-50">{{ $car->bouwjaar }} &middot; {{ $car->brandstof_omschrijving }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <code class="text-xs font-mono bg-[#ebf2f2] text-[#215558] opacity-70 px-2 py-1 rounded-lg">{{ $car->kenteken }}</code>
                                        </td>
                                        <td class="px-5 py-3.5 text-sm font-bold text-[#215558]">{{ $car->formatted_price }}</td>
                                        <td class="px-5 py-3.5">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wide
                                                @if($car->status === 'active') bg-emerald-50 text-emerald-600
                                                @elseif($car->status === 'reserved') bg-amber-50 text-amber-600
                                                @elseif($car->status === 'sold') bg-red-50 text-red-500
                                                @else bg-gray-100 text-gray-500 @endif">
                                                <i class="fa-solid
                                                    @if($car->status === 'active') fa-circle-check
                                                    @elseif($car->status === 'reserved') fa-clock
                                                    @elseif($car->status === 'sold') fa-flag-checkered
                                                    @else fa-pencil @endif text-[9px]"></i>
                                                {{ ucfirst($car->status) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="text-sm text-[#215558] opacity-50 flex items-center gap-1 font-medium">
                                                <i class="fa-solid fa-eye text-[10px]"></i>
                                                {{ $car->view_count }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-sm text-[#215558] opacity-40 font-medium">{{ $car->created_at->format('d-m-Y') }}</td>
                                        <td class="px-5 py-3.5 text-right">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('cars.show', $car) }}" class="cursor-pointer w-8 h-8 rounded-lg flex items-center justify-center text-[#215558]/30 hover:bg-eazy-50 hover:text-eazy transition" title="Bekijk">
                                                    <i class="fa-solid fa-eye text-xs"></i>
                                                </a>
                                                <a href="{{ route('cars.edit', $car) }}" class="cursor-pointer w-8 h-8 rounded-lg flex items-center justify-center text-[#215558]/30 hover:bg-blue-50 hover:text-blue-500 transition" title="Bewerk">
                                                    <i class="fa-solid fa-pen text-xs"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-16 text-center">
                                            <div class="w-16 h-16 rounded-full bg-[#ebf2f2] flex items-center justify-center mx-auto mb-4">
                                                <i class="fa-solid fa-car text-[#215558]/20 text-2xl"></i>
                                            </div>
                                            <p class="text-[#215558] font-bold mb-1">Nog geen auto's gevonden</p>
                                            <p class="text-sm text-[#215558] opacity-50 mb-4">Voeg je eerste auto toe om te beginnen.</p>
                                            <a href="{{ route('cars.create') }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition">
                                                <i class="fa-solid fa-plus"></i> Eerste auto toevoegen
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($cars->hasPages())
                        <div class="px-5 py-4 border-t border-[#215558]/5">
                            {{ $cars->links() }}
                        </div>
                    @endif
                </div>
            @else
                {{-- Kanban View --}}
                @if($cars->isEmpty())
                    <div class="bg-white rounded-2xl border border-[#215558]/10 px-6 py-16 text-center">
                        <div class="w-16 h-16 rounded-full bg-[#ebf2f2] flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-car text-[#215558]/20 text-2xl"></i>
                        </div>
                        <p class="text-[#215558] font-bold mb-1">Nog geen auto's gevonden</p>
                        <p class="text-sm text-[#215558] opacity-50 mb-4">Voeg je eerste auto toe om te beginnen.</p>
                        <a href="{{ route('cars.create') }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition">
                            <i class="fa-solid fa-plus"></i> Eerste auto toevoegen
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        @foreach($kanbanColumns as $status => $column)
                            <div class="flex flex-col">
                                {{-- Column header --}}
                                <div class="flex items-center gap-2 mb-3 px-1">
                                    <div class="w-7 h-7 rounded-lg {{ $column['bg'] }} flex items-center justify-center">
                                        <i class="fa-solid {{ $column['icon'] }} {{ $column['text'] }} text-[10px]"></i>
                                    </div>
                                    <span class="text-sm font-bold text-[#215558]">{{ $column['label'] }}</span>
                                    <span class="text-xs font-bold text-[#215558]/30 bg-[#ebf2f2] px-2 py-0.5 rounded-full">{{ $column['cars']->count() }}</span>
                                </div>

                                {{-- Cards --}}
                                <div class="space-y-3 flex-1 rounded-2xl border-2 border-dashed border-[#215558]/10 p-3 min-h-[120px]">
                                    @forelse($column['cars'] as $car)
                                        <a href="{{ route('cars.show', $car) }}" class="block bg-white rounded-xl border border-[#215558]/8 p-3 hover:shadow-md hover:border-[#215558]/15 transition-all group">
                                            {{-- Car image --}}
                                            @if($car->primaryImage)
                                                <img src="{{ $car->primaryImage->url }}" alt="" class="w-full h-28 object-cover rounded-lg mb-3">
                                            @else
                                                <div class="w-full h-28 bg-[#ebf2f2] rounded-lg flex items-center justify-center mb-3">
                                                    <i class="fa-solid fa-image text-[#215558]/15 text-xl"></i>
                                                </div>
                                            @endif

                                            {{-- Car info --}}
                                            <div class="text-sm font-semibold text-[#215558] truncate group-hover:text-eazy transition-colors">{{ $car->display_title }}</div>
                                            <div class="text-xs text-[#215558] opacity-50 mt-0.5">{{ $car->bouwjaar }} &middot; {{ $car->brandstof_omschrijving }}</div>

                                            {{-- Bottom row --}}
                                            <div class="flex items-center justify-between mt-2.5 pt-2.5 border-t border-[#215558]/5">
                                                <span class="text-sm font-bold text-[#215558]">{{ $car->formatted_price }}</span>
                                                <code class="text-[10px] font-mono bg-[#ebf2f2] text-[#215558] opacity-60 px-1.5 py-0.5 rounded">{{ $car->kenteken }}</code>
                                            </div>

                                            {{-- Meta row --}}
                                            <div class="flex items-center gap-3 mt-2 text-[11px] text-[#215558] opacity-40">
                                                <span class="flex items-center gap-1">
                                                    <i class="fa-solid fa-eye text-[9px]"></i> {{ $car->view_count }}
                                                </span>
                                                <span>{{ $car->created_at->format('d-m-Y') }}</span>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="flex items-center justify-center h-20 text-xs text-[#215558] opacity-30 font-medium">
                                            Geen auto's
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
