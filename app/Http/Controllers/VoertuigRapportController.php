<?php

namespace App\Http\Controllers;

use App\Services\RdwReportService;
use Illuminate\Http\Request;

class VoertuigRapportController extends Controller
{
    public function __construct(private RdwReportService $reports) {}

    public function index(Request $request)
    {
        $kenteken = trim((string) $request->query('kenteken')) ?: null;
        $km = $request->integer('km') ?: null;
        $provincie = trim((string) $request->query('provincie')) ?: null;

        $report = $kenteken ? $this->reports->generate($kenteken, $km, $provincie) : null;

        return view('company.onderzoek', compact('report', 'kenteken', 'km', 'provincie'));
    }
}
