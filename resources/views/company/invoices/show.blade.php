<x-app-layout>
    @php $badge = $invoice->status_badge; @endphp
    <div class="py-8" x-data="{ payOpen: false }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i><span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i><span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#215558] opacity-50 hover:opacity-100 transition mb-3">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Terug naar facturen
            </a>

            {{-- Header + acties --}}
            <div class="flex flex-wrap items-center gap-3 mb-5">
                <h1 class="text-2xl font-black text-[#215558]">{{ $invoice->number ?: 'Concept-factuur' }}</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }}"><i class="fa-solid {{ $badge['icon'] }} text-[9px]"></i> {{ $badge['label'] }}</span>
                <div class="ml-auto flex flex-wrap items-center gap-2">
                    <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-[#215558]/15 text-[#215558] rounded-full text-sm font-semibold hover:border-eazy transition"><i class="fa-solid fa-print text-xs"></i> Print / PDF</a>
                    @if($invoice->isConcept())
                        <a href="{{ route('invoices.edit', $invoice) }}" class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-[#215558]/15 text-[#215558] rounded-full text-sm font-semibold hover:border-eazy transition"><i class="fa-solid fa-pen text-xs"></i> Bewerken</a>
                        <form method="POST" action="{{ route('invoices.send', $invoice) }}" onsubmit="return confirm('Factuur definitief maken? Er wordt een factuurnummer toegekend en de factuur kan daarna niet meer worden bewerkt.')">
                            @csrf
                            <button type="submit" class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition"><i class="fa-solid fa-paper-plane text-xs"></i> Definitief maken</button>
                        </form>
                    @else
                        @if(!$invoice->isPaid() && $invoice->status !== 'geannuleerd')
                            <button @click="payOpen = true" type="button" class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition"><i class="fa-solid fa-euro-sign text-xs"></i> Betaling registreren</button>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Factuur --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 md:p-8">
                <div class="flex flex-wrap justify-between gap-6 mb-8">
                    <div>
                        <div class="text-sm font-bold text-[#215558]">{{ $invoice->company->name }}</div>
                        <div class="text-xs text-[#215558] opacity-60 whitespace-pre-line">{{ trim(($invoice->company->address ?? '') . "\n" . trim(($invoice->company->postal_code ?? '') . ' ' . ($invoice->company->city ?? ''))) }}</div>
                        @if($invoice->company->btw_number)<div class="text-xs text-[#215558] opacity-60">BTW: {{ $invoice->company->btw_number }}</div>@endif
                        @if($invoice->company->kvk_number)<div class="text-xs text-[#215558] opacity-60">KvK: {{ $invoice->company->kvk_number }}</div>@endif
                    </div>
                    <div class="text-right">
                        <div class="text-[11px] font-bold text-[#215558] opacity-50 uppercase tracking-wider">Factuur aan</div>
                        <div class="text-sm font-semibold text-[#215558] whitespace-pre-line">{{ $invoice->bill_to_address ?: ($invoice->customer?->address_block ?? 'Geen klant gekoppeld') }}</div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-x-8 gap-y-2 text-xs mb-6">
                    <div><span class="text-[#215558] opacity-50">Nummer:</span> <span class="font-semibold text-[#215558]">{{ $invoice->number ?: 'concept' }}</span></div>
                    <div><span class="text-[#215558] opacity-50">Datum:</span> <span class="font-semibold text-[#215558]">{{ $invoice->date->format('d-m-Y') }}</span></div>
                    @if($invoice->due_date)<div><span class="text-[#215558] opacity-50">Vervaldatum:</span> <span class="font-semibold text-[#215558]">{{ $invoice->due_date->format('d-m-Y') }}</span></div>@endif
                    <div><span class="text-[#215558] opacity-50">Regeling:</span> <span class="font-semibold text-[#215558]">{{ $invoice->vat_scheme === 'marge' ? 'Margeregeling' : 'BTW' }}</span></div>
                </div>

                {{-- Regels --}}
                <table class="w-full text-sm mb-6">
                    <thead>
                        <tr class="text-[11px] text-[#215558] opacity-50 uppercase tracking-wider border-b border-[#215558]/10">
                            <th class="text-left font-bold py-2">Omschrijving</th>
                            <th class="text-right font-bold py-2 w-16">Aantal</th>
                            @if($invoice->vat_scheme === 'btw')<th class="text-right font-bold py-2 w-16">BTW</th>@endif
                            <th class="text-right font-bold py-2 w-28">Bedrag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->lines as $line)
                            <tr class="border-b border-[#215558]/5">
                                <td class="py-2.5 text-[#215558]">{{ $line->description }}</td>
                                <td class="py-2.5 text-right text-[#215558] opacity-70">{{ rtrim(rtrim(number_format($line->quantity, 2, ',', '.'), '0'), ',') }}</td>
                                @if($invoice->vat_scheme === 'btw')<td class="py-2.5 text-right text-[#215558] opacity-70">{{ $line->vat_rate }}%</td>@endif
                                <td class="py-2.5 text-right font-semibold text-[#215558]">{{ \App\Models\Invoice::eur($invoice->vat_scheme === 'marge' ? $line->line_total : (int) round($line->line_total * (1 + $line->vat_rate / 100))) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totalen --}}
                <div class="flex justify-end">
                    <div class="w-full sm:w-72 space-y-1.5 text-sm">
                        @if($invoice->vat_scheme === 'btw')
                            <div class="flex justify-between"><span class="text-[#215558] opacity-60">Subtotaal</span><span class="text-[#215558]">{{ \App\Models\Invoice::eur($invoice->subtotal) }}</span></div>
                            <div class="flex justify-between"><span class="text-[#215558] opacity-60">BTW</span><span class="text-[#215558]">{{ \App\Models\Invoice::eur($invoice->vat_amount) }}</span></div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-[#215558]/10"><span class="font-bold text-[#215558]">Totaal</span><span class="text-lg font-black text-[#215558]">{{ \App\Models\Invoice::eur($invoice->total) }}</span></div>
                        @if($invoice->amount_paid > 0)
                            <div class="flex justify-between text-emerald-600"><span>Betaald</span><span>{{ \App\Models\Invoice::eur($invoice->amount_paid) }}</span></div>
                            <div class="flex justify-between font-semibold {{ $invoice->outstanding > 0 ? 'text-amber-600' : 'text-emerald-600' }}"><span>Openstaand</span><span>{{ \App\Models\Invoice::eur($invoice->outstanding) }}</span></div>
                        @endif
                    </div>
                </div>

                @if($invoice->marge_note)
                    <p class="mt-4 text-xs text-[#215558] opacity-60 italic">{{ $invoice->marge_note }}</p>
                @endif
                @if($invoice->notes)
                    <div class="mt-4 pt-4 border-t border-[#215558]/5 text-sm text-[#215558] opacity-80 whitespace-pre-line">{{ $invoice->notes }}</div>
                @endif
            </div>

            {{-- Betalingen --}}
            @if($invoice->payments->count() > 0)
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mt-6">
                    <h3 class="text-sm font-bold text-[#215558] mb-3">Betalingen</h3>
                    @foreach($invoice->payments as $p)
                        <div class="flex items-center justify-between py-2 border-b border-[#215558]/5 last:border-0 text-sm">
                            <span class="text-[#215558]">{{ $p->date->format('d-m-Y') }} <span class="opacity-50">{{ $p->method ? '· ' . ucfirst($p->method) : '' }} {{ $p->note }}</span></span>
                            <span class="font-semibold text-[#215558]">{{ \App\Models\Invoice::eur($p->amount) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Gevarenzone --}}
            <div class="flex items-center gap-3 mt-6">
                @if($invoice->isConcept())
                    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Dit concept verwijderen?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="cursor-pointer text-xs text-red-500 font-semibold hover:underline"><i class="fa-solid fa-trash text-[10px] mr-1"></i> Concept verwijderen</button>
                    </form>
                @elseif($invoice->status !== 'geannuleerd')
                    <form method="POST" action="{{ route('invoices.cancel', $invoice) }}" onsubmit="return confirm('Factuur annuleren? Het nummer blijft behouden voor je administratie.')">
                        @csrf
                        <button type="submit" class="cursor-pointer text-xs text-red-500 font-semibold hover:underline"><i class="fa-solid fa-ban text-[10px] mr-1"></i> Factuur annuleren</button>
                    </form>
                @endif
            </div>

            {{-- Betaling-modal --}}
            <div x-show="payOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" @click.self="payOpen = false">
                <div class="bg-white rounded-2xl p-6 w-full max-w-md">
                    <h3 class="text-lg font-black text-[#215558] mb-4">Betaling registreren</h3>
                    <form method="POST" action="{{ route('invoices.payment', $invoice) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Bedrag</label>
                            <input type="number" step="0.01" name="amount" value="{{ number_format($invoice->outstanding / 100, 2, '.', '') }}" required class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Datum</label>
                                <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Methode</label>
                                <select name="method" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    <option value="bank">Bank</option>
                                    <option value="pin">Pin</option>
                                    <option value="contant">Contant</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="payOpen = false" class="cursor-pointer px-4 py-2 text-sm font-semibold text-[#215558] opacity-60 hover:opacity-100">Annuleren</button>
                            <button type="submit" class="cursor-pointer px-5 py-2 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition">Registreren</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
