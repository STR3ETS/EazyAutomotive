<x-app-layout>
    @php use App\Models\Invoice; @endphp
    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Facturen</h1>
                    <p class="text-sm text-[#215558] opacity-50">Maak en beheer je verkoopfacturen met marge- of BTW-regeling.</p>
                </div>
                <a href="{{ route('invoices.create') }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition">
                    <i class="fa-solid fa-plus text-xs"></i> Nieuwe factuur
                </a>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @php
                    $cards = [
                        ['Openstaand', Invoice::eur($stats['openstaand']), 'fa-hourglass-half', 'text-amber-600'],
                        ['Omzet dit jaar', Invoice::eur($stats['omzet_jaar']), 'fa-euro-sign', 'text-emerald-600'],
                        ['Concepten', $stats['concepten'], 'fa-pen', 'text-gray-500'],
                        ['Vervallen', $stats['vervallen'], 'fa-triangle-exclamation', 'text-red-500'],
                    ];
                @endphp
                @foreach($cards as $c)
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-4">
                        <div class="flex items-center gap-2 text-[11px] font-bold text-[#215558] opacity-50 uppercase tracking-wider mb-1"><i class="fa-solid {{ $c[2] }} {{ $c[3] }}"></i> {{ $c[0] }}</div>
                        <div class="text-xl font-black text-[#215558]">{{ $c[1] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Filters --}}
            <form method="GET" class="flex flex-wrap items-center gap-2 mb-4">
                <div class="flex flex-wrap gap-1.5">
                    <a href="{{ route('invoices.index') }}" class="px-3 py-1.5 rounded-full text-xs font-semibold {{ !request('status') ? 'bg-eazy text-white' : 'bg-white border border-[#215558]/10 text-[#215558]' }}">Alle</a>
                    @foreach(Invoice::STATUSES as $val => $meta)
                        <a href="{{ route('invoices.index', ['status' => $val]) }}" class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('status') === $val ? 'bg-eazy text-white' : 'bg-white border border-[#215558]/10 text-[#215558]' }}">{{ $meta['label'] }}</a>
                    @endforeach
                </div>
                <div class="ml-auto">
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Zoek op nummer of klant" class="px-4 py-2 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy w-64 max-w-full">
                </div>
            </form>

            {{-- List --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 overflow-hidden">
                @forelse($invoices as $invoice)
                    @php $badge = $invoice->status_badge; @endphp
                    <a href="{{ route('invoices.show', $invoice) }}" class="flex items-center gap-4 px-5 py-3.5 border-b border-[#215558]/5 last:border-0 hover:bg-eazy-50/40 transition">
                        <div class="w-32 shrink-0">
                            <div class="text-sm font-bold text-[#215558]">{{ $invoice->number ?: 'Concept' }}</div>
                            <div class="text-[11px] text-[#215558] opacity-50">{{ $invoice->date->format('d-m-Y') }}</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-[#215558] truncate">{{ $invoice->bill_to_name ?: ($invoice->customer?->label ?? 'Geen klant') }}</div>
                            <div class="text-[11px] text-[#215558] opacity-50">{{ $invoice->vat_scheme === 'marge' ? 'Margeregeling' : 'BTW' }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-sm font-bold text-[#215558]">{{ Invoice::eur($invoice->total) }}</div>
                            @if($invoice->outstanding > 0 && !$invoice->isConcept())
                                <div class="text-[11px] text-amber-600">open: {{ Invoice::eur($invoice->outstanding) }}</div>
                            @endif
                        </div>
                        <span class="shrink-0 w-32 text-right">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }}"><i class="fa-solid {{ $badge['icon'] }} text-[9px]"></i> {{ $badge['label'] }}</span>
                        </span>
                    </a>
                @empty
                    <div class="text-center py-12 text-[#215558] opacity-40 text-sm">Nog geen facturen. Klik op "Nieuwe factuur" om te beginnen.</div>
                @endforelse
            </div>

            <div class="mt-4">{{ $invoices->links() }}</div>
        </div>
    </div>
</x-app-layout>
