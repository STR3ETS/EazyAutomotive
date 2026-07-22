<?php

namespace App\Http\Controllers;

use App\Models\BedrijfsvoorraadMutatie;
use App\Models\Car;
use App\Services\Rdw\Orv\OrvClient;
use App\Services\Rdw\Orv\OrvResult;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

/**
 * RDW-vrijwaring / bedrijfsvoorraad. Neemt voertuigen op in de bedrijfsvoorraad
 * (vrijwaart de vorige eigenaar) en geeft ze weer uit bij verkoop. Dit is een
 * echte registermutatie via de RDW ORV-dienst; alles wordt gelogd voor de
 * audit-trail. De gevoelige tenaamstellingscode wordt nooit opgeslagen.
 */
class BedrijfsvoorraadController extends Controller
{
    public function index(Request $request, OrvClient $client)
    {
        $mutaties = BedrijfsvoorraadMutatie::where('company_id', $request->user()->company_id)
            ->latest()
            ->take(50)
            ->get();

        return view('company.bedrijfsvoorraad.index', [
            'mutaties' => $mutaties,
            'mode' => $client->mode(),
            'configured' => $client->isConfigured(),
        ]);
    }

    public function vrijwaren(Request $request, OrvClient $client)
    {
        return $this->muteer($request, $client, 'vrijwaring');
    }

    public function uit(Request $request, OrvClient $client)
    {
        return $this->muteer($request, $client, 'uitvoorraad');
    }

    /** Printbaar vrijwaringsbewijs (PDF via de browser) van een geslaagde mutatie. */
    public function print(Request $request, BedrijfsvoorraadMutatie $mutatie)
    {
        abort_unless($mutatie->company_id === $request->user()->company_id, 403);
        abort_unless($mutatie->isGeslaagd(), 404);

        $mutatie->load(['company', 'car']);

        return view('company.bedrijfsvoorraad.print', compact('mutatie'));
    }

    private function muteer(Request $request, OrvClient $client, string $type)
    {
        $data = $request->validate([
            'kenteken' => 'required|string|max:12',
            'tenaamstellingscode' => 'required|string|max:20',
        ]);

        $kenteken = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $data['kenteken']));
        $code = preg_replace('/\D/', '', $data['tenaamstellingscode']);

        if (! preg_match('/^\d{4,12}$/', $code)) {
            return back()->with('error', 'Vul een geldige tenaamstellingscode in (4 tot 12 cijfers van de kentekencard of het tenaamstellingsverslag).');
        }

        // Koppel aan een auto in de eigen voorraad als het kenteken bekend is.
        $car = Car::where('company_id', $request->user()->company_id)
            ->where('kenteken', $kenteken)
            ->first();

        try {
            $result = $type === 'vrijwaring'
                ? $client->vrijwaar($kenteken, $code)
                : $client->uitVoorraad($kenteken, $code);
        } catch (\Throwable $e) {
            report($e);
            $result = OrvResult::fout($e->getMessage());
        }

        BedrijfsvoorraadMutatie::create([
            'company_id' => $request->user()->company_id,
            'user_id' => $request->user()->id,
            'car_id' => $car?->id,
            'type' => $type,
            'kenteken' => $kenteken,
            'status' => $result->geslaagd ? 'geslaagd' : 'mislukt',
            'mode' => $client->mode(),
            'vrijwaringsbewijs' => $result->vrijwaringsbewijs,
            'bewijs_datum' => $result->datum ? Carbon::parse($result->datum) : null,
            'referentie' => $result->referentie,
            'foutmelding' => $result->foutmelding,
        ]);

        if (! $result->geslaagd) {
            return back()->with('error', 'Mutatie mislukt: ' . ($result->foutmelding ?: 'onbekende fout bij de RDW.'));
        }

        $bericht = $type === 'vrijwaring'
            ? 'Voertuig ' . $this->kentekenFormat($kenteken) . ' is opgenomen in bedrijfsvoorraad. Vrijwaringsbewijs: ' . $result->vrijwaringsbewijs . '.'
            : 'Voertuig ' . $this->kentekenFormat($kenteken) . ' is uit bedrijfsvoorraad gegeven.';

        return back()->with('success', $bericht);
    }

    private function kentekenFormat(string $kenteken): string
    {
        return trim(chunk_split($kenteken, 2, '-'), '-');
    }
}
