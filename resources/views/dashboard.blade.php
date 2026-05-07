<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Totaal auto's</div>
                    <div class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total_cars'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Actief</div>
                    <div class="text-3xl font-bold text-green-600 mt-1">{{ $stats['active_cars'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Gereserveerd</div>
                    <div class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['reserved_cars'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Verkocht</div>
                    <div class="text-3xl font-bold text-red-600 mt-1">{{ $stats['sold_cars'] }}</div>
                </div>
            </div>

            {{-- Views Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Views vandaag</div>
                    <div class="text-2xl font-bold text-eazy mt-1">{{ $stats['views_today'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Views deze week</div>
                    <div class="text-2xl font-bold text-eazy mt-1">{{ $stats['views_week'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500">Views deze maand</div>
                    <div class="text-2xl font-bold text-eazy mt-1">{{ $stats['views_month'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Cars --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Recent toegevoegd</h3>
                            <a href="{{ route('cars.create') }}" class="text-sm text-eazy hover:text-eazy-dark">+ Nieuwe auto</a>
                        </div>
                        @forelse($recentCars as $car)
                            <a href="{{ route('cars.show', $car) }}" class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded">
                                @if($car->primaryImage)
                                    <img src="{{ $car->primaryImage->url }}" alt="{{ $car->display_title }}" class="w-16 h-12 object-cover rounded">
                                @else
                                    <div class="w-16 h-12 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-400">Geen foto</div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">{{ $car->display_title }}</div>
                                    <div class="text-sm text-gray-500">{{ $car->kenteken }} &middot; {{ $car->formatted_price }}</div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                    @if($car->status === 'active') bg-green-100 text-green-800
                                    @elseif($car->status === 'reserved') bg-yellow-100 text-yellow-800
                                    @elseif($car->status === 'sold') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($car->status) }}
                                </span>
                            </a>
                        @empty
                            <p class="text-gray-500 text-sm">Nog geen auto's toegevoegd. <a href="{{ route('cars.create') }}" class="text-eazy hover:underline">Voeg je eerste auto toe!</a></p>
                        @endforelse
                    </div>
                </div>

                {{-- Popular Cars --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Meest bekeken</h3>
                        @forelse($popularCars as $car)
                            <a href="{{ route('cars.show', $car) }}" class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 -mx-2 px-2 rounded">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">{{ $car->display_title }}</div>
                                    <div class="text-sm text-gray-500">{{ $car->formatted_price }}</div>
                                </div>
                                <div class="text-sm text-gray-500">{{ $car->view_count }} views</div>
                            </a>
                        @empty
                            <p class="text-gray-500 text-sm">Nog geen views.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
