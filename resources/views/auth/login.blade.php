<x-guest-layout>
    {{-- Heading --}}
    <div class="mb-7">
        <span class="font-hand text-eazy-dark text-xl">Fijn dat je er weer bent</span>
        <h1 class="text-2xl font-black text-eazy-darker tracking-tight">Welkom terug</h1>
        <p class="text-sm text-muted mt-1">Log in op je EazyAutomotive-account.</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-5 flex items-center gap-2.5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
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

        {{-- Password --}}
        <div x-data="{ show: false }">
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-[11px] font-bold text-eazy-darker uppercase tracking-wider">Wachtwoord</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-eazy-dark hover:text-eazy-darker transition" href="{{ route('password.request') }}">Wachtwoord vergeten?</a>
                @endif
            </div>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm" aria-hidden="true"></i>
                <input id="password" type="password" name="password" required autocomplete="current-password"
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

        {{-- Remember Me --}}
        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
            <input id="remember_me" type="checkbox" name="remember" class="rounded-md border-eazy-darker/20 text-eazy-dark shadow-sm focus:ring-eazy-dark">
            <span class="text-sm text-muted font-medium">Onthoud mij op dit apparaat</span>
        </label>

        <button type="submit" class="btn-shine w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-eazy-dark text-white rounded-full text-sm font-bold hover:bg-eazy-darker shadow-lg shadow-eazy/25 transition-all">
            <i class="fa-solid fa-right-to-bracket text-xs" aria-hidden="true"></i> Inloggen
        </button>

        <div class="pt-5 border-t border-eazy-darker/5 text-center">
            <span class="text-sm text-muted">Nog geen account?</span>
            <a class="text-sm text-eazy-dark hover:text-eazy-darker font-bold ml-1 transition" href="{{ route('register') }}">Gratis starten</a>
        </div>
    </form>
</x-guest-layout>
