<x-app-layout>
    <div class="py-8" x-data="{
        prijs: @js(old('verkoopprijs', '')),
        koper: { naam: @js(old('koper_naam', '')), adres: @js(old('koper_adres', '')), postcode: @js(old('koper_postcode', '')), plaats: @js(old('koper_plaats', '')), email: @js(old('koper_email', '')), telefoon: @js(old('koper_telefoon', '')) },
        pickCar(o) { if (o && o.dataset.prijs) this.prijs = o.dataset.prijs; },
        pickKlant(o) {
            if (!o || !o.value) return;
            this.koper.naam = o.dataset.naam || this.koper.naam;
            this.koper.adres = o.dataset.adres || '';
            this.koper.postcode = o.dataset.postcode || '';
            this.koper.plaats = o.dataset.plaats || '';
            this.koper.email = o.dataset.email || '';
            this.koper.telefoon = o.dataset.telefoon || '';
        }
    }">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-6">
                <a href="{{ route('koopovereenkomsten.index') }}" class="w-9 h-9 rounded-lg bg-white border border-[#215558]/10 flex items-center justify-center text-[#215558] hover:border-eazy transition"><i class="fa-solid fa-arrow-left text-sm"></i></a>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Nieuwe koopovereenkomst</h1>
                    <p class="text-sm text-[#215558] opacity-50">Kies de auto en de koper, de rest wordt zoveel mogelijk voor je ingevuld.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl text-sm">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('koopovereenkomsten.store') }}" class="space-y-6">
                @csrf

                {{-- Voertuig --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <h2 class="text-sm font-black text-[#215558] mb-4">Voertuig</h2>
                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Auto uit voorraad</label>
                    <select name="car_id" required @change="pickCar($event.target.selectedOptions[0])"
                        class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy mb-4">
                        <option value="">Kies een auto...</option>
                        @foreach($cars as $car)
                            <option value="{{ $car->id }}" data-prijs="{{ $car->prijs ? $car->prijs / 100 : '' }}" @selected(old('car_id') == $car->id)>
                                {{ $car->kenteken ?: 'zonder kenteken' }} - {{ trim($car->merk . ' ' . $car->handelsbenaming) }}@if($car->bouwjaar) ({{ $car->bouwjaar }})@endif
                            </option>
                        @endforeach
                    </select>
                    <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Chassisnummer (VIN, optioneel)</label>
                    <input type="text" name="chassisnummer" value="{{ old('chassisnummer') }}" maxlength="40"
                        class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                </div>

                {{-- Koper --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <h2 class="text-sm font-black text-[#215558] mb-4">Koper</h2>
                    @if($customers->count())
                        <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Bestaande klant (optioneel)</label>
                        <select name="customer_id" @change="pickKlant($event.target.selectedOptions[0])"
                            class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy mb-4">
                            <option value="">Nieuwe koper / handmatig invullen</option>
                            @foreach($customers as $klant)
                                <option value="{{ $klant->id }}" @selected(old('customer_id') == $klant->id)
                                    data-naam="{{ $klant->naam }}" data-adres="{{ $klant->adres }}" data-postcode="{{ $klant->postcode }}"
                                    data-plaats="{{ $klant->plaats }}" data-email="{{ $klant->email }}" data-telefoon="{{ $klant->telefoon }}">
                                    {{ $klant->label }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Naam</label>
                            <input type="text" name="koper_naam" x-model="koper.naam" required maxlength="150"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Adres</label>
                            <input type="text" name="koper_adres" x-model="koper.adres" maxlength="150"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Postcode</label>
                            <input type="text" name="koper_postcode" x-model="koper.postcode" maxlength="12"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Plaats</label>
                            <input type="text" name="koper_plaats" x-model="koper.plaats" maxlength="100"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">E-mail</label>
                            <input type="email" name="koper_email" x-model="koper.email" maxlength="150"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Telefoon</label>
                            <input type="text" name="koper_telefoon" x-model="koper.telefoon" maxlength="40"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                    </div>
                </div>

                {{-- Prijs & voorwaarden --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                    <h2 class="text-sm font-black text-[#215558] mb-4">Prijs &amp; voorwaarden</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Verkoopprijs (euro)</label>
                            <input type="number" name="verkoopprijs" x-model="prijs" step="0.01" min="0" required
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">BTW-regeling</label>
                            <select name="btw_type" class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <option value="marge" @selected(old('btw_type', 'marge') === 'marge')>Margeregeling (incl., niet verrekenbaar)</option>
                                <option value="btw" @selected(old('btw_type') === 'btw')>BTW-auto (21% verrekenbaar)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Inruil omschrijving (optioneel)</label>
                            <input type="text" name="inruil_omschrijving" value="{{ old('inruil_omschrijving') }}" maxlength="200"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Inruilbedrag (euro, optioneel)</label>
                            <input type="number" name="inruil_bedrag" value="{{ old('inruil_bedrag') }}" step="0.01" min="0"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Leverdatum (optioneel)</label>
                            <input type="date" name="leverdatum" value="{{ old('leverdatum') }}"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Garantie (optioneel)</label>
                            <input type="text" name="garantie" value="{{ old('garantie') }}" maxlength="200" placeholder="Bijv. 6 maanden BOVAG-garantie"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Bijzonderheden (optioneel)</label>
                            <textarea name="bijzonderheden" rows="3" maxlength="2000"
                                class="block w-full px-4 py-2.5 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy resize-none">{{ old('bijzonderheden') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('koopovereenkomsten.index') }}" class="px-5 py-2.5 rounded-full text-sm font-bold text-[#215558] hover:bg-[#215558]/5 transition">Annuleren</a>
                    <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition">
                        <i class="fa-solid fa-file-contract"></i> Overeenkomst maken
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
