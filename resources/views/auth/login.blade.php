<x-guest-layout>
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-eazy-50 flex items-center justify-center">
                <i class="fa-solid fa-right-to-bracket text-eazy"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-[#215558]">Welkom terug</h2>
                <p class="text-xs text-[#215558]/40 font-medium">Log in op je EazyAutomotive account</p>
            </div>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">E-mailadres</label>
            <div class="relative">
                <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="block w-full pl-10 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm text-[#215558] focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25"
                    placeholder="naam@bedrijf.nl">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Wachtwoord</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="block w-full pl-10 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm text-[#215558] focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25"
                    placeholder="Je wachtwoord">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded-md border-[#215558]/15 text-eazy shadow-sm focus:ring-eazy" name="remember">
                <span class="ms-2 text-sm text-[#215558]/60 font-medium">Onthoud mij</span>
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="cursor-pointer w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 hover:shadow-eazy/30 transition-all">
                <i class="fa-solid fa-right-to-bracket text-xs"></i> Inloggen
            </button>
        </div>

        <div class="flex items-center justify-between mt-5 pt-5 border-t border-[#215558]/5">
            @if (Route::has('password.request'))
                <a class="text-sm text-eazy hover:text-eazy-dark font-medium transition" href="{{ route('password.request') }}">
                    Wachtwoord vergeten?
                </a>
            @endif

            <a class="text-sm text-[#215558]/40 hover:text-[#215558]/70 font-medium transition" href="{{ route('register') }}">
                Nog geen account?
            </a>
        </div>
    </form>
</x-guest-layout>
