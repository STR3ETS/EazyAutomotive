{{-- Autonomous AI colleague: chat sidebar + activity feed with one-click undo. --}}
<div
    x-data="eazyAssistant({ endpoint: @js(route('ai.send')), undoBase: @js(url('/ai/activity')), csrf: @js(csrf_token()) })"
    class="fixed bottom-6 right-6 z-40 flex flex-col items-end print:hidden"
>
    {{-- Panel --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mb-3 w-[min(380px,calc(100vw-2rem))] h-[min(560px,72vh)] flex flex-col bg-white rounded-2xl shadow-2xl shadow-gray-400/30 border border-gray-200/70 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-br from-eazy-darker via-eazy-dark to-eazy text-white shrink-0">
            <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 11.5a8.38 8.38 0 0 1-8.5 8.5 8.5 8.5 0 0 1-3.8-.9L3 21l1.9-5.7A8.38 8.38 0 0 1 4 11.5 8.5 8.5 0 0 1 12.5 3 8.38 8.38 0 0 1 21 11.5z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-bold leading-tight">AI-collega</div>
                <div class="text-[11px] text-eazy-50">Vraag het, of laat het regelen</div>
            </div>
            <button type="button" @click="open = false" class="cursor-pointer w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white/15 transition" aria-label="Sluiten">
                <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Messages --}}
        <div x-ref="scroll" class="flex-1 overflow-y-auto px-4 py-4 space-y-3 bg-[#f4faf9]">
            <template x-if="messages.length === 0">
                <div class="text-center text-xs text-muted py-6 px-2">
                    <p class="font-semibold text-eazy-darker mb-1">Hoi! Ik ben je AI-collega.</p>
                    <p>Probeer bijvoorbeeld: <span class="font-medium text-eazy-dark">"Voeg de auto met kenteken 12-ABC-3 toe"</span> of <span class="font-medium text-eazy-dark">"Hoeveel auto's staan er actief?"</span></p>
                </div>
            </template>

            <template x-for="(m, i) in messages" :key="i">
                <div :class="m.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div class="max-w-[85%]">
                        <div
                            class="px-3.5 py-2 text-sm whitespace-pre-wrap break-words shadow-sm"
                            :class="m.role === 'user'
                                ? 'bg-eazy-dark text-white rounded-2xl rounded-br-md'
                                : 'bg-white text-gray-800 border border-gray-200/70 rounded-2xl rounded-bl-md'"
                            x-text="m.text"></div>

                        {{-- Activity chips with undo --}}
                        <template x-if="m.activities && m.activities.length">
                            <div class="mt-1.5 space-y-1">
                                <template x-for="a in m.activities" :key="a.id">
                                    <div class="flex items-center gap-2 text-[11px] bg-emerald-50 border border-emerald-200/70 rounded-lg px-2 py-1"
                                         :class="a.undone ? 'opacity-60' : ''">
                                        <span class="flex-1 text-emerald-800" :class="a.undone ? 'line-through' : ''" x-text="a.summary"></span>
                                        <button type="button" x-show="!a.undone" @click="undo(a)"
                                                class="cursor-pointer inline-flex items-center gap-1 text-emerald-700 hover:text-emerald-900 font-semibold shrink-0">
                                            <svg viewBox="0 0 24 24" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <polyline points="1 4 1 10 7 10"/>
                                                <path d="M3.5 15a9 9 0 1 0 2.1-9.4L1 10"/>
                                            </svg>
                                            Ongedaan
                                        </button>
                                        <span x-show="a.undone" class="text-gray-400 shrink-0">Teruggedraaid</span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
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

        {{-- Input --}}
        <form @submit.prevent="send" class="p-3 border-t border-gray-100 bg-white shrink-0">
            <div class="flex items-end gap-2">
                <textarea x-model="input" rows="1"
                          @keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); send(); }"
                          placeholder="Typ een opdracht of vraag..."
                          class="flex-1 resize-none rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-800 focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark/40 focus:outline-none max-h-28"></textarea>
                <button type="submit" :disabled="sending || !input.trim()"
                        class="cursor-pointer w-10 h-10 shrink-0 flex items-center justify-center rounded-xl bg-eazy-dark hover:bg-eazy-darker text-white disabled:opacity-40 disabled:cursor-not-allowed transition"
                        aria-label="Versturen">
                    <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 2 11 13"/>
                        <path d="M22 2 15 22l-4-9-9-4 20-7z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    {{-- Launcher --}}
    <button type="button" @click="open = !open"
            class="cursor-pointer w-14 h-14 flex items-center justify-center rounded-full bg-gradient-to-br from-eazy-dark to-eazy-darker text-white shadow-lg shadow-eazy-dark/30 hover:scale-105 active:scale-95 transition"
            :aria-label="open ? 'AI-collega sluiten' : 'AI-collega openen'">
        <svg x-show="!open" viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 11.5a8.38 8.38 0 0 1-8.5 8.5 8.5 8.5 0 0 1-3.8-.9L3 21l1.9-5.7A8.38 8.38 0 0 1 4 11.5 8.5 8.5 0 0 1 12.5 3 8.38 8.38 0 0 1 21 11.5z"/>
        </svg>
        <svg x-show="open" x-cloak viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
    </button>
</div>

<script>
    window.eazyAssistant = function (cfg) {
        return {
            open: false,
            sending: false,
            input: '',
            conversationId: null,
            messages: [],

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
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': cfg.csrf,
                        },
                        body: JSON.stringify({ message: text, conversation_id: this.conversationId }),
                    });
                    const data = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        this.messages.push({ role: 'assistant', text: data.error || 'Er ging iets mis.' });
                    } else {
                        this.conversationId = data.conversation_id;
                        this.messages.push({
                            role: 'assistant',
                            text: data.reply,
                            activities: (data.activities || []).map((a) => ({ ...a, undone: false })),
                        });
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
                    const res = await fetch(`${cfg.undoBase}/${activity.id}/undo`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
                    });
                    if (res.ok) activity.undone = true;
                } catch (e) { /* keep the chip; user can retry */ }
            },

            scrollDown() {
                this.$nextTick(() => {
                    const el = this.$refs.scroll;
                    if (el) el.scrollTop = el.scrollHeight;
                });
            },
        };
    };
</script>
