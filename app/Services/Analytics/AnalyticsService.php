<?php

namespace App\Services\Analytics;

use App\Models\Car;
use App\Models\CarView;
use App\Models\Invoice;
use App\Models\Lead;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Rekent alle cijfers voor het Inzichten-dashboard uit: voorraad, marketing,
 * verkoop en financien. Alles strikt bedrijf-gescoped. Grafiekreeksen worden
 * met nullen aangevuld zodat de client alleen hoeft te tekenen.
 */
class AnalyticsService
{
    /** @return array<string,mixed> */
    public function forCompany(int $companyId): array
    {
        return [
            'kpis' => $this->kpis($companyId),
            'views_series' => $this->viewsSeries($companyId, 30),
            'revenue_series' => $this->revenueSeries($companyId, 12),
            'stock_by_status' => $this->stockByStatus($companyId),
            'top_brands' => $this->topBrands($companyId),
            'top_cars' => $this->topCars($companyId),
            'funnel' => $this->funnel($companyId),
        ];
    }

    /** @return array<string,mixed> */
    private function kpis(int $companyId): array
    {
        $cars = fn () => Car::where('company_id', $companyId);
        $views = fn () => CarView::where('company_id', $companyId);
        $leads = fn () => Lead::where('company_id', $companyId);

        $views30 = (clone $views())->where('created_at', '>=', now()->subDays(30))->count();
        $viewsPrev30 = (clone $views())->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();

        $leads30 = (clone $leads())->where('created_at', '>=', now()->subDays(30))->count();
        $leadsPrev30 = (clone $leads())->whereBetween('created_at', [now()->subDays(60), now()->subDays(30)])->count();

        $won = (clone $leads())->where('status', 'gewonnen')->count();
        $lost = (clone $leads())->where('status', 'verloren')->count();
        $closed = $won + $lost;

        $revenueYear = (int) Invoice::where('company_id', $companyId)
            ->whereNotIn('status', ['concept', 'geannuleerd'])
            ->whereYear('date', now()->year)
            ->sum('total');

        $outstanding = (int) Invoice::where('company_id', $companyId)
            ->whereIn('status', ['verzonden', 'deels_betaald', 'vervallen'])
            ->sum(DB::raw('total - amount_paid'));

        $avgAge = (clone $cars())->where('status', 'active')->whereNotNull('created_at')
            ->avg(DB::raw('DATEDIFF(NOW(), created_at)'));

        return [
            'active_cars' => (clone $cars())->where('status', 'active')->count(),
            'stock_value' => (int) (clone $cars())->where('status', 'active')->sum('prijs'),
            'avg_stock_age' => $avgAge !== null ? (int) round((float) $avgAge) : null,
            'sold_month' => (clone $cars())->where('status', 'sold')->where('sold_at', '>=', now()->startOfMonth())->count(),
            'sold_year' => (clone $cars())->where('status', 'sold')->whereYear('sold_at', now()->year)->count(),
            'views_30' => $views30,
            'views_trend' => $this->trendPct($views30, $viewsPrev30),
            'leads_30' => $leads30,
            'leads_trend' => $this->trendPct($leads30, $leadsPrev30),
            'conversion' => $closed > 0 ? (int) round($won / $closed * 100) : null,
            'revenue_year' => $revenueYear,
            'outstanding' => $outstanding,
        ];
    }

    /** Weergaven per dag over de laatste N dagen, met nullen aangevuld. */
    private function viewsSeries(int $companyId, int $days): array
    {
        $from = now()->subDays($days - 1)->startOfDay();

        $rows = CarView::where('company_id', $companyId)
            ->where('created_at', '>=', $from)
            ->groupBy('d')
            ->orderBy('d')
            ->get([DB::raw('DATE(created_at) as d'), DB::raw('COUNT(*) as aantal')])
            ->keyBy('d');

        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $day = (clone $from)->addDays($i);
            $key = $day->toDateString();
            $out[] = [
                'label' => $day->format('d-m'),
                'short' => $day->format('j'),
                'value' => (int) ($rows[$key]->aantal ?? 0),
            ];
        }

        return $out;
    }

    /** Omzet (definitieve facturen) per maand over de laatste N maanden. */
    private function revenueSeries(int $companyId, int $months): array
    {
        $from = now()->startOfMonth()->subMonths($months - 1);

        $rows = Invoice::where('company_id', $companyId)
            ->whereNotIn('status', ['concept', 'geannuleerd'])
            ->where('date', '>=', $from)
            ->groupBy('ym')
            ->get([DB::raw("DATE_FORMAT(date, '%Y-%m') as ym"), DB::raw('SUM(total) as totaal')])
            ->keyBy('ym');

        $maanden = ['jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'];
        $out = [];
        for ($i = 0; $i < $months; $i++) {
            $m = (clone $from)->addMonths($i);
            $key = $m->format('Y-m');
            $out[] = [
                'label' => $maanden[$m->month - 1] . ' ' . $m->format('y'),
                'short' => $maanden[$m->month - 1],
                'value' => (int) ($rows[$key]->totaal ?? 0),
            ];
        }

        return $out;
    }

    /** @return array<int,array<string,mixed>> */
    private function stockByStatus(int $companyId): array
    {
        $meta = [
            'active' => ['label' => 'Actief', 'color' => 'emerald'],
            'reserved' => ['label' => 'Gereserveerd', 'color' => 'amber'],
            'sold' => ['label' => 'Verkocht', 'color' => 'blue'],
            'draft' => ['label' => 'Concept', 'color' => 'gray'],
        ];

        $counts = Car::where('company_id', $companyId)
            ->groupBy('status')
            ->pluck(DB::raw('COUNT(*)'), 'status');

        $out = [];
        foreach ($meta as $status => $m) {
            $out[] = $m + ['status' => $status, 'count' => (int) ($counts[$status] ?? 0)];
        }

        return $out;
    }

    /** @return array<int,array<string,mixed>> top 5 merken naar actieve voorraad. */
    private function topBrands(int $companyId): array
    {
        return Car::where('company_id', $companyId)
            ->where('status', 'active')
            ->whereNotNull('merk')
            ->where('merk', '!=', '')
            ->groupBy('merk')
            ->orderByDesc('aantal')
            ->limit(5)
            ->get(['merk', DB::raw('COUNT(*) as aantal')])
            ->map(fn ($r) => ['merk' => $r->merk, 'aantal' => (int) $r->aantal])
            ->all();
    }

    /** @return array<int,array<string,mixed>> best bekeken actieve auto's + leads. */
    private function topCars(int $companyId): array
    {
        $cars = Car::where('company_id', $companyId)
            ->where('status', 'active')
            ->orderByDesc('view_count')
            ->take(5)
            ->get();

        if ($cars->isEmpty()) {
            return [];
        }

        $leadCounts = Lead::where('company_id', $companyId)
            ->whereIn('car_id', $cars->pluck('id'))
            ->groupBy('car_id')
            ->pluck(DB::raw('COUNT(*)'), 'car_id');

        return $cars->map(fn (Car $car) => [
            'id' => $car->id,
            'titel' => $car->display_title,
            'views' => (int) $car->view_count,
            'leads' => (int) ($leadCounts[$car->id] ?? 0),
            'prijs' => (int) $car->prijs,
        ])->all();
    }

    /** @return array<string,mixed> leads-trechter per status. */
    private function funnel(int $companyId): array
    {
        $counts = Lead::where('company_id', $companyId)
            ->groupBy('status')
            ->pluck(DB::raw('COUNT(*)'), 'status');

        $stages = [];
        foreach (Lead::STATUSES as $key => $meta) {
            $stages[] = [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'count' => (int) ($counts[$key] ?? 0),
            ];
        }

        return [
            'stages' => $stages,
            'total' => (int) $counts->sum(),
        ];
    }

    private function trendPct(int $current, int $previous): ?int
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : null;
        }

        return (int) round(($current - $previous) / $previous * 100);
    }
}
