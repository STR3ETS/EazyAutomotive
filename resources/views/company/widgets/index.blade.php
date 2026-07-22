<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Header --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-eazy-50 flex items-center justify-center">
                    <i class="fa-solid fa-shapes text-eazy"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#215558]">Widgets</h1>
                    <p class="text-sm text-[#215558] opacity-50">Kies een widget om het ontwerp aan te passen en de code voor je website te krijgen.</p>
                </div>
            </div>

            {{-- Widget-kaarten --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
                @foreach($widgets as $w)
                    <a href="{{ route($w['route']) }}" class="group bg-white rounded-2xl border border-[#215558]/10 p-6 hover:border-eazy/40 hover:shadow-lg hover:shadow-eazy/5 transition-all">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl {{ $w['kleur'] }} flex items-center justify-center shrink-0">
                                <i class="fa-solid {{ $w['icon'] }} text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-[#215558]">{{ $w['naam'] }}</h3>
                                    <i class="fa-solid fa-arrow-right text-xs text-[#215558]/30 group-hover:text-eazy group-hover:translate-x-0.5 transition-all"></i>
                                </div>
                                <p class="text-[13px] text-[#215558] opacity-50 mt-1 leading-relaxed">{{ $w['omschrijving'] }}</p>
                                <div class="mt-3 flex items-center gap-3 text-[11px] font-bold text-[#215558]/50">
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-sliders text-[10px]"></i> Ontwerp</span>
                                    <span class="inline-flex items-center gap-1"><i class="fa-solid fa-code text-[10px]"></i> Insluitcode</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- API-sleutel --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6">
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-[#215558]/5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fa-solid fa-key text-amber-500 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#215558]">API-sleutel</h3>
                        <p class="text-xs text-[#215558] opacity-50">Deze sleutel koppelt alle widgets aan jouw account</p>
                    </div>
                </div>
                <code class="block bg-gray-50 px-4 py-3 rounded-xl text-xs font-mono text-gray-500 break-all mb-4 border border-gray-100">{{ $company->api_key }}</code>
                <form method="POST" action="{{ route('settings.regenerate-key') }}" onsubmit="return confirm('Weet je het zeker? Als je een nieuwe sleutel genereert, stoppen alle widgets op je website tot je de codes vervangt.')">
                    @csrf
                    <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-full text-xs font-semibold hover:bg-red-100 transition">
                        <i class="fa-solid fa-rotate text-xs"></i> Nieuwe sleutel genereren
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
