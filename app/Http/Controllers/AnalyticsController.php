<?php

namespace App\Http\Controllers;

use App\Services\Analytics\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request, AnalyticsService $analytics)
    {
        $data = $analytics->forCompany($request->user()->company_id);

        return view('company.analytics.index', $data);
    }
}
