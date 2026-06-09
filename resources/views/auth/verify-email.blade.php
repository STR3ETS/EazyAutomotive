<x-guest-layout>
    {{-- Heading --}}
    <div class="mb-6">
        <div class="w-12 h-12 rounded-2xl bg-eazy-50 flex items-center justify-center mb-4">
            <i class="fa-solid fa-envelope-open-text text-eazy-dark text-lg" aria-hidden="true"></i>
        </div>
        <h1 class="text-2xl font-black text-eazy-darker tracking-tight">Bevestig je e-mailadres</h1>
        <p class="text-sm text-muted mt-1 leading-relaxed">Bedankt voor je aanmelding! Klik op de link in de e-mail die we je zojuist hebben gestuurd. Geen e-mail ontvangen? Dan sturen we hem graag opnieuw.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 flex items-center gap-2.5 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm font-medium">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
            <span>Er is een nieuwe bevestigingslink verstuurd naar je e-mailadres.</span>
        </div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-shine w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-eazy-dark text-white rounded-full text-sm font-bold hover:bg-eazy-darker shadow-lg shadow-eazy/25 transition-all">
                <i class="fa-solid fa-paper-plane text-xs" aria-hidden="true"></i> Stuur opnieuw
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-muted hover:text-eazy-dark transition">
                <i class="fa-solid fa-right-from-bracket text-xs" aria-hidden="true"></i> Uitloggen
            </button>
        </form>
    </div>
</x-guest-layout>
