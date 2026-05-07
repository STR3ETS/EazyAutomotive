<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Auto's</h2>
            <a href="{{ route('cars.create') }}" class="inline-flex items-center px-4 py-2 bg-eazy border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-eazy-dark transition">
                + Nieuwe auto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('cars.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label for="search" class="block text-sm font-medium text-gray-700">Zoeken</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Merk, model of kenteken..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                                <option value="">Alle</option>
                                <option value="draft" @selected(request('status') === 'draft')>Concept</option>
                                <option value="active" @selected(request('status') === 'active')>Actief</option>
                                <option value="reserved" @selected(request('status') === 'reserved')>Gereserveerd</option>
                                <option value="sold" @selected(request('status') === 'sold')>Verkocht</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-eazy-darker border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-eazy-dark transition">
                                Filteren
                            </button>
                            @if(request('search') || request('status'))
                                <a href="{{ route('cars.index') }}" class="ml-2 text-sm text-gray-500 hover:text-gray-700">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Cars Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kenteken</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prijs</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toegevoegd</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($cars as $car)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if($car->primaryImage)
                                                <img src="{{ $car->primaryImage->url }}" alt="" class="w-12 h-9 object-cover rounded">
                                            @else
                                                <div class="w-12 h-9 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-400">-</div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $car->display_title }}</div>
                                                <div class="text-xs text-gray-500">{{ $car->bouwjaar }} &middot; {{ $car->brandstof_omschrijving }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $car->kenteken }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $car->formatted_price }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($car->status === 'active') bg-green-100 text-green-800
                                            @elseif($car->status === 'reserved') bg-yellow-100 text-yellow-800
                                            @elseif($car->status === 'sold') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($car->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $car->view_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $car->created_at->format('d-m-Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('cars.show', $car) }}" class="text-eazy hover:text-eazy-dark mr-3">Bekijk</a>
                                        <a href="{{ route('cars.edit', $car) }}" class="text-gray-600 hover:text-gray-900">Bewerk</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        Nog geen auto's. <a href="{{ route('cars.create') }}" class="text-eazy hover:underline">Voeg je eerste auto toe!</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($cars->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $cars->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
