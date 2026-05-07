<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\CarView;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;

        $stats = [
            'total_cars' => Car::where('company_id', $companyId)->count(),
            'active_cars' => Car::where('company_id', $companyId)->where('status', 'active')->count(),
            'sold_cars' => Car::where('company_id', $companyId)->where('status', 'sold')->count(),
            'reserved_cars' => Car::where('company_id', $companyId)->where('status', 'reserved')->count(),
            'views_today' => CarView::where('company_id', $companyId)->whereDate('created_at', today())->count(),
            'views_week' => CarView::where('company_id', $companyId)->where('created_at', '>=', now()->subWeek())->count(),
            'views_month' => CarView::where('company_id', $companyId)->where('created_at', '>=', now()->subMonth())->count(),
        ];

        $recentCars = Car::where('company_id', $companyId)
            ->with('primaryImage')
            ->latest()
            ->take(5)
            ->get();

        $popularCars = Car::where('company_id', $companyId)
            ->active()
            ->orderByDesc('view_count')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentCars', 'popularCars'));
    }
}
