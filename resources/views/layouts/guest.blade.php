<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;600&family=Inter+Tight:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- FontAwesome -->
        <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#ebf2f2] relative overflow-hidden">

            {{-- Subtle background decoration --}}
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-eazy/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-eazy/5 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10 mb-6 text-center">
                <a href="/" class="inline-flex items-center gap-2">
                    <span class="text-3xl font-black text-[#215558]">Eazy</span>
                    <span class="text-3xl font-light text-eazy">Automotive</span>
                </a>
                <p class="text-[#215558]/30 text-sm font-medium mt-1">by Eazyonline</p>
            </div>

            <div class="relative z-10 w-full sm:max-w-md px-8 py-8 bg-white shadow-xl shadow-[#215558]/5 overflow-hidden sm:rounded-2xl border border-[#215558]/10">
                {{ $slot }}
            </div>

            <p class="relative z-10 mt-8 text-[#215558]/25 text-xs font-medium">&copy; {{ date('Y') }} Eazyonline. Alle rechten voorbehouden.</p>
        </div>
    </body>
</html>
