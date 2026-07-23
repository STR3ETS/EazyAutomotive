<x-guest-layout>
    @if($invalid)
        {{-- Ongeldige of verlopen uitnodiging --}}
        <div class="text-center py-4">
            <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-link-slash text-red-400 text-xl" aria-hidden="true"></i>
            </div>
            <h1 class="text-2xl font-black text-eazy-darker tracking-tight">Uitnodiging niet geldig</h1>
            <p class="text-sm text-muted mt-2 leading-relaxed">
                Deze uitnodigingslink is verlopen of al gebruikt. Vraag je beheerder om een nieuwe uitnodiging.
            </p>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-3 bg-eazy-dark text-white rounded-full text-sm font-bold hover:bg-eazy-darker transition">
                Naar inloggen <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        </div>
    @else
        {{-- Heading --}}
        <div class="mb-7">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-eazy-50 rounded-full text-[11px] font-bold text-eazy-dark uppercase tracking-wider mb-3">
                <i class="fa-solid fa-user-plus text-[10px]" aria-hidden="true"></i> Uitnodiging voor {{ $invitation->company->name }}
            </div>
            <h1 class="text-2xl font-black text-eazy-darker tracking-tight">Word lid van het team</h1>
            <p class="text-sm text-muted mt-1">
                Je bent uitgenodigd als <strong class="text-eazy-darker">{{ \App\Support\Roles::label($invitation->role) }}</strong>. Stel je wachtwoord in om te starten.
            </p>
        </div>

        <form method="POST" action="{{ route('invitation.accept', $invitation->token) }}" class="space-y-4">
            @csrf

            {{-- E-mail (vast) --}}
            <div>
                <label class="block text-[11px] font-bold text-eazy-darker uppercase tracking-wider mb-1.5">E-mailadres</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm" aria-hidden="true"></i>
                    <input type="email" value="{{ $invitation->email }}" readonly
                        class="block w-full pl-11 pr-4 py-3 rounded-xl border border-eazy-darker/10 bg-eazy-50/40 text-sm text-eazy-darker/70 cursor-not-allowed">
                </div>
            </div>

            {{-- Naam --}}
            <div>
                <label for="name" class="block text-[11px] font-bold text-eazy-darker uppercase tracking-wider mb-1.5">Jouw naam</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm" aria-hidden="true"></i>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                        class="block w-full pl-11 pr-4 py-3 rounded-xl border border-eazy-darker/10 bg-white text-sm text-eazy-darker focus:border-eazy-dark focus:ring-2 focus:ring-eazy-dark placeholder:text-muted/60"
                        placeholder="Je volledige naam">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            {{-- Wachtwoord --}}
            <div x-data="{ show: false }">
                <label for="password" class="block text-[11px] font-bold text-eazy-darker uppercase tracking-wider mb-1.5">Wachtwoord</label>
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

            {{-- Wachtwoord bevestigen --}}
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
                Account aanmaken en starten <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
            </button>
        </form>
    @endif
</x-guest-layout>
