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

        $report = $kenteken ? $this->reports->generate($kenteken, $km) : null;

        return view('company.onderzoek', compact('report', 'kenteken', 'km'));
    }
}
