<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nieuwe auto toevoegen</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Stap 1: Kenteken opzoeken</h3>
                    <p class="text-sm text-gray-500 mb-6">Voer het kenteken in om automatisch de voertuiggegevens op te halen via de RDW.</p>

                    <form method="POST" action="{{ route('cars.lookup') }}">
                        @csrf

                        <div class="flex gap-4 items-end">
                            <div class="flex-1">
                                <label for="kenteken" class="block text-sm font-medium text-gray-700">Kenteken</label>
                                <input type="text" name="kenteken" id="kenteken" value="{{ old('kenteken') }}"
                                    placeholder="bijv. AB123C"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm uppercase font-mono text-lg tracking-wider"
                                    maxlength="8" required>
                                @error('kenteken')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-eazy border border-transparent rounded-md font-semibold text-sm text-white hover:bg-eazy-dark transition">
                                    Opzoeken
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('cars.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Terug naar overzicht</a>
            </div>

        </div>
    </div>
</x-app-layout>
