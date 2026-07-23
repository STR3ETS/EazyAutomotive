<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="p-2 fixed top-4 bottom-4 left-4 w-[300px] bg-white border border-gray-200/60 rounded-2xl shadow-lg shadow-gray-200/50 z-30
              transform transition-transform duration-200 ease-in-out lg:translate-x-0
              flex flex-col overflow-hidden">

    {{-- Logo + Close (mobile) --}}
    <div class="flex items-center justify-between px-6 h-16 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5">
            <img src="/assets/logo.webp" class="max-h-8">
        </a>
        <button @click="sidebarOpen = false" class="cursor-pointer lg:hidden w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    {{-- Divider --}}
    <div class="px-5"><div class="border-t border-gray-100"></div></div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 overflow-y-auto">
        {{-- Dashboard: altijd zichtbaar, buiten de secties --}}
        @php $dashActive = request()->routeIs('dashboard'); @endphp
        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false" data-tour="nav-dashboard"
           class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition-colors
                  {{ $dashActive ? 'bg-eazy-50 text-eazy-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
            <span class="w-6 flex justify-center"><i class="fa-solid fa-house text-sm {{ $dashActive ? 'text-eazy' : 'text-gray-400' }}"></i></span>
            <span>Dashboard</span>
        </a>

        {{-- AI-collega: prominent, kan alles in het platform --}}
        @php $aiActive = request()->routeIs('ai.page'); @endphp
        <a href="{{ route('ai.page') }}" @click="sidebarOpen = false" data-tour="nav-ai"
           class="mt-1 flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-bold transition-all
                  {{ $aiActive ? 'bg-gradient-to-br from-eazy-dark to-eazy-darker text-white shadow-md shadow-eazy/20' : 'bg-eazy-50/60 text-eazy-700 hover:bg-eazy-50' }}">
            <span class="w-6 flex justify-center">
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V8h1a4 4 0 0 1 4 4v1.27c.6.34 1 .99 1 1.73a2 2 0 1 1-2.73-1.86A2 2 0 0 0 16 12h-1v2h-2v-2H9a2 2 0 0 0-1 3.73A2 2 0 1 1 5 14c0-.74.4-1.39 1-1.73V12a4 4 0 0 1 4-4h1V6.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/></svg>
            </span>
            <span>AI-collega</span>
            <span class="ml-auto text-[9px] font-black uppercase tracking-wide px-1.5 py-0.5 rounded-full {{ $aiActive ? 'bg-white/20 text-white' : 'bg-eazy text-white' }}">Nieuw</span>
        </a>

        @php
            $groups = [
                ['label' => 'Voorraad', 'items' => [
                    ['route' => 'cars.index', 'match' => 'cars.*', 'icon' => 'fa-car', 'label' => "Auto's", 'tour' => 'nav-cars'],
                    ['route' => 'import.index', 'match' => 'import.*', 'icon' => 'fa-file-import', 'label' => 'Importeren', 'tour' => 'nav-import'],
                    ['route' => 'onderzoek', 'match' => 'onderzoek', 'icon' => 'fa-magnifying-glass-chart', 'label' => 'Onderzoek', 'tour' => 'nav-research'],
                    ['route' => 'bedrijfsvoorraad.index', 'match' => 'bedrijfsvoorraad.*', 'icon' => 'fa-file-signature', 'label' => 'Vrijwaring', 'tour' => 'nav-vrijwaring'],
                ]],
                ['label' => 'Marketing', 'items' => [
                    ['route' => 'publiceren', 'match' => 'publiceren*', 'icon' => 'fa-share-nodes', 'label' => 'Publiceren', 'tour' => 'nav-publish'],
                    ['route' => 'widgets.index', 'match' => 'widgets.*', 'icon' => 'fa-shapes', 'label' => 'Widgets', 'tour' => 'nav-widgets'],
                    ['route' => 'studio.index', 'match' => 'studio*', 'icon' => 'fa-clapperboard', 'label' => 'Video Studio', 'tour' => 'nav-studio'],
                ]],
                ['label' => 'Verkoop', 'items' => [
                    ['route' => 'leads.index', 'match' => 'leads*', 'icon' => 'fa-inbox', 'label' => 'Leads', 'tour' => 'nav-leads'],
                    ['route' => 'proefritten', 'match' => 'proefritten', 'icon' => 'fa-calendar-check', 'label' => 'Proefritten', 'tour' => 'nav-proefrit'],
                    ['route' => 'koopovereenkomsten.index', 'match' => 'koopovereenkomsten.*', 'icon' => 'fa-file-contract', 'label' => 'Koopcontracten', 'tour' => 'nav-koopcontracten'],
                ]],
                ['label' => 'Administratie', 'items' => [
                    ['route' => 'invoices.index', 'match' => 'invoices.*', 'icon' => 'fa-file-invoice', 'label' => 'Facturen', 'tour' => 'nav-invoices'],
                    ['route' => 'customers.index', 'match' => 'customers.*', 'icon' => 'fa-users', 'label' => 'Klanten', 'tour' => 'nav-customers'],
                    ['route' => 'expenses.index', 'match' => 'expenses.*', 'icon' => 'fa-receipt', 'label' => 'Kosten', 'tour' => 'nav-expenses'],
                    ['route' => 'bookkeeping.index', 'match' => 'bookkeeping.*', 'icon' => 'fa-chart-pie', 'label' => 'Boekhouding', 'tour' => 'nav-bookkeeping'],
                ]],
            ];
        @endphp

        @foreach($groups as $group)
            @php $groupActive = collect($group['items'])->contains(fn ($i) => request()->routeIs($i['match'])); @endphp
            <div x-data="{ open: {{ $groupActive ? 'true' : 'false' }} }" class="mt-3">
                <button type="button" @click="open = !open"
                    class="cursor-pointer w-full flex items-center justify-between px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400 hover:text-gray-600 transition-colors">
                    <span>{{ $group['label'] }}</span>
                    <i class="fa-solid fa-chevron-down text-[9px] transition-transform duration-200" :class="{ '-rotate-90': !open }"></i>
                </button>
                <div x-show="open" x-transition.opacity.duration.150ms class="mt-1 space-y-0.5" @unless($groupActive) style="display:none" @endunless>
                    @foreach($group['items'] as $item)
                        @php $active = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}" @click="sidebarOpen = false" data-tour="{{ $item['tour'] }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition-colors
                                  {{ $active ? 'bg-eazy-50 text-eazy-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                            <span class="w-6 flex justify-center"><i class="fa-solid {{ $item['icon'] }} text-sm {{ $active ? 'text-eazy' : 'text-gray-400' }}"></i></span>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Instellingen: standalone onderaan --}}
        @php $setActive = request()->routeIs('settings.*'); @endphp
        <div class="mt-3 pt-3 border-t border-gray-100">
            <a href="{{ route('settings.edit') }}" @click="sidebarOpen = false" data-tour="nav-settings"
               class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-semibold transition-colors
                      {{ $setActive ? 'bg-eazy-50 text-eazy-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <span class="w-6 flex justify-center"><i class="fa-solid fa-gear text-sm {{ $setActive ? 'text-eazy' : 'text-gray-400' }}"></i></span>
                <span>Instellingen</span>
            </a>
        </div>
    </nav>

    {{-- User section --}}
    <div class="px-4 py-4 border-t border-gray-100 shrink-0">
        {{-- User info --}}
        <div class="flex items-center gap-3 px-3 py-2.5 mb-2">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-eazy to-eazy-dark flex items-center justify-center shrink-0">
                <span class="text-xs font-bold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</div>
                <div class="text-xs text-gray-400 truncate">{{ Auth::user()->company?->name }}</div>
            </div>
        </div>

        {{-- Account links --}}
        <a href="{{ route('profile.edit') }}"
           @click="sidebarOpen = false"
           class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
            <i class="fa-solid fa-user w-5 text-center text-xs text-gray-400"></i>
            <span>Profiel</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                <i class="fa-solid fa-right-from-bracket w-5 text-center text-xs text-gray-400"></i>
                <span>Uitloggen</span>
            </button>
        </form>
    </div>
</aside>
