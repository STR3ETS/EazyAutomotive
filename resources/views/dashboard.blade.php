<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl shadow-sm">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Welcome Banner --}}
            @php
                $hour = now()->hour;
                if ($hour < 12) $greeting = 'Goedemorgen';
                elseif ($hour < 18) $greeting = 'Goedemiddag';
                else $greeting = 'Goedenavond';
            @endphp
            <div class="bg-gradient-to-br from-eazy-darker via-eazy-dark to-eazy rounded-3xl p-8 text-white mb-8 relative overflow-hidden">
                <button type="button" onclick="window.dispatchEvent(new Event('eazy-replay-tour'))"
                    class="absolute top-5 right-5 z-20 inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur text-white text-xs font-bold transition">
                    <i class="fa-solid fa-circle-play text-xs"></i> Rondleiding
                </button>
                <div class="relative z-10">
                    <p class="text-eazy-200 text-sm font-medium mb-1">{{ $greeting }}</p>
                    <h1 class="text-3xl font-black mb-2">Welkom terug, {{ Auth::user()->name }}!</h1>
                    <p class="text-eazy-100/80 text-sm max-w-md">Hier is een overzicht van je autoaanbod. Beheer je voorraad, bekijk statistieken en pas je ontwerp aan.</p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-[0.07]">
                    <i class="fa-solid fa-car-side text-[180px]"></i>
                </div>
            </div>

            {{-- AI-collega (prominent) --}}
            <div data-tour="ai" class="bg-white rounded-2xl border border-[#215558]/10 p-5 sm:p-6 mb-8 shadow-sm"
                 x-data="aiChat({ endpoint: @js(route('ai.send')), undoBase: @js(url('/ai/activity')), csrf: @js(csrf_token()) })">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-eazy-dark to-eazy-darker flex items-center justify-center text-white shadow-md shadow-eazy/25 shrink-0">
                        <i class="fa-solid fa-robot text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-black text-[#215558]">{{ config('ai.name', 'Sam') }}</h2>
                            <span class="text-[9px] font-black uppercase tracking-wide px-1.5 py-0.5 rounded-full bg-eazy text-white">AI-collega</span>
                        </div>
                        <p class="text-xs text-[#215558] opacity-50">Vraag het, of laat het regelen. Ik kan vrijwel alles in het platform, net als jij.</p>
                    </div>
                </div>

                {{-- Gesprek --}}
                <div x-show="messages.length" x-ref="scroll" class="max-h-80 overflow-y-auto space-y-3 mb-3 pr-1 bg-[#f4faf9] rounded-xl p-3" style="display:none">
                    <template x-for="(m, i) in messages" :key="i">
                        <div :class="m.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div class="max-w-[85%]">
                                <div class="px-3.5 py-2 text-sm whitespace-pre-wrap break-words shadow-sm"
                                     :class="m.role === 'user' ? 'bg-eazy-dark text-white rounded-2xl rounded-br-md' : 'bg-white text-gray-800 border border-gray-200/70 rounded-2xl rounded-bl-md'"
                                     x-text="m.text"></div>
                                <template x-if="m.activities && m.activities.length">
                                    <div class="mt-1.5 space-y-1">
                                        <template x-for="a in m.activities" :key="a.id">
                                            <div class="flex items-center gap-2 text-[11px] bg-emerald-50 border border-emerald-200/70 rounded-lg px-2.5 py-1.5" :class="a.undone ? 'opacity-60' : ''">
                                                <i class="fa-solid fa-circle-check text-emerald-500 text-[10px]"></i>
                                                <span class="flex-1 text-emerald-800" :class="a.undone ? 'line-through' : ''" x-text="a.summary"></span>
                                                <button type="button" x-show="!a.undone" @click="undo(a)" class="cursor-pointer inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-900 font-semibold shrink-0"><i class="fa-solid fa-rotate-left text-[10px]"></i> Ongedaan</button>
                                                <span x-show="a.undone" class="text-gray-400 shrink-0">Teruggedraaid</span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <div x-show="sending" class="flex justify-start">
                        <div class="bg-white border border-gray-200/70 rounded-2xl rounded-bl-md px-3.5 py-2.5 shadow-sm">
                            <span class="flex gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-eazy-dark/60 animate-bounce"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-eazy-dark/60 animate-bounce" style="animation-delay:.15s"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-eazy-dark/60 animate-bounce" style="animation-delay:.3s"></span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Invoer --}}
                <form @submit.prevent="send">
                    <div class="flex items-end gap-2">
                        <textarea x-model="input" rows="1"
                                  @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); send(); }"
                                  placeholder="Typ een opdracht of vraag, bijv. voeg kenteken 12-ABC-3 toe"
                                  class="flex-1 resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark/30 focus:outline-none max-h-28"></textarea>
                        <button type="submit" :disabled="sending || !input.trim()"
                                class="cursor-pointer w-11 h-11 shrink-0 flex items-center justify-center rounded-xl bg-eazy-dark hover:bg-eazy-darker text-white disabled:opacity-40 disabled:cursor-not-allowed transition" aria-label="Versturen">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
                        </button>
                    </div>
                </form>

                {{-- Voorbeelden (alleen als er nog geen gesprek is) --}}
                <div x-show="!messages.length" class="flex flex-wrap gap-2 mt-3">
                    @foreach([
                        'Hoeveel auto\'s staan er actief?',
                        'Voeg de auto met kenteken 12-ABC-3 toe',
                        'Wat is kenteken 84-KJT-3 waard bij 200.000 km?',
                        'Laat de nieuwe leads zien',
                        'Pas het thema Prestige toe',
                    ] as $voorbeeld)
                        <button type="button" @click="input = @js($voorbeeld); send()"
                            class="cursor-pointer text-xs px-3 py-1.5 rounded-full border border-[#215558]/10 text-[#215558] hover:border-eazy hover:bg-eazy-50 transition">{{ $voorbeeld }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-14">
                <a href="{{ route('cars.create') }}" data-tour="quick-add" class="cursor-pointer group bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden flex items-center gap-4 hover:border-eazy/30 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-eazy-50 flex items-center justify-center group-hover:bg-eazy transition-colors">
                        <i class="fa-solid fa-plus text-eazy group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-gray-800">Nieuwe auto</div>
                        <div class="text-xs text-gray-400">Voeg toe aan je aanbod</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-200 group-hover:text-eazy transition-colors"></i>
                </a>
                <a href="{{ route('widgets.index') }}" class="cursor-pointer group bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden flex items-center gap-4 hover:border-eazy/30 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-500 transition-colors">
                        <i class="fa-solid fa-palette text-indigo-500 group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-gray-800">Ontwerpen</div>
                        <div class="text-xs text-gray-400">Pas je widget aan</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-200 group-hover:text-indigo-500 transition-colors"></i>
                </a>
                <a href="{{ route('settings.edit') }}" class="cursor-pointer group bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden flex items-center gap-4 hover:border-eazy/30 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center group-hover:bg-amber-500 transition-colors">
                        <i class="fa-solid fa-gear text-amber-500 group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-gray-800">Instellingen</div>
                        <div class="text-xs text-gray-400">Bedrijfsgegevens beheren</div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-200 group-hover:text-amber-500 transition-colors"></i>
                </a>
            </div>

            {{-- Statistics --}}
            <div data-tour="stats" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                @foreach([
                    ['label' => 'Totaal', 'key' => 'total_cars', 'icon' => 'fa-car', 'iconBg' => 'bg-blue-50', 'iconColor' => 'text-blue-500', 'valueColor' => 'text-blue-600'],
                    ['label' => 'Actief', 'key' => 'active_cars', 'icon' => 'fa-circle-check', 'iconBg' => 'bg-emerald-50', 'iconColor' => 'text-emerald-500', 'valueColor' => 'text-emerald-600'],
                    ['label' => 'Gereserveerd', 'key' => 'reserved_cars', 'icon' => 'fa-clock', 'iconBg' => 'bg-amber-50', 'iconColor' => 'text-amber-500', 'valueColor' => 'text-amber-600'],
                    ['label' => 'Verkocht', 'key' => 'sold_cars', 'icon' => 'fa-flag-checkered', 'iconBg' => 'bg-red-50', 'iconColor' => 'text-red-400', 'valueColor' => 'text-red-500'],
                ] as $stat)
                <div class="group bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl {{ $stat['iconBg'] }} flex items-center justify-center shrink-0">
                        <i class="fa-solid {{ $stat['icon'] }} {{ $stat['iconColor'] }}"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-semibold text-[#215558] opacity-80">{{ $stat['label'] }}</div>
                        <div class="text-2xl font-bold {{ $stat['valueColor'] }}">{{ $stats[$stat['key']] }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Views Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                @foreach([
                    ['label' => 'Views vandaag', 'key' => 'views_today', 'icon' => 'fa-eye', 'trend' => 'Vandaag'],
                    ['label' => 'Views deze week', 'key' => 'views_week', 'icon' => 'fa-chart-line', 'trend' => 'Afgelopen 7 dagen'],
                    ['label' => 'Views deze maand', 'key' => 'views_month', 'icon' => 'fa-chart-bar', 'trend' => 'Afgelopen 30 dagen'],
                ] as $view)
                <div class="group bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-[#215558] opacity-80">{{ $view['label'] }}</span>
                        <div class="w-8 h-8 rounded-lg bg-eazy-50 flex items-center justify-center">
                            <i class="fa-solid {{ $view['icon'] }} text-eazy text-xs"></i>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-eazy mb-1">{{ $stats[$view['key']] }}</div>
                    <div class="text-xs text-[#215558] font-semibold opacity-50"><i class="fa-solid fa-calendar-day mr-1"></i>{{ $view['trend'] }}</div>
                </div>
                @endforeach
            </div>

            {{-- Recent + Popular --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Recent Cars --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                <i class="fa-solid fa-clock-rotate-left text-blue-500 text-xs"></i>
                            </div>
                            <h3 class="text-sm font-bold text-[#215558]">Recent toegevoegd</h3>
                        </div>
                        <a href="{{ route('cars.create') }}" class="text-xs font-semibold text-eazy hover:text-eazy-dark transition flex items-center gap-1">
                            <i class="fa-solid fa-plus text-[10px]"></i> Nieuwe auto
                        </a>
                    </div>
                    <div class="p-4">
                        @forelse($recentCars as $car)
                            <a href="{{ route('cars.show', $car) }}" class="flex items-center gap-4 p-2.5 rounded-xl hover:bg-gray-50 transition-colors {{ !$loop->last ? 'mb-1' : '' }}">
                                @if($car->primaryImage)
                                    <img src="{{ $car->primaryImage->url }}" alt="" class="w-14 h-10 object-cover rounded-lg">
                                @else
                                    <div class="w-14 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-image text-gray-300 text-xs"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-gray-800 truncate">{{ $car->display_title }}</div>
                                    <div class="text-xs text-gray-400">{{ $car->kenteken }} &middot; {{ $car->formatted_price }}</div>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                                    @if($car->status === 'active') bg-emerald-50 text-emerald-600
                                    @elseif($car->status === 'reserved') bg-amber-50 text-amber-600
                                    @elseif($car->status === 'sold') bg-red-50 text-red-500
                                    @else bg-gray-50 text-gray-500 @endif">
                                    @if($car->status === 'active')<i class="fa-solid fa-circle text-[5px]"></i>@endif
                                    {{ ucfirst($car->status) }}
                                </span>
                            </a>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-car text-gray-300 text-xl"></i>
                                </div>
                                <p class="text-sm text-gray-500 mb-2">Nog geen auto's toegevoegd</p>
                                <a href="{{ route('cars.create') }}" class="text-sm font-semibold text-eazy hover:text-eazy-dark transition">
                                    <i class="fa-solid fa-plus mr-1"></i>Voeg je eerste auto toe
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Popular Cars --}}
                <div class="bg-white rounded-2xl border border-[#215558]/10 p-4 relative overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                            <i class="fa-solid fa-fire text-orange-500 text-xs"></i>
                        </div>
                        <h3 class="text-sm font-bold text-[#215558]">Meest bekeken</h3>
                    </div>
                    <div class="p-4">
                        @forelse($popularCars as $i => $car)
                            <a href="{{ route('cars.show', $car) }}" class="flex items-center gap-4 p-2.5 rounded-xl hover:bg-gray-50 transition-colors {{ !$loop->last ? 'mb-1' : '' }}">
                                <div class="w-7 h-7 rounded-lg {{ $i === 0 ? 'bg-amber-100 text-amber-600' : ($i === 1 ? 'bg-gray-100 text-gray-500' : 'bg-orange-50 text-orange-400') }} flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ $i + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-gray-800 truncate">{{ $car->display_title }}</div>
                                    <div class="text-xs text-gray-400">{{ $car->formatted_price }}</div>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-400 font-medium shrink-0">
                                    <i class="fa-solid fa-eye text-[10px]"></i>
                                    {{ $car->view_count }}
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                    <i class="fa-solid fa-chart-line text-gray-300 text-xl"></i>
                                </div>
                                <p class="text-sm text-gray-500">Nog geen views ontvangen</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        window.aiChat = function (cfg) {
            return {
                sending: false, input: '', conversationId: null, messages: [],
                async send() {
                    const text = this.input.trim();
                    if (!text || this.sending) return;
                    this.messages.push({ role: 'user', text });
                    this.input = '';
                    this.sending = true;
                    this.scrollDown();
                    try {
                        const res = await fetch(cfg.endpoint, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
                            body: JSON.stringify({ message: text, conversation_id: this.conversationId }),
                        });
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            this.messages.push({ role: 'assistant', text: data.error || 'Er ging iets mis.' });
                        } else {
                            this.conversationId = data.conversation_id;
                            this.messages.push({ role: 'assistant', text: data.reply, activities: (data.activities || []).map(a => ({ ...a, undone: false })) });
                        }
                    } catch (e) {
                        this.messages.push({ role: 'assistant', text: 'Kon de assistent niet bereiken.' });
                    } finally {
                        this.sending = false;
                        this.scrollDown();
                    }
                },
                async undo(activity) {
                    if (activity.undone) return;
                    try {
                        const res = await fetch(`${cfg.undoBase}/${activity.id}/undo`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf } });
                        if (res.ok) activity.undone = true;
                    } catch (e) {}
                },
                scrollDown() { this.$nextTick(() => { const el = this.$refs.scroll; if (el) el.scrollTop = el.scrollHeight; }); },
            };
        };
    </script>

    {{-- First-login guided tour: highlights real elements and walks through them (see resources/js/tour.js) --}}
    <script>
        (function () {
            var tourConfig = {
                completeUrl: '{{ route('onboarding.complete') }}',
                csrf: '{{ csrf_token() }}',
            };
            function launch() {
                if (window.startEazyTour) {
                    window.startEazyTour(tourConfig);
                }
            }
            @if($showTutorial)
                window.addEventListener('load', function () { setTimeout(launch, 400); });
            @endif
            window.addEventListener('eazy-replay-tour', launch);
        })();
    </script>
</x-app-layout>
