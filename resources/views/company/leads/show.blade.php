<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <a href="{{ route('leads.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#215558] opacity-50 hover:opacity-100 transition mb-3">
                <i class="fa-solid fa-arrow-left text-[10px]"></i> Terug naar leads
            </a>

            {{-- Header --}}
            <div class="flex flex-wrap items-center gap-3 mb-6">
                <h1 class="text-2xl font-black text-[#215558]">{{ $lead->naam }}</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#ebf2f2] text-[#215558]/70"><i class="fa-solid {{ $lead->type_meta['icon'] }} text-[9px]"></i> {{ $lead->type_meta['label'] }}</span>
                @php $badge = $lead->status_badge; @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $badge['bg'] }} {{ $badge['text'] }}"><i class="fa-solid {{ $badge['icon'] }} text-[9px]"></i> {{ $badge['label'] }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left: details --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                        <h3 class="text-sm font-bold text-[#215558] mb-4">Contactgegevens</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between py-1 border-b border-[#215558]/5">
                                <dt class="text-[#215558] opacity-50">E-mail</dt>
                                <dd>@if($lead->email)<a href="mailto:{{ $lead->email }}" class="font-semibold text-eazy-dark hover:underline">{{ $lead->email }}</a>@else<span class="text-[#215558]/40">-</span>@endif</dd>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-[#215558]/5">
                                <dt class="text-[#215558] opacity-50">Telefoon</dt>
                                <dd>@if($lead->telefoon)<a href="tel:{{ $lead->telefoon }}" class="font-semibold text-eazy-dark hover:underline">{{ $lead->telefoon }}</a>@else<span class="text-[#215558]/40">-</span>@endif</dd>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-[#215558]/5">
                                <dt class="text-[#215558] opacity-50">Auto</dt>
                                <dd class="font-semibold text-[#215558]">{{ $lead->car?->display_title ?? 'Algemeen' }}</dd>
                            </div>
                            @if(! empty($lead->data['gewenste_datum']))
                                <div class="flex items-center justify-between py-1 border-b border-[#215558]/5">
                                    <dt class="text-[#215558] opacity-50">Gewenste datum</dt>
                                    <dd class="font-semibold text-[#215558]">{{ \Illuminate\Support\Carbon::parse($lead->data['gewenste_datum'])->format('d-m-Y') }}</dd>
                                </div>
                            @endif
                            <div class="flex items-center justify-between py-1 border-b border-[#215558]/5">
                                <dt class="text-[#215558] opacity-50">Bron</dt>
                                <dd class="font-semibold text-[#215558] capitalize">{{ $lead->source ?? 'onbekend' }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-[#215558]/5">
                                <dt class="text-[#215558] opacity-50">Binnengekomen</dt>
                                <dd class="font-semibold text-[#215558]">{{ $lead->created_at->format('d-m-Y H:i') }}</dd>
                            </div>
                            @if($lead->last_contacted_at)
                                <div class="flex items-center justify-between py-1">
                                    <dt class="text-[#215558] opacity-50">Laatst gecontacteerd</dt>
                                    <dd class="font-semibold text-[#215558]">{{ $lead->last_contacted_at->format('d-m-Y H:i') }}</dd>
                                </div>
                            @endif
                        </dl>

                        @if($lead->bericht)
                            <div class="mt-5 pt-4 border-t border-[#215558]/5">
                                <h4 class="text-[11px] font-bold text-[#215558] opacity-60 uppercase tracking-wider mb-2">Bericht</h4>
                                <p class="text-sm text-[#215558] opacity-80 whitespace-pre-line">{{ $lead->bericht }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if($lead->email)<a href="mailto:{{ $lead->email }}" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark transition"><i class="fa-solid fa-envelope text-xs"></i> Mailen</a>@endif
                        @if($lead->telefoon)<a href="tel:{{ $lead->telefoon }}" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-[#215558] text-white rounded-full text-sm font-bold hover:bg-eazy-darker transition"><i class="fa-solid fa-phone text-xs"></i> Bellen</a>@endif
                        <form method="POST" action="{{ route('leads.destroy', $lead) }}" class="ml-auto" onsubmit="return confirm('Deze lead verwijderen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 text-red-500 rounded-full text-sm font-semibold hover:bg-red-50 transition"><i class="fa-solid fa-trash text-xs"></i> Verwijderen</button>
                        </form>
                    </div>
                </div>

                {{-- Right: management --}}
                <div>
                    <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                        <h3 class="text-sm font-bold text-[#215558] mb-4">Beheer</h3>
                        <form method="POST" action="{{ route('leads.update', $lead) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Status</label>
                                <select name="status" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    @foreach(\App\Models\Lead::STATUSES as $val => $meta)
                                        <option value="{{ $val }}" @selected($lead->status === $val)>{{ $meta['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Toegewezen aan</label>
                                <select name="assigned_to" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                                    <option value="">Niemand</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected($lead->assigned_to === $user->id)>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Opvolgen op</label>
                                <input type="datetime-local" name="follow_up_at" value="{{ optional($lead->follow_up_at)->format('Y-m-d\TH:i') }}" class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Notities</label>
                                <textarea name="notes" rows="5" maxlength="5000" placeholder="Interne notities over deze lead..." class="block w-full px-3 py-2 rounded-xl border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy resize-none">{{ $lead->notes }}</textarea>
                            </div>
                            <button type="submit" class="cursor-pointer w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 transition"><i class="fa-solid fa-floppy-disk text-xs"></i> Opslaan</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
