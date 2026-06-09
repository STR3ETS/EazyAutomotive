<x-guest-layout>
    {{-- Heading --}}
    <div class="mb-6">
        <div class="w-12 h-12 rounded-2xl bg-eazy-50 flex items-center justify-center mb-4">
            <i class="fa-solid fa-key text-eazy-dark text-lg" aria-hidden="true"></i>
        </div>
        <h1 class="text-2xl font-black text-eazy-darker tracking-tight">Wachtwoord vergeten?</h1>
        <p class="text-sm text-muted mt-1 leading-relaxed">Geen probleem. Vul je e-mailadres in en we sturen je een link om een nieuw wachtwoord in te stellen.</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-5 flex items-center gap-2.5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-[11px] font-bold text-eazy-darker uppercase tracking-wider mb-1.5">E-mailadres</label>
            <div class="relative">
                <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm" aria-hidden="true"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="block w-full pl-11 pr-4 py-3 rounded-xl border border-eazy-darker/10 bg-white text-sm text-eazy-darker focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark placeholder:text-muted/60"
                    placeholder="naam@bedrijf.nl">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="btn-shine w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-eazy-dark text-white rounded-full text-sm font-bold hover:bg-eazy-darker shadow-lg shadow-eazy/25 transition-all">
            <i class="fa-solid fa-paper-plane text-xs" aria-hidden="true"></i> Verstuur herstellink
        </button>

        <div class="pt-5 border-t border-eazy-darker/5 text-center">
            <a class="inline-flex items-center gap-1.5 text-sm text-eazy-dark hover:text-eazy-darker font-bold transition" href="{{ route('login') }}">
                <i class="fa-solid fa-arrow-left text-[10px]" aria-hidden="true"></i> Terug naar inloggen
            </a>
        </div>
    </form>
</x-guest-layout>
