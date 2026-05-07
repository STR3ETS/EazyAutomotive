<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanySettingsController extends Controller
{
    public function edit(Request $request)
    {
        $company = $request->user()->company;

        return view('company.settings', compact('company'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'website' => 'nullable|url|max:255',
            'kvk_number' => 'nullable|string|max:20',
            'btw_number' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
        ]);

        $company = $request->user()->company;

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $logo = $request->file('logo');
            $extension = $logo->getClientOriginalExtension() ?: ($logo->guessExtension() ?: 'png');
            $filename = Str::random(40) . '.' . $extension;
            Storage::disk('public')->put("logos/{$filename}", file_get_contents($logo->getPathname()));
            $validated['logo_path'] = "logos/{$filename}";
        }
        unset($validated['logo']);

        $company->update($validated);

        return back()->with('success', 'Bedrijfsinstellingen bijgewerkt!');
    }

    public function regenerateApiKey(Request $request)
    {
        $company = $request->user()->company;
        $company->update(['api_key' => Str::random(64)]);

        return back()->with('success', 'API key opnieuw gegenereerd. Vergeet niet je embed code bij te werken!');
    }

    public function embedCode(Request $request)
    {
        $company = $request->user()->company;

        return view('company.embed', compact('company'));
    }

    public function updateEmbedSettings(Request $request)
    {
        $validated = $request->validate([
            // Layout
            'columns' => 'nullable|integer|min:1|max:4',
            'image_position' => 'nullable|in:top,bottom',
            'image_height' => 'nullable|integer|min:100|max:350',

            // Card styling
            'primary_color' => 'nullable|string|max:7',
            'card_bg_color' => 'nullable|string|max:7',
            'card_border_radius' => 'nullable|integer|min:0|max:30',
            'card_padding' => 'nullable|integer|min:0|max:40',
            'card_border_color' => 'nullable|string|max:7',
            'card_border_width' => 'nullable|integer|min:0|max:4',
            'card_shadow' => 'nullable|in:none,sm,md,lg',
            'hover_effect' => 'nullable|in:lift,shadow,scale,glow,none',

            // Typography
            'font_family' => 'nullable|string|max:30',
            'title_size' => 'nullable|integer|min:12|max:28',
            'title_color' => 'nullable|string|max:7',
            'price_size' => 'nullable|integer|min:12|max:36',
            'currency' => 'nullable|in:EUR,USD,GBP,none',

            // Label styling
            'label_style' => 'nullable|in:badge,outline,icon-text,pill',
            'label_bg_color' => 'nullable|string|max:7',
            'label_text_color' => 'nullable|string|max:7',
            'label_radius' => 'nullable|integer|min:0|max:20',
            'label_padding_x' => 'nullable|integer|min:0|max:24',
            'label_padding_y' => 'nullable|integer|min:0|max:16',
            'label_gap' => 'nullable|integer|min:0|max:20',

            // Visibility
            'show_price' => 'nullable|boolean',
            'show_km' => 'nullable|boolean',
            'show_fuel' => 'nullable|boolean',

            // Detail page overrides
            'detail_custom' => 'nullable|boolean',
            'detail_bg_color' => 'nullable|string|max:7',
            'detail_border_radius' => 'nullable|integer|min:0|max:30',
            'detail_padding' => 'nullable|integer|min:0|max:60',
            'detail_title_size' => 'nullable|integer|min:14|max:36',
            'detail_title_color' => 'nullable|string|max:7',
            'detail_price_size' => 'nullable|integer|min:14|max:42',
            'detail_gallery_height' => 'nullable|integer|min:150|max:500',
            'detail_spec_columns' => 'nullable|integer|min:1|max:3',
            'detail_overlay_opacity' => 'nullable|integer|min:20|max:90',
        ]);

        $validated['show_price'] = $request->boolean('show_price');
        $validated['show_km'] = $request->boolean('show_km');
        $validated['show_fuel'] = $request->boolean('show_fuel');
        $validated['detail_custom'] = $request->boolean('detail_custom');

        $company = $request->user()->company;
        $company->update(['embed_settings' => $validated]);

        return back()->with('success', 'Widget instellingen opgeslagen!');
    }
}
