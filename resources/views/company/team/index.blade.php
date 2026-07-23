<x-app-layout>
    @php
        // Volledige class-strings per rolkleur, zodat Tailwind ze bij het bouwen oppikt
        // (dynamisch samengestelde classes worden niet gescand).
        $palette = [
            'amber'   => ['badge' => 'bg-amber-50 text-amber-600',   'sel' => 'border-amber-200 bg-amber-50 text-amber-700',     'icon' => 'bg-amber-100 text-amber-600',     'soft' => 'bg-amber-50/40'],
            'violet'  => ['badge' => 'bg-violet-50 text-violet-600', 'sel' => 'border-violet-200 bg-violet-50 text-violet-700',   'icon' => 'bg-violet-100 text-violet-600',   'soft' => 'bg-violet-50/40'],
            'blue'    => ['badge' => 'bg-blue-50 text-blue-600',     'sel' => 'border-blue-200 bg-blue-50 text-blue-700',         'icon' => 'bg-blue-100 text-blue-600',       'soft' => 'bg-blue-50/40'],
            'emerald' => ['badge' => 'bg-emerald-50 text-emerald-600','sel' => 'border-emerald-200 bg-emerald-50 text-emerald-700','icon' => 'bg-emerald-100 text-emerald-600', 'soft' => 'bg-emerald-50/40'],
        ];
        $pal = fn ($role, $slot) => $palette[$roles[$role]['color'] ?? 'blue'][$slot] ?? $palette['blue'][$slot];
    @endphp

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg mt-0.5"></i>
                    <ul class="text-sm font-medium space-y-0.5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Deelbare uitnodigingslink na aanmaken --}}
            @if(session('invite_link'))
                <div x-data="{ link: @js(session('invite_link')), copied: false }"
                     class="mb-6 bg-eazy-50 border border-eazy/20 rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-link text-eazy-dark text-sm"></i>
                        <h3 class="text-sm font-bold text-[#215558]">Uitnodigingslink</h3>
                    </div>
                    <p class="text-xs text-[#215558] opacity-60 mb-3">Stuur deze link naar de collega. Hij is 7 dagen geldig; via de link stelt de collega zelf een wachtwoord in.</p>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly :value="link"
                            class="flex-1 px-4 py-2.5 rounded-full border-[#215558]/10 bg-white text-sm text-[#215558] focus:border-eazy focus:ring-eazy">
                        <button type="button"
                            @click="navigator.clipboard.writeText(link); copied = true; setTimeout(() => copied = false, 2000)"
                            class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 bg-eazy-dark text-white rounded-full text-sm font-bold hover:bg-eazy-darker transition whitespace-nowrap">
                            <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                            <span x-text="copied ? 'Gekopieerd' : 'Kopieer'"></span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Header --}}
            <div class="mb-6">
                <h1 class="text-2xl font-black text-[#215558]">Team &amp; rollen</h1>
                <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Nodig collega's uit en bepaal per rol wat ze mogen zien en doen</p>
            </div>

            {{-- Uitnodigen --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                    <div class="w-9 h-9 rounded-xl bg-eazy-50 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-eazy-dark text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#215558]">Collega uitnodigen</h3>
                        <p class="text-xs text-[#215558] opacity-50">Je krijgt een link die je kunt delen</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('team.invite') }}" class="flex flex-col md:flex-row gap-3 md:items-end">
                    @csrf
                    <div class="flex-1">
                        <label for="email" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">E-mailadres</label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="collega@bedrijf.nl"
                                class="block w-full pl-9 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25">
                        </div>
                    </div>
                    <div class="md:w-52">
                        <label for="role" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Rol</label>
                        <select name="role" id="role"
                            class="block w-full px-4 py-2.5 rounded-full border-[#215558]/10 text-sm focus:border-eazy focus:ring-eazy">
                            @foreach($assignable as $key => $meta)
                                <option value="{{ $key }}" @selected(old('role', 'sales') === $key)>{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-eazy-dark text-white rounded-full text-sm font-bold hover:bg-eazy-darker transition whitespace-nowrap">
                        <i class="fa-solid fa-paper-plane text-xs"></i> Uitnodigen
                    </button>
                </form>
            </div>

            {{-- Teamleden --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-users text-blue-500 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#215558]">Teamleden</h3>
                        <p class="text-xs text-[#215558] opacity-50">{{ $members->count() }} {{ $members->count() === 1 ? 'lid' : 'leden' }}</p>
                    </div>
                </div>

                <div class="space-y-2.5">
                    @foreach($members as $member)
                        @php
                            $meta = $roles[$member->role] ?? $roles['sales'];
                            $isSelf = $member->id === auth()->id();
                            $isOwner = $member->isOwner();
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3.5 rounded-xl border border-[#215558]/5 hover:border-[#215558]/10 transition">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-eazy to-eazy-dark flex items-center justify-center shrink-0">
                                <span class="text-xs font-bold text-white">{{ strtoupper(mb_substr($member->name, 0, 2)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-[#215558] truncate">{{ $member->name }}</span>
                                    @if($isSelf)<span class="text-[10px] font-bold text-eazy-dark bg-eazy-50 px-1.5 py-0.5 rounded">jij</span>@endif
                                </div>
                                <div class="text-xs text-[#215558] opacity-50 truncate">{{ $member->email }}</div>
                            </div>

                            {{-- Rol: badge voor eigenaar/jezelf, anders wijzigbaar --}}
                            @if($isOwner || $isSelf)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ $pal($member->role, 'badge') }}">
                                    <i class="fa-solid {{ $meta['icon'] }} text-[10px]"></i> {{ $meta['label'] }}
                                </span>
                            @else
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('team.role', $member) }}" class="flex items-center">
                                        @csrf @method('PUT')
                                        <select name="role" onchange="this.form.submit()"
                                            class="px-3 py-1.5 rounded-full text-xs font-bold focus:border-eazy focus:ring-eazy cursor-pointer {{ $pal($member->role, 'sel') }}">
                                            @foreach($assignable as $key => $rm)
                                                <option value="{{ $key }}" @selected($member->role === $key)>{{ $rm['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                    <form method="POST" action="{{ route('team.destroy', $member) }}"
                                          onsubmit="return confirm('{{ $member->name }} uit het team verwijderen?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Verwijderen"
                                            class="cursor-pointer w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Openstaande uitnodigingen --}}
            @if($invitations->isNotEmpty())
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 mb-6">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                            <i class="fa-solid fa-hourglass-half text-amber-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#215558]">Openstaande uitnodigingen</h3>
                            <p class="text-xs text-[#215558] opacity-50">Nog niet geaccepteerd</p>
                        </div>
                    </div>

                    <div class="space-y-2.5">
                        @foreach($invitations as $invite)
                            @php $rm = $roles[$invite->role] ?? $roles['sales']; @endphp
                            <div x-data="{ link: @js($invite->acceptUrl()), copied: false }"
                                 class="flex flex-col sm:flex-row sm:items-center gap-3 p-3.5 rounded-xl border border-[#215558]/5">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-[#215558] truncate">{{ $invite->email }}</div>
                                    <div class="text-xs text-[#215558] opacity-50">
                                        {{ $rm['label'] }} &middot;
                                        @if($invite->isExpired())
                                            <span class="text-red-500 font-semibold">verlopen</span>
                                        @else
                                            geldig t/m {{ $invite->expires_at->format('d-m-Y') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        @click="navigator.clipboard.writeText(link); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 border border-[#215558]/15 text-[#215558] rounded-full text-xs font-bold hover:bg-[#215558]/5 transition whitespace-nowrap">
                                        <i class="fa-solid" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                                        <span x-text="copied ? 'Gekopieerd' : 'Kopieer link'"></span>
                                    </button>
                                    <form method="POST" action="{{ route('team.invite.cancel', $invite) }}"
                                          onsubmit="return confirm('Uitnodiging intrekken?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Intrekken"
                                            class="cursor-pointer w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition">
                                            <i class="fa-solid fa-xmark text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Rol-uitleg --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                    <div class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center">
                        <i class="fa-solid fa-circle-info text-gray-400 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#215558]">Wat mag welke rol?</h3>
                        <p class="text-xs text-[#215558] opacity-50">Toegang per rol</p>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach($roles as $key => $meta)
                        <div class="flex items-start gap-3 p-3 rounded-xl {{ $pal($key, 'soft') }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $pal($key, 'icon') }}">
                                <i class="fa-solid {{ $meta['icon'] }} text-xs"></i>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-[#215558]">{{ $meta['label'] }}</div>
                                <div class="text-xs text-[#215558] opacity-60 leading-relaxed">{{ $meta['description'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
