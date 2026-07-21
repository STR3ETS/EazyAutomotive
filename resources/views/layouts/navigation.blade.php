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

        @php
            $groups = [
                ['label' => 'Voorraad', 'items' => [
                    ['route' => 'cars.index', 'match' => 'cars.*', 'icon' => 'fa-car', 'label' => "Auto's", 'tour' => 'nav-cars'],
                    ['route' => 'onderzoek', 'match' => 'onderzoek', 'icon' => 'fa-magnifying-glass-chart', 'label' => 'Onderzoek', 'tour' => 'nav-research'],
                    ['route' => 'bedrijfsvoorraad.index', 'match' => 'bedrijfsvoorraad.*', 'icon' => 'fa-file-signature', 'label' => 'Vrijwaring', 'tour' => 'nav-vrijwaring'],
                ]],
                ['label' => 'Marketing', 'items' => [
                    ['route' => 'publiceren', 'match' => 'publiceren*', 'icon' => 'fa-share-nodes', 'label' => 'Publiceren', 'tour' => 'nav-publish'],
                    ['route' => 'studio.index', 'match' => 'studio*', 'icon' => 'fa-clapperboard', 'label' => 'Video Studio', 'tour' => 'nav-studio'],
                    ['route' => 'ontwerpen', 'match' => 'ontwerpen', 'icon' => 'fa-palette', 'label' => 'Ontwerpen', 'tour' => 'nav-design'],
                    ['route' => 'brandbook.index', 'match' => 'brandbook.*', 'icon' => 'fa-swatchbook', 'label' => 'Brandbook', 'tour' => 'nav-brandbook'],
                    ['route' => 'integratie', 'match' => 'integratie', 'icon' => 'fa-code', 'label' => 'Integratie', 'tour' => 'nav-embed'],
                ]],
                ['label' => 'Verkoop', 'items' => [
                    ['route' => 'leads.index', 'match' => 'leads*', 'icon' => 'fa-inbox', 'label' => 'Leads', 'tour' => 'nav-leads'],
                    ['route' => 'proefritten', 'match' => 'proefritten', 'icon' => 'fa-calendar-check', 'label' => 'Proefritten', 'tour' => 'nav-proefrit'],
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
