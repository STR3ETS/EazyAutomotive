<x-guest-layout>
    {{-- Heading --}}
    <div class="mb-6">
        <div class="w-12 h-12 rounded-2xl bg-eazy-50 flex items-center justify-center mb-4">
            <i class="fa-solid fa-lock-open text-eazy-dark text-lg" aria-hidden="true"></i>
        </div>
        <h1 class="text-2xl font-black text-eazy-darker tracking-tight">Nieuw wachtwoord</h1>
        <p class="text-sm text-muted mt-1">Kies een nieuw, sterk wachtwoord voor je account.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div>
            <label for="email" class="block text-[11px] font-bold text-eazy-darker uppercase tracking-wider mb-1.5">E-mailadres</label>
            <div class="relative">
                <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm" aria-hidden="true"></i>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                    class="block w-full pl-11 pr-4 py-3 rounded-xl border border-eazy-darker/10 bg-white text-sm text-eazy-darker focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark placeholder:text-muted/60"
                    placeholder="naam@bedrijf.nl">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Password --}}
        <div x-data="{ show: false }">
            <label for="password" class="block text-[11px] font-bold text-eazy-darker uppercase tracking-wider mb-1.5">Nieuw wachtwoord</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm" aria-hidden="true"></i>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    :type="show ? 'text' : 'password'"
                    class="block w-full pl-11 pr-11 py-3 rounded-xl border border-eazy-darker/10 bg-white text-sm text-eazy-darker focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark placeholder:text-muted/60"
                    placeholder="Minimaal 8 tekens">
                <button type="button" x-on:click="show = !show" :aria-label="show ? 'Wachtwoord verbergen' : 'Wachtwoord tonen'"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center text-muted hover:text-eazy-dark transition">
                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div x-data="{ show: false }">
            <label for="password_confirmation" class="block text-[11px] font-bold text-eazy-darker uppercase tracking-wider mb-1.5">Wachtwoord bevestigen</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm" aria-hidden="true"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    :type="show ? 'text' : 'password'"
                    class="block w-full pl-11 pr-11 py-3 rounded-xl border border-eazy-darker/10 bg-white text-sm text-eazy-darker focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark placeholder:text-muted/60"
                    placeholder="Herhaal je wachtwoord">
                <button type="button" x-on:click="show = !show" :aria-label="show ? 'Wachtwoord verbergen' : 'Wachtwoord tonen'"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 flex items-center justify-center text-muted hover:text-eazy-dark transition">
                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="btn-shine w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-eazy-dark text-white rounded-full text-sm font-bold hover:bg-eazy-darker shadow-lg shadow-eazy/25 transition-all">
            <i class="fa-solid fa-check text-xs" aria-hidden="true"></i> Wachtwoord opslaan
        </button>

        <div class="pt-5 border-t border-eazy-darker/5 text-center">
            <a class="inline-flex items-center gap-1.5 text-sm text-eazy-dark hover:text-eazy-darker font-bold transition" href="{{ route('login') }}">
                <i class="fa-solid fa-arrow-left text-[10px]" aria-hidden="true"></i> Terug naar inloggen
            </a>
        </div>
    </form>
</x-guest-layout>
