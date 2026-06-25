<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $company = $request->user()->company;

        $leads = $company->leads()
            ->with(['car', 'assignedUser'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('naam', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('telefoon', 'like', "%{$s}%");
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'nieuw' => $company->leads()->where('status', 'nieuw')->count(),
            'open' => $company->leads()->open()->count(),
            'gewonnen' => $company->leads()->where('status', 'gewonnen')->count(),
            'totaal' => $company->leads()->count(),
        ];

        $cars = $company->cars()->orderBy('merk')->get(['id', 'merk', 'handelsbenaming', 'titel']);

        return view('company.leads.index', compact('leads', 'stats', 'cars'));
    }

    /** Manually log a lead (phone call, walk-in, etc.). */
    public function store(Request $request)
    {
        $company = $request->user()->company;

        $validated = $request->validate([
            'naam' => 'required|string|max:150',
            'email' => 'nullable|email|max:190',
            'telefoon' => 'nullable|string|max:40',
            'type' => 'required|in:proefrit,contact,inruil,financiering,overig',
            'car_id' => 'nullable|integer',
            'bericht' => 'nullable|string|max:2000',
        ]);

        if (empty($validated['email']) && empty($validated['telefoon'])) {
            return back()->with('error', 'Vul minstens een e-mail of telefoonnummer in.')->withInput();
        }

        $carId = ! empty($validated['car_id'])
            ? $company->cars()->whereKey($validated['car_id'])->value('id')
            : null;

        $company->leads()->create([
            'car_id' => $carId,
            'type' => $validated['type'],
            'naam' => $validated['naam'],
            'email' => $validated['email'] ?? null,
            'telefoon' => $validated['telefoon'] ?? null,
            'bericht' => $validated['bericht'] ?? null,
            'status' => 'nieuw',
            'source' => 'handmatig',
        ]);

        return back()->with('success', 'Lead toegevoegd.');
    }

    public function show(Request $request, Lead $lead)
    {
        abort_unless($lead->company_id === $request->user()->company_id, 403);

        $lead->load(['car', 'assignedUser']);
        $users = $request->user()->company->users()->orderBy('name')->get(['id', 'name']);

        return view('company.leads.show', compact('lead', 'users'));
    }

    /** Full update from the lead detail page (status, owner, follow-up, notes together). */
    public function update(Request $request, Lead $lead)
    {
        abort_unless($lead->company_id === $request->user()->company_id, 403);

        $validated = $request->validate([
            'status' => 'required|in:nieuw,contact,afspraak,gewonnen,verloren',
            'assigned_to' => 'nullable|integer',
            'follow_up_at' => 'nullable|date',
            'notes' => 'nullable|string|max:5000',
        ]);

        $assigned = ! empty($validated['assigned_to'])
            ? $request->user()->company->users()->whereKey($validated['assigned_to'])->value('id')
            : null;

        $lead->update([
            'status' => $validated['status'],
            'assigned_to' => $assigned,
            'follow_up_at' => $validated['follow_up_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Lead bijgewerkt.');
    }

    /** Quick status change from the list (does not touch other fields). */
    public function updateStatus(Request $request, Lead $lead)
    {
        abort_unless($lead->company_id === $request->user()->company_id, 403);

        $validated = $request->validate([
            'status' => 'required|in:nieuw,contact,afspraak,gewonnen,verloren',
        ]);

        $lead->update($validated);

        return back()->with('success', 'Status bijgewerkt.');
    }

    public function destroy(Request $request, Lead $lead)
    {
        abort_unless($lead->company_id === $request->user()->company_id, 403);

        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead verwijderd.');
    }
}
