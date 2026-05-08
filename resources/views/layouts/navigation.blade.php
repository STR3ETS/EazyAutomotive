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
    <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto">
        @php
            $navItems = [
                ['route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'fa-house', 'label' => 'Dashboard'],
                ['route' => 'cars.index', 'match' => 'cars.*', 'icon' => 'fa-car', 'label' => "Auto's"],
                ['route' => 'ontwerpen', 'match' => 'ontwerpen', 'icon' => 'fa-palette', 'label' => 'Ontwerpen'],
                ['route' => 'integratie', 'match' => 'integratie', 'icon' => 'fa-code', 'label' => 'Integratie'],
                ['route' => 'publiceren', 'match' => 'publiceren*', 'icon' => 'fa-share-nodes', 'label' => 'Publiceren'],
                ['route' => 'settings.edit', 'match' => 'settings.*', 'icon' => 'fa-gear', 'label' => 'Instellingen'],
            ];
        @endphp

        @foreach($navItems as $item)
            @php $active = request()->routeIs($item['match']); @endphp
            <a href="{{ route($item['route']) }}"
               @click="sidebarOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150
                      {{ $active
                          ? 'bg-eazy-50 text-eazy-700'
                          : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}">
                <div class="w-8 h-8 flex items-center justify-center rounded-lg
                            {{ $active ? 'bg-eazy text-white' : 'bg-gray-100 text-gray-400' }}">
                    <i class="fa-solid {{ $item['icon'] }} text-xs"></i>
                </div>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
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
