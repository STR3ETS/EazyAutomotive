<?php

namespace App\Http\Controllers;

use App\Models\ProefritAanvraag;
use Illuminate\Http\Request;

class ProefritController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->user()->company;

        $aanvragen = $company->proefritAanvragen()
            ->with('car')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'nieuw' => $company->proefritAanvragen()->where('status', 'nieuw')->count(),
            'ingepland' => $company->proefritAanvragen()->where('status', 'ingepland')->count(),
            'afgerond' => $company->proefritAanvragen()->where('status', 'afgerond')->count(),
            'totaal' => $company->proefritAanvragen()->count(),
        ];

        return view('company.proefritten', compact('aanvragen', 'stats'));
    }

    public function updateStatus(Request $request, ProefritAanvraag $aanvraag)
    {
        if ($aanvraag->company_id !== $request->user()->company_id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:nieuw,gecontacteerd,ingepland,afgerond,geannuleerd',
        ]);

        $aanvraag->update($validated);

        return back()->with('success', 'Status bijgewerkt.');
    }
}
