<x-app-layout>
    <div class="py-8" x-data="customerManager()">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i><span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Klanten</h1>
                    <p class="text-sm text-[#215558] opacity-50">Je debiteuren voor de facturatie.</p>
                </div>
                <button @click="openNew()" type="button" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition">
                    <i class="fa-solid fa-plus text-xs"></i> Klant toevoegen
                </button>
            </div>

            <form method="GET" class="mb-4">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Zoek op naam, bedrijf of e-mail" class="px-4 py-2 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy w-72 max-w-full">
            </form>

            <div class="bg-white rounded-2xl border border-[#215558]/10 overflow-hidden">
                @forelse($customers as $customer)
                    <div class="flex items-center gap-4 px-5 py-3.5 border-b border-[#215558]/5 last:border-0">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-[#215558] truncate">{{ $customer->label }}</div>
                            <div class="text-[11px] text-[#215558] opacity-50">
                                {{ ucfirst($customer->type) }}{{ $customer->email ? ' · ' . $customer->email : '' }}{{ $customer->plaats ? ' · ' . $customer->plaats : '' }}
                            </div>
                        </div>
                        <span class="text-[11px] text-[#215558] opacity-50 shrink-0">{{ $customer->invoices_count }} {{ $customer->invoices_count === 1 ? 'factuur' : 'facturen' }}</span>
                        <button @click='openEdit(@json([
                            "id" => $customer->id, "type" => $customer->type, "naam" => $customer->naam,
                            "bedrijfsnaam" => $customer->bedrijfsnaam, "email" => $customer->email, "telefoon" => $customer->telefoon,
                            "adres" => $customer->adres, "postcode" => $customer->postcode, "plaats" => $customer->plaats,
                            "kvk_nummer" => $customer->kvk_nummer, "btw_nummer" => $customer->btw_nummer, "notities" => $customer->notities,
                        ]))' type="button" class="cursor-pointer text-xs font-semibold text-eazy hover:underline shrink-0">Bewerken</button>
                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Deze klant verwijderen?')" class="shrink-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="cursor-pointer w-8 h-8 text-red-400 hover:bg-red-50 rounded-full transition"><i class="fa-solid fa-trash text-[10px]"></i></button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-12 text-[#215558] opacity-40 text-sm">Nog geen klanten. Voeg er een toe of maak een factuur aan met een nieuwe klant.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $customers->links() }}</div>

            {{-- Modal --}}
            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 overflow-y-auto" @click.self="open = false">
                <div class="bg-white rounded-2xl p-6 w-full max-w-lg my-8">
                    <h3 class="text-lg font-black text-[#215558] mb-4" x-text="mode === 'new' ? 'Nieuwe klant' : 'Klant bewerken'"></h3>
                    <form method="POST" :action="mode === 'new' ? storeUrl : (baseUpdate + '/' + f.id)" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @csrf
                        <input type="hidden" name="_method" :value="mode === 'new' ? 'POST' : 'PUT'">
                        <select name="type" x-model="f.type" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            <option value="particulier">Particulier</option>
                            <option value="zakelijk">Zakelijk</option>
                        </select>
                        <input type="text" name="naam" x-model="f.naam" placeholder="Naam *" required class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        <input type="text" name="bedrijfsnaam" x-model="f.bedrijfsnaam" placeholder="Bedrijfsnaam" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        <input type="email" name="email" x-model="f.email" placeholder="E-mail" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        <input type="text" name="telefoon" x-model="f.telefoon" placeholder="Telefoon" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        <input type="text" name="adres" x-model="f.adres" placeholder="Adres" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        <input type="text" name="postcode" x-model="f.postcode" placeholder="Postcode" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        <input type="text" name="plaats" x-model="f.plaats" placeholder="Plaats" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        <input type="text" name="kvk_nummer" x-model="f.kvk_nummer" placeholder="KvK-nummer" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        <input type="text" name="btw_nummer" x-model="f.btw_nummer" placeholder="BTW-nummer" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        <textarea name="notities" x-model="f.notities" rows="2" placeholder="Notities" class="px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy sm:col-span-2 resize-none"></textarea>
                        <div class="sm:col-span-2 flex justify-end gap-2 pt-1">
                            <button type="button" @click="open = false" class="cursor-pointer px-4 py-2 text-sm font-semibold text-[#215558] opacity-60 hover:opacity-100">Annuleren</button>
                            <button type="submit" class="cursor-pointer px-5 py-2 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition">Opslaan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function customerManager() {
            return {
                open: false,
                mode: 'new',
                storeUrl: '{{ route('customers.store') }}',
                baseUpdate: '{{ url('klanten') }}',
                f: {},
                openNew() { this.mode = 'new'; this.f = { type: 'particulier' }; this.open = true; },
                openEdit(c) { this.mode = 'edit'; this.f = { ...c }; this.open = true; },
            };
        }
    </script>
</x-app-layout>
