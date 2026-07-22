<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Customer;
use App\Models\Koopovereenkomst;
use Illuminate\Http\Request;

/**
 * Koop-/verkoopovereenkomsten: de dealer verkoopt een auto aan een koper. Levert
 * een printbaar (PDF via de browser) contract met dealer, koper, voertuig, prijs
 * (marge of BTW), inruil, levering en garantie.
 */
class KoopovereenkomstController extends Controller
{
    public function index(Request $request)
    {
        $overeenkomsten = Koopovereenkomst::where('company_id', $request->user()->company_id)
            ->with(['car', 'customer'])
            ->latest()
            ->paginate(20);

        return view('company.koopovereenkomsten.index', compact('overeenkomsten'));
    }

    public function create(Request $request)
    {
        $companyId = $request->user()->company_id;

        return view('company.koopovereenkomsten.create', [
            'cars' => Car::where('company_id', $companyId)
                ->whereIn('status', ['active', 'reserved', 'draft'])
                ->orderBy('merk')->get(),
            'customers' => Customer::where('company_id', $companyId)->orderBy('naam')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $companyId = $request->user()->company_id;

        $data = $request->validate([
            'car_id' => 'required|integer',
            'customer_id' => 'nullable|integer',
            'chassisnummer' => 'nullable|string|max:40',
            'koper_naam' => 'required|string|max:150',
            'koper_adres' => 'nullable|string|max:150',
            'koper_postcode' => 'nullable|string|max:12',
            'koper_plaats' => 'nullable|string|max:100',
            'koper_email' => 'nullable|email|max:150',
            'koper_telefoon' => 'nullable|string|max:40',
            'verkoopprijs' => 'required|numeric|min:0',
            'btw_type' => 'required|in:marge,btw',
            'inruil_omschrijving' => 'nullable|string|max:200',
            'inruil_bedrag' => 'nullable|numeric|min:0',
            'leverdatum' => 'nullable|date',
            'garantie' => 'nullable|string|max:200',
            'bijzonderheden' => 'nullable|string|max:2000',
        ]);

        $car = Car::where('company_id', $companyId)->findOrFail($data['car_id']);
        $customer = ! empty($data['customer_id'])
            ? Customer::where('company_id', $companyId)->find($data['customer_id'])
            : null;

        $overeenkomst = new Koopovereenkomst([
            'btw_type' => $data['btw_type'],
            'verkoopprijs' => (int) round($data['verkoopprijs'] * 100),
            'inruil_omschrijving' => $data['inruil_omschrijving'] ?? null,
            'inruil_bedrag' => isset($data['inruil_bedrag']) ? (int) round($data['inruil_bedrag'] * 100) : null,
            'leverdatum' => $data['leverdatum'] ?? null,
            'garantie' => $data['garantie'] ?? null,
            'bijzonderheden' => $data['bijzonderheden'] ?? null,
            'status' => 'definitief',
            'koper' => [
                'naam' => $data['koper_naam'],
                'bedrijfsnaam' => $customer?->bedrijfsnaam,
                'adres' => $data['koper_adres'] ?? null,
                'postcode' => $data['koper_postcode'] ?? null,
                'plaats' => $data['koper_plaats'] ?? null,
                'email' => $data['koper_email'] ?? null,
                'telefoon' => $data['koper_telefoon'] ?? null,
                'kvk' => $customer?->kvk_nummer,
                'btw' => $customer?->btw_nummer,
            ],
            'voertuig' => [
                'kenteken' => $car->kenteken,
                'merk' => $car->merk,
                'model' => $car->handelsbenaming,
                'bouwjaar' => $car->bouwjaar,
                'kilometerstand' => $car->kilometerstand,
                'kleur' => $car->eerste_kleur,
                'brandstof' => $car->brandstof_omschrijving,
                'chassisnummer' => $data['chassisnummer'] ?? null,
            ],
        ]);
        $overeenkomst->company_id = $companyId;
        $overeenkomst->car_id = $car->id;
        $overeenkomst->customer_id = $customer?->id;
        $overeenkomst->assignNumber();
        $overeenkomst->save();

        return redirect()->route('koopovereenkomsten.print', $overeenkomst)
            ->with('success', 'Koopovereenkomst ' . $overeenkomst->nummer . ' aangemaakt.');
    }

    public function print(Request $request, Koopovereenkomst $koopovereenkomst)
    {
        abort_unless($koopovereenkomst->company_id === $request->user()->company_id, 403);
        $koopovereenkomst->load('company');

        return view('company.koopovereenkomsten.print', compact('koopovereenkomst'));
    }

    public function destroy(Request $request, Koopovereenkomst $koopovereenkomst)
    {
        abort_unless($koopovereenkomst->company_id === $request->user()->company_id, 403);
        $koopovereenkomst->delete();

        return redirect()->route('koopovereenkomsten.index')->with('success', 'Koopovereenkomst verwijderd.');
    }
}
