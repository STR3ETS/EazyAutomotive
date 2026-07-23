<x-app-layout>
    <div class="py-6" x-data="aiPage({ endpoint: @js(route('ai.send')), undoBase: @js(url('/ai/activity')), csrf: @js(csrf_token()) })">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col" style="height: calc(100vh - 8rem)">

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-4 shrink-0">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-eazy-dark to-eazy-darker flex items-center justify-center text-white shadow-lg shadow-eazy/20">
                    <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V8h1a4 4 0 0 1 4 4v1.27c.6.34 1 .99 1 1.73a2 2 0 1 1-2.73-1.86A2 2 0 0 0 16 12h-1v2h-2v-2H9a2 2 0 0 0-1 3.73A2 2 0 1 1 5 14c0-.74.4-1.39 1-1.73V12a4 4 0 0 1 4-4h1V6.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">AI-collega</h1>
                    <p class="text-sm text-[#215558] opacity-50">Vraag het, of laat het regelen. Ik kan vrijwel alles in het platform, net als jij.</p>
                </div>
            </div>

            {{-- Chat --}}
            <div class="flex-1 min-h-0 bg-white rounded-2xl border border-[#215558]/10 flex flex-col overflow-hidden">
                <div x-ref="scroll" class="flex-1 overflow-y-auto px-4 sm:px-6 py-5 space-y-4 bg-[#f4faf9]">

                    {{-- Welkom / capabilities (leeg) --}}
                    <template x-if="messages.length === 0">
                        <div class="max-w-2xl mx-auto py-4">
                            <p class="text-center text-[#215558] font-bold mb-1">Hoi! Waarmee kan ik je helpen?</p>
                            <p class="text-center text-sm text-[#215558] opacity-50 mb-6">Ik voer taken meteen uit. Alles wat ik wijzig zie je terug en kun je met één klik ongedaan maken.</p>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 mb-6">
                                @foreach([
                                    ['fa-car','Voorraad beheren'],
                                    ['fa-pen-nib','Advertenties schrijven'],
                                    ['fa-right-left','Taxeren op kenteken'],
                                    ['fa-inbox','Leads & klanten'],
                                    ['fa-file-contract','Koopovereenkomsten'],
                                    ['fa-receipt','Kosten inboeken'],
                                    ['fa-palette','Huisstijl aanpassen'],
                                    ['fa-magnifying-glass-chart','RDW & statistieken'],
                                ] as $cap)
                                    <div class="flex items-center gap-2 bg-white border border-[#215558]/10 rounded-xl px-3 py-2.5">
                                        <i class="fa-solid {{ $cap[0] }} text-eazy text-xs"></i>
                                        <span class="text-[11px] font-semibold text-[#215558]">{{ $cap[1] }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <p class="text-[11px] font-bold text-[#215558] opacity-40 uppercase tracking-wider mb-2 text-center">Probeer bijvoorbeeld</p>
                            <div class="flex flex-wrap justify-center gap-2">
                                @foreach([
                                    'Hoeveel auto\'s staan er actief?',
                                    'Voeg de auto met kenteken 12-ABC-3 toe',
                                    'Wat is kenteken 84-KJT-3 waard bij 200.000 km?',
                                    'Schrijf een advertentietekst voor auto 1',
                                    'Laat de nieuwe leads zien',
                                    'Pas het thema Prestige toe',
                                ] as $voorbeeld)
                                    <button type="button" @click="input = @js($voorbeeld); send()"
                                        class="cursor-pointer text-xs px-3 py-1.5 rounded-full border border-[#215558]/10 text-[#215558] hover:border-eazy hover:bg-eazy-50 transition">{{ $voorbeeld }}</button>
                                @endforeach
                            </div>
                        </div>
                    </template>

                    <template x-for="(m, i) in messages" :key="i">
                        <div :class="m.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div class="max-w-[80%]">
                                <div class="px-4 py-2.5 text-sm whitespace-pre-wrap break-words shadow-sm"
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
                        <div class="bg-white border border-gray-200/70 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm">
                            <span class="flex gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-eazy-dark/60 animate-bounce"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-eazy-dark/60 animate-bounce" style="animation-delay:.15s"></span>
                                <span class="w-1.5 h-1.5 rounded-full bg-eazy-dark/60 animate-bounce" style="animation-delay:.3s"></span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Input --}}
                <form @submit.prevent="send" class="p-3 border-t border-gray-100 bg-white shrink-0">
                    <div class="flex items-end gap-2">
                        <textarea x-model="input" rows="1" x-ref="ta"
                                  @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); send(); }"
                                  placeholder="Typ een opdracht of vraag, bijvoorbeeld: zet lead 3 op gewonnen"
                                  class="flex-1 resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark/30 focus:outline-none max-h-32"></textarea>
                        <button type="submit" :disabled="sending || !input.trim()"
                                class="cursor-pointer w-11 h-11 shrink-0 flex items-center justify-center rounded-xl bg-eazy-dark hover:bg-eazy-darker text-white disabled:opacity-40 disabled:cursor-not-allowed transition" aria-label="Versturen">
                            <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 2 11 13"/><path d="M22 2 15 22l-4-9-9-4 20-7z"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.aiPage = function (cfg) {
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
</x-app-layout>
