<x-guest-layout>
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fa-solid fa-user-plus text-emerald-500"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-[#215558]">Account aanmaken</h2>
                <p class="text-xs text-[#215558]/40 font-medium">Start met het beheren van je autovoorraad</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Company Name -->
        <div>
            <label for="company_name" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Bedrijfsnaam</label>
            <div class="relative">
                <i class="fa-solid fa-building absolute left-3.5 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}" required autofocus autocomplete="organization"
                    class="block w-full pl-10 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm text-[#215558] focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25"
                    placeholder="bijv. Auto van Dijk">
            </div>
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="mt-4">
            <label for="name" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Jouw naam</label>
            <div class="relative">
                <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                    class="block w-full pl-10 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm text-[#215558] focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25"
                    placeholder="Je volledige naam">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <label for="email" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">E-mailadres</label>
            <div class="relative">
                <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
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
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="block w-full pl-10 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm text-[#215558] focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25"
                    placeholder="Minimaal 8 tekens">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-1.5">Wachtwoord bevestigen</label>
            <div class="relative">
                <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-[#215558]/25 text-sm"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="block w-full pl-10 pr-4 py-2.5 rounded-full border-[#215558]/10 text-sm text-[#215558] focus:border-eazy focus:ring-eazy placeholder:text-[#215558]/25"
                    placeholder="Herhaal je wachtwoord">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <button type="submit" class="cursor-pointer w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 hover:shadow-eazy/30 transition-all">
                <i class="fa-solid fa-rocket text-xs"></i> Gratis registreren
            </button>
        </div>

        <div class="mt-5 pt-5 border-t border-[#215558]/5 text-center">
            <span class="text-sm text-[#215558]/40 font-medium">Al een account?</span>
            <a class="text-sm text-eazy hover:text-eazy-dark font-bold ml-1 transition" href="{{ route('login') }}">
                Inloggen
            </a>
        </div>
    </form>
</x-guest-layout>
