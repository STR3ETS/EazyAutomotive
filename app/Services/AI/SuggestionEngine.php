<?php

namespace App\Services\AI;

use App\Models\Car;
use App\Models\Lead;
use Illuminate\Support\Facades\Cache;

/**
 * De proactieve laag van de AI-collega: scant de eigen voorraad en leads en
 * levert concrete suggestiekaarten voor het dashboard. Regel-gebaseerd (snel en
 * gratis, geen model-call), altijd bedrijf-gescoped. Weggeklikte suggesties
 * blijven een paar dagen verborgen.
 */
class SuggestionEngine
{
    /** @return array<int, array<string,mixed>> */
    public function forCompany(int $companyId, int $userId): array
    {
        $dismissed = Cache::get($this->cacheKey($userId), []);

        $out = [];
        foreach ($this->build($companyId) as $s) {
            if (! in_array($s['key'], $dismissed, true)) {
                $out[] = $s;
            }
        }

        return array_slice($out, 0, 5);
    }

    public function dismiss(int $userId, string $key): void
    {
        $dismissed = Cache::get($this->cacheKey($userId), []);
        $dismissed[] = $key;
        Cache::put($this->cacheKey($userId), array_values(array_unique($dismissed)), now()->addDays(5));
    }

    private function cacheKey(int $userId): string
    {
        return "sam_dismissed_{$userId}";
    }

    /** @return array<int, array<string,mixed>> */
    private function build(int $companyId): array
    {
        $out = [];
        $active = fn () => Car::where('company_id', $companyId)->where('status', 'active');

        $drafts = Car::where('company_id', $companyId)->where('status', 'draft')->count();
        if ($drafts > 0) {
            $out[] = $this->card('activeer_concepten', 'fa-circle-play', 'bg-blue-50', 'text-blue-500',
                "{$drafts} auto's staan nog in concept",
                'Ze zijn nog niet zichtbaar op je website. Zal ik ze activeren?',
                ['type' => 'run', 'label' => 'Activeren']);
        }

        $noDesc = (clone $active())->where(fn ($q) => $q->whereNull('beschrijving')->orWhere('beschrijving', ''))->count();
        if ($noDesc > 0) {
            $out[] = $this->card('schrijf_teksten', 'fa-pen-nib', 'bg-eazy-50', 'text-eazy',
                "{$noDesc} actieve auto's hebben geen advertentietekst",
                'Ik kan er automatisch een pakkende, SEO-vriendelijke tekst voor schrijven.',
                ['type' => 'run', 'label' => 'Schrijf teksten']);
        }

        $noImg = (clone $active())->doesntHave('images')->count();
        if ($noImg > 0) {
            $out[] = $this->card('geen_fotos', 'fa-image', 'bg-amber-50', 'text-amber-500',
                "{$noImg} actieve auto's hebben nog geen foto's",
                "Auto's met foto's krijgen veel meer weergaven. Voeg ze toe bij de auto.",
                ['type' => 'link', 'label' => 'Bekijk voorraad', 'url' => route('cars.index')]);
        }

        $noPrice = (clone $active())->where(fn ($q) => $q->whereNull('prijs')->orWhere('prijs', 0))->count();
        if ($noPrice > 0) {
            $out[] = $this->card('geen_prijs', 'fa-tag', 'bg-amber-50', 'text-amber-500',
                "{$noPrice} auto's staan online zonder prijs",
                'Een prijs (of "op aanvraag") geeft kopers meer vertrouwen.',
                ['type' => 'link', 'label' => 'Bekijk voorraad', 'url' => route('cars.index')]);
        }

        $newLeads = Lead::where('company_id', $companyId)->where('status', 'nieuw')->count();
        if ($newLeads > 0) {
            $out[] = $this->card('nieuwe_leads', 'fa-inbox', 'bg-indigo-50', 'text-indigo-500',
                "{$newLeads} nieuwe leads wachten op opvolging",
                'Snel reageren geeft de grootste kans op een deal.',
                ['type' => 'link', 'label' => 'Naar leads', 'url' => route('leads.index')]);
        }

        $stale = (clone $active())->where('created_at', '<', now()->subDays(60))->count();
        if ($stale > 0) {
            $out[] = $this->card('lang_te_koop', 'fa-hourglass-half', 'bg-red-50', 'text-red-400',
                "{$stale} auto's staan al 60+ dagen te koop",
                'Overweeg een scherpere prijs, nieuwe foto\'s of extra promotie.',
                ['type' => 'link', 'label' => 'Bekijk voorraad', 'url' => route('cars.index')]);
        }

        return $out;
    }

    /** @param array<string,mixed> $actie */
    private function card(string $key, string $icon, string $iconBg, string $iconColor, string $titel, string $tekst, array $actie): array
    {
        return compact('key', 'icon', 'iconBg', 'iconColor', 'titel', 'tekst', 'actie');
    }
}
