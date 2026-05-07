<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Welkom terug</h2>
        <p class="text-sm text-gray-500 mt-1">Log in op je EazyAutomotive account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="E-mailadres" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Wachtwoord" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-eazy shadow-sm focus:ring-eazy" name="remember">
                <span class="ms-2 text-sm text-gray-600">Onthoud mij</span>
            </label>
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3 text-sm">
                Inloggen
            </x-primary-button>
        </div>

        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm text-eazy hover:text-eazy-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-eazy" href="{{ route('password.request') }}">
                    Wachtwoord vergeten?
                </a>
            @endif

            <a class="text-sm text-gray-500 hover:text-gray-700" href="{{ route('register') }}">
                Nog geen account?
            </a>
        </div>
    </form>
</x-guest-layout>
