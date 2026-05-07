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
        <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;600&family=Inter+Tight:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-eazy-darker via-eazy-700 to-eazy-dark">
            <div class="mb-6">
                <a href="/" class="flex items-center gap-2">
                    <span class="text-3xl font-bold text-white">Eazy</span>
                    <span class="text-3xl font-light text-eazy-300">Automotive</span>
                </a>
                <p class="text-center text-eazy-300/70 text-sm mt-1">by Eazyonline</p>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white shadow-xl overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>

            <p class="mt-8 text-eazy-300/50 text-xs">&copy; {{ date('Y') }} Eazyonline. Alle rechten voorbehouden.</p>
        </div>
    </body>
</html>
