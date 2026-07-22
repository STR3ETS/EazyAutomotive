<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Widgets-hub: één plek waar de dealer per widget het ontwerp doet én de
 * insluitcode krijgt. Vervangt de losse "Ontwerpen" en "Integratie" pagina's.
 *
 * Widgets:
 *   voorraad  - het autoaanbod (uitgebreid ontwerpbaar, met thema-presets)
 *   contact   - contact / inruil / financiering formulier
 *   proefrit  - proefrit aanvragen
 *   taxatie   - inruil-taxatie (kenteken -> waarde -> lead)
 *
 * Uiterlijk van alle widgets komt uit company->embed_settings (kleur, font,
 * hoeken). De voorraadwidget heeft daarnaast eigen layout-opties.
 */
class WidgetController extends Controller
{
    /**
     * Kant-en-klare thema's; elk zet in één klik de belangrijkste stijlopties.
     * Bewust markant verschillend in kleur, hoeken, schaduw en labelstijl.
     */
    public const THEMES = [
        ['id' => 'modern', 'naam' => 'Strak modern', 'primary_color' => '#0F9B9F', 'card_bg_color' => '#ffffff', 'card_border_radius' => 14, 'card_shadow' => 'md', 'hover_effect' => 'lift', 'label_style' => 'pill', 'font_family' => 'Inter'],
        ['id' => 'rond', 'naam' => 'Zacht & rond', 'primary_color' => '#7c6bf5', 'card_bg_color' => '#ffffff', 'card_border_radius' => 26, 'card_shadow' => 'lg', 'hover_effect' => 'scale', 'label_style' => 'badge', 'font_family' => 'Poppins'],
        ['id' => 'strak', 'naam' => 'Strak minimal', 'primary_color' => '#111827', 'card_bg_color' => '#ffffff', 'card_border_radius' => 0, 'card_shadow' => 'none', 'hover_effect' => 'none', 'label_style' => 'outline', 'font_family' => 'system'],
        ['id' => 'warm', 'naam' => 'Warm & zonnig', 'primary_color' => '#f97316', 'card_bg_color' => '#fff7ed', 'card_border_radius' => 18, 'card_shadow' => 'sm', 'hover_effect' => 'shadow', 'label_style' => 'pill', 'font_family' => 'Lato'],
        ['id' => 'premium', 'naam' => 'Donker premium', 'primary_color' => '#2dd4bf', 'card_bg_color' => '#0f172a', 'card_border_radius' => 12, 'card_shadow' => 'lg', 'hover_effect' => 'glow', 'label_style' => 'pill', 'font_family' => 'Montserrat'],
        ['id' => 'blauw', 'naam' => 'Fris blauw', 'primary_color' => '#2563eb', 'card_bg_color' => '#ffffff', 'card_border_radius' => 8, 'card_shadow' => 'md', 'hover_effect' => 'lift', 'label_style' => 'badge', 'font_family' => 'Roboto'],
    ];

    public function index(Request $request)
    {
        return view('company.widgets.index', [
            'company' => $request->user()->company,
            'widgets' => $this->widgetMeta(),
        ]);
    }

    /** Globale huisstijl die voor alle widgets geldt. */
    public function theme(Request $request)
    {
        return view('company.widgets.theme', [
            'company' => $request->user()->company,
            'themes' => self::THEMES,
        ]);
    }

    public function voorraad(Request $request)
    {
        return view('company.widgets.voorraad', [
            'company' => $request->user()->company,
        ]);
    }

    public function contact(Request $request)
    {
        return view('company.widgets.contact', ['company' => $request->user()->company]);
    }

    public function proefrit(Request $request)
    {
        return view('company.widgets.proefrit', ['company' => $request->user()->company]);
    }

    public function taxatie(Request $request)
    {
        return view('company.widgets.taxatie', ['company' => $request->user()->company]);
    }

    /** Opslaan van de globale huisstijl (geldt voor alle widgets). */
    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'card_bg_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'card_border_radius' => 'nullable|integer|min:0|max:30',
            'card_shadow' => 'nullable|in:none,sm,md,lg',
            'hover_effect' => 'nullable|in:lift,shadow,scale,glow,none',
            'font_family' => 'nullable|string|max:30',
            'label_style' => 'nullable|in:badge,outline,icon-text,pill',
        ]);

        // Leesbare tekstkleuren afleiden uit de gekozen kaartachtergrond, zodat
        // een donker thema wél leesbaar is (lichte tekst op donkere kaart).
        $light = $this->isLightColor($validated['card_bg_color'] ?? '#ffffff');
        $validated['title_color'] = $light ? '#111827' : '#f8fafc';
        $validated['label_bg_color'] = $light ? '#f3f4f6' : '#1f2a37';
        $validated['label_text_color'] = $light ? '#4b5563' : '#d1d5db';
        $validated['card_border_color'] = $light ? '#e5e7eb' : '#243040';
        $validated['detail_title_color'] = $validated['title_color'];
        $validated['detail_desc_color'] = $light ? '#6b7280' : '#cbd5e1';

        $this->mergeSettings($request, $validated);

        return redirect()->route('widgets.theme')->with('success', 'Huisstijl opgeslagen! Dit geldt voor al je widgets.');
    }

    /** Is deze achtergrondkleur licht genoeg voor donkere tekst? */
    private function isLightColor(string $hex): bool
    {
        if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $hex)) {
            return true;
        }

        $r = hexdec(substr($hex, 1, 2));
        $g = hexdec(substr($hex, 3, 2));
        $b = hexdec(substr($hex, 5, 2));

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 150;
    }

    /** Opslaan van de voorraadwidget: alleen layout en zichtbaarheid. */
    public function updateVoorraad(Request $request)
    {
        $validated = $request->validate([
            'columns' => 'nullable|integer|min:1|max:4',
            'image_position' => 'nullable|in:top,bottom',
            'image_height' => 'nullable|integer|min:120|max:340',
        ]);

        $validated['show_price'] = $request->boolean('show_price');
        $validated['show_km'] = $request->boolean('show_km');
        $validated['show_fuel'] = $request->boolean('show_fuel');

        $this->mergeSettings($request, $validated);

        return redirect()->route('widgets.voorraad')->with('success', 'Voorraad-widget opgeslagen!');
    }

    /** Opslaan van de proefritwidget (teksten + zichtbaarheid). */
    public function updateProefrit(Request $request)
    {
        $validated = $request->validate([
            'proefrit_titel' => 'nullable|string|max:80',
            'proefrit_intro' => 'nullable|string|max:300',
            'proefrit_knop' => 'nullable|string|max:50',
            'proefrit_bedankt' => 'nullable|string|max:300',
            'proefrit_privacy_tekst' => 'nullable|string|max:300',
        ]);

        $validated['proefrit_toon_datum'] = $request->boolean('proefrit_toon_datum');
        $validated['proefrit_toon_bericht'] = $request->boolean('proefrit_toon_bericht');
        $validated['proefrit_auto_verplicht'] = $request->boolean('proefrit_auto_verplicht');
        $validated['proefrit_privacy'] = $request->boolean('proefrit_privacy');

        $this->mergeSettings($request, $validated);

        return redirect()->route('widgets.proefrit')->with('success', 'Proefrit-widget opgeslagen!');
    }

    /** @param array<string, mixed> $values */
    private function mergeSettings(Request $request, array $values): void
    {
        $company = $request->user()->company;
        $company->update(['embed_settings' => array_merge($company->embed_settings ?? [], $values)]);
    }

    /** @return array<int, array<string, string>> */
    private function widgetMeta(): array
    {
        return [
            ['key' => 'voorraad', 'route' => 'widgets.voorraad', 'icon' => 'fa-car', 'kleur' => 'bg-eazy-50 text-eazy', 'naam' => 'Voorraad', 'omschrijving' => 'Je complete autoaanbod met filters en detailpagina.'],
            ['key' => 'contact', 'route' => 'widgets.contact', 'icon' => 'fa-inbox', 'kleur' => 'bg-blue-50 text-blue-500', 'naam' => 'Contact & inruil', 'omschrijving' => 'Contact-, inruil- en financieringsformulier naar je CRM.'],
            ['key' => 'proefrit', 'route' => 'widgets.proefrit', 'icon' => 'fa-calendar-check', 'kleur' => 'bg-purple-50 text-purple-500', 'naam' => 'Proefrit', 'omschrijving' => 'Laat bezoekers een proefrit aanvragen vanaf je site.'],
            ['key' => 'taxatie', 'route' => 'widgets.taxatie', 'icon' => 'fa-right-left', 'kleur' => 'bg-amber-50 text-amber-500', 'naam' => 'Inruil-taxatie', 'omschrijving' => 'Kenteken invullen, direct een inruilindicatie, en een lead.'],
        ];
    }
}
