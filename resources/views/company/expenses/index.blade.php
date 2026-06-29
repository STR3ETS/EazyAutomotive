<x-app-layout>
    <div class="py-8" x-data="expenseManager()">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i><span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl text-sm">{{ $errors->first() }}</div>
            @endif

            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Inkoop & kosten</h1>
                    <p class="text-sm text-[#215558] opacity-50">Inkoopfacturen en bonnen, met bijlage en categorie.</p>
                </div>
                <button @click="openNew()" type="button" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition">
                    <i class="fa-solid fa-plus text-xs"></i> Kostenpost toevoegen
                </button>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                    <div class="text-[11px] font-bold text-[#215558] opacity-50 uppercase tracking-wider mb-1">Totaal (incl.)</div>
                    <div class="text-xl font-black text-[#215558]">{{ \App\Models\Invoice::eur($stats['totaal']) }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                    <div class="text-[11px] font-bold text-[#215558] opacity-50 uppercase tracking-wider mb-1">Voorbelasting (BTW)</div>
                    <div class="text-xl font-black text-[#215558]">{{ \App\Models\Invoice::eur($stats['voorbelasting']) }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                    <div class="text-[11px] font-bold text-[#215558] opacity-50 uppercase tracking-wider mb-1">Aantal posten</div>
                    <div class="text-xl font-black text-[#215558]">{{ $stats['aantal'] }}</div>
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" class="flex flex-wrap items-center gap-2 mb-4">
                <select name="category" onchange="this.form.submit()" class="px-3 py-2 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                    <option value="">Alle categorieën</option>
                    @foreach(\App\Models\Expense::CATEGORIES as $val => $label)
                        <option value="{{ $val }}" @selected(request('category') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Zoek op omschrijving of leverancier" class="ml-auto px-4 py-2 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy w-64 max-w-full">
            </form>

            {{-- List --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 overflow-hidden">
                @forelse($expenses as $expense)
                    <div class="flex items-center gap-4 px-5 py-3.5 border-b border-[#215558]/5 last:border-0">
                        <div class="w-24 shrink-0 text-xs text-[#215558] opacity-60">{{ $expense->date->format('d-m-Y') }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-[#215558] truncate">{{ $expense->description }}</div>
                            <div class="text-[11px] text-[#215558] opacity-50">{{ $expense->category_label }}{{ $expense->supplier ? ' · ' . $expense->supplier : '' }}{{ $expense->car ? ' · ' . $expense->car->display_title : '' }}</div>
                        </div>
                        @if($expense->attachment_path)
                            <a href="{{ route('expenses.attachment', $expense) }}" title="Bijlage" class="shrink-0 text-[#215558] opacity-40 hover:opacity-100 hover:text-eazy transition"><i class="fa-solid fa-paperclip"></i></a>
                        @endif
                        <div class="text-right shrink-0 w-28">
                            <div class="text-sm font-bold text-[#215558]">{{ \App\Models\Invoice::eur($expense->amount_incl) }}</div>
                            <div class="text-[10px] text-[#215558] opacity-40">BTW {{ \App\Models\Invoice::eur($expense->vat_amount) }}</div>
                        </div>
                        @php $ed = ['id' => $expense->id, 'date' => $expense->date->format('Y-m-d'), 'supplier' => $expense->supplier, 'description' => $expense->description, 'category' => $expense->category, 'amount' => number_format($expense->amount_incl / 100, 2, '.', ''), 'vat_rate' => $expense->vat_rate, 'car_id' => $expense->car_id, 'notes' => $expense->notes, 'attachment' => $expense->attachment_name]; @endphp
                        <button @click="openEdit({{ \Illuminate\Support\Js::from($ed) }})" type="button" class="cursor-pointer text-xs font-semibold text-eazy hover:underline shrink-0">Bewerken</button>
                        <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Deze kostenpost verwijderen?')" class="shrink-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="cursor-pointer w-8 h-8 text-red-400 hover:bg-red-50 rounded-full transition"><i class="fa-solid fa-trash text-[10px]"></i></button>
                        </form>
                    </div>
                @empty
                    <div class="text-center py-12 text-[#215558] opacity-40 text-sm">Nog geen kosten. Voeg je eerste inkoopfactuur of bon toe.</div>
                @endforelse
            </div>
            <div class="mt-4">{{ $expenses->links() }}</div>

            {{-- Modal --}}
            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 overflow-y-auto" @click.self="open = false">
                <div class="bg-white rounded-2xl p-6 w-full max-w-lg my-8">
                    <h3 class="text-lg font-black text-[#215558] mb-4" x-text="mode === 'new' ? 'Nieuwe kostenpost' : 'Kostenpost bewerken'"></h3>
                    <form method="POST" :action="mode === 'new' ? storeUrl : (baseUpdate + '/' + f.id)" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @csrf
                        <input type="hidden" name="_method" :value="mode === 'new' ? 'POST' : 'PUT'">
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-70 uppercase tracking-wider mb-1">Datum</label>
                            <input type="date" name="date" x-model="f.date" required class="w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-70 uppercase tracking-wider mb-1">Leverancier</label>
                            <input type="text" name="supplier" x-model="f.supplier" placeholder="Optioneel" class="w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-[#215558] opacity-70 uppercase tracking-wider mb-1">Omschrijving</label>
                            <input type="text" name="description" x-model="f.description" required class="w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-70 uppercase tracking-wider mb-1">Categorie</label>
                            <select name="category" x-model="f.category" class="w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                @foreach(\App\Models\Expense::CATEGORIES as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-70 uppercase tracking-wider mb-1">Auto (optioneel)</label>
                            <select name="car_id" x-model="f.car_id" class="w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <option value="">Geen</option>
                                @foreach($cars as $car)
                                    <option value="{{ $car->id }}">{{ $car->display_title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-70 uppercase tracking-wider mb-1">Bedrag (incl. BTW)</label>
                            <input type="number" step="0.01" min="0" name="amount" x-model="f.amount" required class="w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-70 uppercase tracking-wider mb-1">BTW</label>
                            <select name="vat_rate" x-model="f.vat_rate" class="w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                <option value="21">21%</option>
                                <option value="9">9%</option>
                                <option value="0">0% / geen</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-[#215558] opacity-70 uppercase tracking-wider mb-1">Bijlage (bon/factuur)</label>
                            <input type="file" name="attachment" accept=".pdf,image/jpeg,image/png,image/webp" class="w-full text-sm text-[#215558]">
                            <p x-show="f.attachment" class="text-[11px] text-[#215558] opacity-50 mt-1">Huidige bijlage: <span x-text="f.attachment"></span>. Kies een nieuw bestand om te vervangen.</p>
                        </div>
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
        function expenseManager() {
            return {
                open: false,
                mode: 'new',
                storeUrl: '{{ route('expenses.store') }}',
                baseUpdate: '{{ url('kosten') }}',
                f: {},
                openNew() { this.mode = 'new'; this.f = { date: '{{ now()->format('Y-m-d') }}', category: 'overig', vat_rate: '21', car_id: '' }; this.open = true; },
                openEdit(e) { this.mode = 'edit'; this.f = { ...e, vat_rate: String(e.vat_rate), car_id: e.car_id ? String(e.car_id) : '' }; this.open = true; },
            };
        }
    </script>
</x-app-layout>
