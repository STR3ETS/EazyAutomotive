<x-app-layout>
    <style>
        .kenteken-plate {
            position: relative;
            display: flex;
            align-items: stretch;
            background: #f5c211;
            border-radius: 6px;
            overflow: hidden;
            border: 3px solid #d4a40a;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12), inset 0 1px 0 rgba(255,255,255,0.15);
            width: 100%;
            max-width: 420px;
            height: 64px;
        }
        /* Blue EU strip */
        .kenteken-strip {
            width: 42px;
            background: #003399;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
            flex-shrink: 0;
            position: relative;
        }
        /* EU circle of 12 stars */
        .kenteken-eu-stars {
            width: 24px;
            height: 24px;
            position: relative;
        }
        .kenteken-eu-stars .eu-star {
            position: absolute;
            color: #f5c211;
            font-size: 5px;
            line-height: 1;
        }
        .kenteken-strip-nl {
            font-size: 13px;
            font-weight: 900;
            color: #fff;
            line-height: 1;
            letter-spacing: 0.5px;
            margin-top: 1px;
        }
        /* Input field */
        .kenteken-input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            font-family: 'Arial Black', 'Arial', 'Helvetica Neue', sans-serif;
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 4px;
            color: #000;
            text-transform: uppercase;
            padding: 0 20px;
            text-align: center;
            min-width: 0;
            line-height: 64px;
        }
        .kenteken-input::placeholder {
            color: rgba(0,0,0,0.18);
            letter-spacing: 3px;
        }
        .kenteken-input:focus {
            outline: none;
        }
        .kenteken-plate:focus-within {
            border-color: #b88d08;
            box-shadow: 0 0 0 4px rgba(245,194,17,0.35), 0 2px 8px rgba(0,0,0,0.12);
        }
    </style>

    <div class="py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-xl">
                    <i class="fa-solid fa-circle-exclamation text-lg mt-0.5"></i>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="mb-6">
                <a href="{{ route('cars.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#215558] opacity-50 hover:opacity-100 transition mb-3">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i> Terug naar overzicht
                </a>
                <h1 class="text-2xl font-black text-[#215558]">Nieuwe auto toevoegen</h1>
                <p class="text-sm text-[#215558] opacity-50 font-medium mt-0.5">Begin met het opzoeken van het kenteken</p>
            </div>

            {{-- Kenteken Lookup Card --}}
            <div class="bg-white rounded-2xl border border-[#215558]/10 p-6 relative overflow-hidden">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-[#215558]/5">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                        <i class="fa-solid fa-magnifying-glass text-amber-500 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#215558]">Kenteken opzoeken</h3>
                        <p class="text-xs text-[#215558] opacity-50">Voer het kenteken in om de voertuiggegevens automatisch op te halen via de RDW</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('cars.lookup') }}">
                    @csrf

                    <div class="flex flex-col gap-4 items-start">
                        <div class="flex-1">
                            <label for="kenteken" class="block text-[11px] font-bold text-[#215558] opacity-80 uppercase tracking-wider mb-2">Kenteken</label>
                            <div class="kenteken-plate">
                                <div class="kenteken-strip">
                                    <div class="kenteken-eu-stars">
                                        {{-- 12 EU stars in a circle --}}
                                        @for($i = 0; $i < 12; $i++)
                                            @php
                                                $angle = ($i * 30) - 90;
                                                $x = 12 + 9 * cos(deg2rad($angle)) - 2.5;
                                                $y = 12 + 9 * sin(deg2rad($angle)) - 2.5;
                                            @endphp
                                            <span class="eu-star" style="left:{{ $x }}px;top:{{ $y }}px;">★</span>
                                        @endfor
                                    </div>
                                    <span class="kenteken-strip-nl">NL</span>
                                </div>
                                <input type="text" name="kenteken" id="kenteken" value="{{ old('kenteken') }}"
                                    placeholder="XX-999-X"
                                    class="kenteken-input"
                                    maxlength="9" required autocomplete="off">
                            </div>
                            @error('kenteken')
                                <p class="mt-2 text-xs text-red-600 font-medium"><i class="fa-solid fa-triangle-exclamation mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <button type="submit" class="cursor-pointer inline-flex items-center gap-2 px-6 py-3 bg-eazy text-white rounded-full text-sm font-bold hover:bg-eazy-dark shadow-lg shadow-eazy/20 hover:shadow-eazy/30 transition-all">
                                <i class="fa-solid fa-magnifying-glass text-xs"></i> Opzoeken
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Helper text --}}
                <div class="mt-5 pt-4 border-t border-[#215558]/5">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fa-solid fa-circle-info text-blue-400 text-xs"></i>
                        </div>
                        <p class="text-xs text-[#215558] opacity-50 leading-relaxed">We zoeken het kenteken op in het RDW-register en vullen automatisch alle gegevens in: merk, model, bouwjaar, brandstof, kleur en meer. Je hoeft alleen nog de prijs en foto's toe te voegen.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
