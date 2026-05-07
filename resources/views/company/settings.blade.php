<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Bedrijfsinstellingen</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bedrijfsgegevens</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Bedrijfsnaam *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">E-mailadres</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Telefoonnummer</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                                <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}"
                                    placeholder="https://"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700">Adres</label>
                                <input type="text" name="address" id="address" value="{{ old('address', $company->address) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700">Postcode</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $company->postal_code) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">Plaats</label>
                                <input type="text" name="city" id="city" value="{{ old('city', $company->city) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="kvk_number" class="block text-sm font-medium text-gray-700">KVK-nummer</label>
                                <input type="text" name="kvk_number" id="kvk_number" value="{{ old('kvk_number', $company->kvk_number) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>

                            <div>
                                <label for="btw_number" class="block text-sm font-medium text-gray-700">BTW-nummer</label>
                                <input type="text" name="btw_number" id="btw_number" value="{{ old('btw_number', $company->btw_number) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-eazy focus:ring-eazy sm:text-sm">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="logo" class="block text-sm font-medium text-gray-700">Logo</label>
                            @if($company->logo_path)
                                <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Logo" class="w-24 h-24 object-contain rounded border mb-2">
                            @endif
                            <input type="file" name="logo" id="logo" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-eazy-50 file:text-eazy-dark hover:file:bg-eazy-100">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-eazy border border-transparent rounded-md font-semibold text-sm text-white hover:bg-eazy-dark transition">
                        Instellingen opslaan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
