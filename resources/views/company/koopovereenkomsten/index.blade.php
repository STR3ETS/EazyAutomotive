<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-eazy-50 flex items-center justify-center">
                        <i class="fa-solid fa-file-contract text-eazy"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-[#215558]">Koopcontracten</h1>
                        <p class="text-sm text-[#215558] opacity-50">Koop- en verkoopovereenkomsten, klaar om te printen of als PDF op te slaan.</p>
                    </div>
                </div>
                <a href="{{ route('koopovereenkomsten.create') }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition">
                    <i class="fa-solid fa-plus"></i> Nieuwe overeenkomst
                </a>
            </div>

            @if($overeenkomsten->count())
                <div class="bg-white rounded-2xl border border-[#215558]/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[10px] font-bold text-[#215558] opacity-40 uppercase tracking-wider border-b border-[#215558]/10">
                                    <th class="px-5 py-3">Nummer</th>
                                    <th class="px-5 py-3">Datum</th>
                                    <th class="px-5 py-3">Voertuig</th>
                                    <th class="px-5 py-3">Koper</th>
                                    <th class="px-5 py-3">Te betalen</th>
                                    <th class="px-5 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#215558]/5">
                                @foreach($overeenkomsten as $o)
                                    <tr>
                                        <td class="px-5 py-3 font-semibold text-[#215558]">{{ $o->nummer }}</td>
                                        <td class="px-5 py-3 text-[#215558] opacity-70 whitespace-nowrap">{{ $o->created_at->format('d-m-Y') }}</td>
                                        <td class="px-5 py-3 text-[#215558]">
                                            <span class="font-semibold uppercase">{{ data_get($o->voertuig, 'kenteken', '-') }}</span>
                                            <span class="opacity-60">{{ trim(data_get($o->voertuig, 'merk', '') . ' ' . data_get($o->voertuig, 'model', '')) }}</span>
                                        </td>
                                        <td class="px-5 py-3 text-[#215558] opacity-80">{{ data_get($o->koper, 'naam', '-') }}</td>
                                        <td class="px-5 py-3 font-semibold text-[#215558] whitespace-nowrap">{{ \App\Models\Koopovereenkomst::euro($o->teBetalen()) }}</td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('koopovereenkomsten.print', $o) }}" target="_blank" rel="noopener" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#215558] text-white rounded-full text-xs font-bold hover:opacity-90 transition"><i class="fa-solid fa-print text-[10px]"></i> Print / PDF</a>
                                                <form method="POST" action="{{ route('koopovereenkomsten.destroy', $o) }}" onsubmit="return confirm('Deze overeenkomst verwijderen?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="cursor-pointer inline-flex items-center justify-center w-8 h-8 text-red-500 rounded-full hover:bg-red-50 transition"><i class="fa-solid fa-trash text-[10px]"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-4">{{ $overeenkomsten->links() }}</div>
            @else
                <div class="text-center py-16 bg-white rounded-2xl border border-[#215558]/10">
                    <div class="w-12 h-12 rounded-xl bg-[#215558]/5 flex items-center justify-center mx-auto mb-3"><i class="fa-solid fa-file-contract text-[#215558]/20 text-2xl"></i></div>
                    <p class="text-[#215558] font-bold mb-1">Nog geen koopovereenkomsten</p>
                    <p class="text-sm text-[#215558] opacity-50 mb-4">Maak je eerste koopcontract voor een verkochte auto.</p>
                    <a href="{{ route('koopovereenkomsten.create') }}" class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition"><i class="fa-solid fa-plus"></i> Nieuwe overeenkomst</a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
