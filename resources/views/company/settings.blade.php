<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg mt-0.5"></i>
                    <ul class="text-sm font-medium space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-black text-[#215558]">Instellingen</h1>
                <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Beheer je bedrijfsgegevens en branding</p>
            </div>

            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Logo Section --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden mb-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                        <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center">
                            <i class="fa-solid fa-image text-violet-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#215558]">Bedrijfslogo</h3>
                            <p class="text-xs text-[#215558] opacity-50">Je logo wordt getoond in de widget en op facturen</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-6">
                        @if($company->logo_path)
                            <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Logo" class="w-20 h-20 object-contain rounded-xl border-2 border-[#215558]/10 bg-[#ebf2f2]/50 p-2">
                        @else
                            <div class="w-20 h-20 rounded-xl border-2 border-dashed border-[#215558]/15 bg-[#ebf2f2]/50 flex items-center justify-center">
                                <i class="fa-solid fa-building text-[#215558]/20 text-xl"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <label for="logo" class="group cursor-pointer block">
                                <div class="border-2 border-dashed border-[#215558]/15 rounded-xl p-4 text-center hover:border-eazy hover:bg-eazy-50/30 transition-colors">
                                    <i class="fa-solid fa-cloud-arrow-up text-[#215558]/20 text-xl mb-1 group-hover:text-eazy transition-colors"></i>
                                    <p class="text-xs font-bold text-[#215558] opacity-60 group-hover:text-eazy group-hover:opacity-100 transition-colors">Klik om logo te uploaden</p>
                                    <p class="text-[10px] text-[#215558] opacity-40 mt-0.5">PNG, JPG, WebP tot 2MB</p>
                                </div>
                                <input type="file" name="logo" id="logo" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Company Info --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden mb-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                            <i class="fa-solid fa-building text-blue-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#215558]">Bedrijfsgegevens</h3>
                            <p class="text-xs text-[#215558] opacity-50">Basisinformatie over je bedrijf</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">
                                Bedrijfsnaam <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-building absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">E-mailadres</label>
                            <div class="relative">
                                <i class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}"
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Telefoonnummer</label>
                            <div class="relative">
                                <i class="fa-solid fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}"
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            </div>
                        </div>

                        <div>
                            <label for="website" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Website</label>
                            <div class="relative">
                                <i class="fa-solid fa-globe absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}" placeholder="https://"
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/20">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Address --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden mb-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                        <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center">
                            <i class="fa-solid fa-location-dot text-green-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#215558]">Locatie</h3>
                            <p class="text-xs text-[#215558] opacity-50">Adres van je bedrijf</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label for="address" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Adres</label>
                            <div class="relative">
                                <i class="fa-solid fa-road absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="text" name="address" id="address" value="{{ old('address', $company->address) }}"
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            </div>
                        </div>

                        <div>
                            <label for="postal_code" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Postcode</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $company->postal_code) }}"
                                class="block w-full px-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>

                        <div>
                            <label for="city" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Plaats</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $company->city) }}"
                                class="block w-full px-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                    </div>
                </div>

                {{-- Legal --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden mb-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                            <i class="fa-solid fa-file-contract text-amber-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#215558]">Zakelijke gegevens</h3>
                            <p class="text-xs text-[#215558] opacity-50">KVK en BTW informatie</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="kvk_number" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">KVK-nummer</label>
                            <div class="relative">
                                <i class="fa-solid fa-hashtag absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="text" name="kvk_number" id="kvk_number" value="{{ old('kvk_number', $company->kvk_number) }}"
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            </div>
                        </div>

                        <div>
                            <label for="btw_number" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">BTW-nummer</label>
                            <div class="relative">
                                <i class="fa-solid fa-receipt absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                                <input type="text" name="btw_number" id="btw_number" value="{{ old('btw_number', $company->btw_number) }}"
                                    class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-8 py-3 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 hover:shadow-eazy/30 transition-all">
                        <i class="fa-solid fa-floppy-disk"></i> Instellingen opslaan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
