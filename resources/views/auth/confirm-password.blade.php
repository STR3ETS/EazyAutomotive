<x-guest-layout>
    {{-- Heading --}}
    <div class="mb-6">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center mb-4">
            <i class="fa-solid fa-shield-halved text-amber-500 text-lg" aria-hidden="true"></i>
        </div>
        <h1 class="text-2xl font-black text-eazy-darker tracking-tight">Bevestig je wachtwoord</h1>
        <p class="text-sm text-muted mt-1 leading-relaxed">Dit is een beveiligd gedeelte. Bevestig je wachtwoord om verder te gaan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div x-data="{ show: false }">
            <label for="password" class="block text-[11px] font-bold text-eazy-darker uppercase tracking-wider mb-1.5">Wachtwoord</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm" aria-hidden="true"></i>
                <input id="password" type="password" name="password" required autocomplete="current-password" autofocus
                    :type="show ? 'text' : 'password'"
                    class="block w-full pl-11 pr-11 py-3 rounded-xl border border-eazy-darker/10 bg-white text-sm text-eazy-darker focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark placeholder:text-muted/60"
                    placeholder="Je wachtwoord">
                <button type="button" x-on:click="show = !show" :aria-label="show ? 'Wachtwoord verbergen' : 'Wachtwoord tonen'"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center text-muted hover:text-eazy-dark transition">
                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="btn-shine w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-eazy-dark text-white rounded-full text-sm font-bold hover:bg-eazy-darker shadow-lg shadow-eazy/25 transition-all">
            <i class="fa-solid fa-check text-xs" aria-hidden="true"></i> Bevestigen
        </button>
    </form>
</x-guest-layout>
