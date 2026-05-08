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

        <!-- FontAwesome -->
        <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-[#ebf2f2]">

            {{-- Mobile overlay --}}
            <div x-show="sidebarOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm z-20 lg:hidden"
                 style="display: none;"></div>

            {{-- Sidebar --}}
            @include('layouts.navigation')

            {{-- Main Content --}}
            <div class="lg:pl-[288px] min-h-screen flex flex-col">

                {{-- Mobile top bar --}}
                <div class="lg:hidden sticky top-0 z-10 flex items-center gap-3 bg-white/90 backdrop-blur-md border-b border-gray-100 px-4 py-3">
                    <button @click="sidebarOpen = true" class="cursor-pointer flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5">
                        <span class="text-lg font-bold text-eazy-darker">Eazy</span>
                        <span class="text-lg font-light text-eazy">Automotive</span>
                    </a>
                </div>

                {{-- Page Content --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
