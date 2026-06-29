<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::where('company_id', $request->user()->company_id)
            ->withCount('invoices')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn ($w) => $w->where('naam', 'like', "%{$s}%")
                    ->orWhere('bedrijfsnaam', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%"));
            })
            ->orderBy('naam')
            ->paginate(30)
            ->withQueryString();

        return view('company.customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCustomer($request);
        $data['company_id'] = $request->user()->company_id;

        Customer::create($data);

        return back()->with('success', 'Klant toegevoegd.');
    }

    public function update(Request $request, Customer $customer)
    {
        $this->authorizeCustomer($request, $customer);
        $customer->update($this->validateCustomer($request));

        return back()->with('success', 'Klant bijgewerkt.');
    }

    public function destroy(Request $request, Customer $customer)
    {
        $this->authorizeCustomer($request, $customer);
        $customer->delete();

        return back()->with('success', 'Klant verwijderd.');
    }

    private function authorizeCustomer(Request $request, Customer $customer): void
    {
        abort_unless($customer->company_id === $request->user()->company_id, 403);
    }

    private function validateCustomer(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:particulier,zakelijk',
            'naam' => 'required|string|max:150',
            'bedrijfsnaam' => 'nullable|string|max:150',
            'email' => 'nullable|email|max:190',
            'telefoon' => 'nullable|string|max:40',
            'adres' => 'nullable|string|max:190',
            'postcode' => 'nullable|string|max:12',
            'plaats' => 'nullable|string|max:120',
            'land' => 'nullable|string|max:80',
            'kvk_nummer' => 'nullable|string|max:20',
            'btw_nummer' => 'nullable|string|max:20',
            'notities' => 'nullable|string|max:2000',
        ]);
    }
}
