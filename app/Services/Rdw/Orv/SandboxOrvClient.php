<?php

namespace App\Services\Rdw\Orv;

use Illuminate\Support\Carbon;

/**
 * Simuleert de RDW ORV-dienst lokaal, zodat de volledige vrijwaring-flow te
 * testen is zonder erkenning of certificaat. Geeft realistische antwoorden en
 * een paar vaste testgevallen om ook de foutafhandeling te kunnen zien.
 *
 * Testgevallen:
 *   - tenaamstellingscode "000000000"  -> mislukt (ongeldige/onbekende code)
 *   - kenteken dat op "0" eindigt        -> mislukt (voertuig niet vrijwaarbaar)
 *   - al het overige                     -> geslaagd + vrijwaringsbewijs
 */
class SandboxOrvClient implements OrvClient
{
    public function vrijwaar(string $kenteken, string $tenaamstellingscode): OrvResult
    {
        if ($fout = $this->valideer($kenteken, $tenaamstellingscode)) {
            return OrvResult::fout($fout);
        }

        if (str_ends_with($kenteken, '0')) {
            return OrvResult::fout('Dit voertuig kan niet in bedrijfsvoorraad worden opgenomen (sandbox-testgeval).');
        }

        return OrvResult::ok(
            vrijwaringsbewijs: $this->bewijsnummer($kenteken),
            datum: Carbon::now()->toIso8601String(),
            referentie: 'ORV-SANDBOX-' . strtoupper(bin2hex(random_bytes(4))),
        );
    }

    public function uitVoorraad(string $kenteken, string $tenaamstellingscode): OrvResult
    {
        if ($fout = $this->valideer($kenteken, $tenaamstellingscode)) {
            return OrvResult::fout($fout);
        }

        return OrvResult::ok(
            vrijwaringsbewijs: null,
            datum: Carbon::now()->toIso8601String(),
            referentie: 'ORV-SANDBOX-' . strtoupper(bin2hex(random_bytes(4))),
        );
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function mode(): string
    {
        return 'sandbox';
    }

    private function valideer(string $kenteken, string $code): ?string
    {
        if ($kenteken === '' || strlen($kenteken) < 4) {
            return 'Ongeldig kenteken.';
        }
        if (! preg_match('/^\d{4,12}$/', $code)) {
            return 'Ongeldige tenaamstellingscode (verwacht 4 tot 12 cijfers van de kentekencard of het tenaamstellingsverslag).';
        }
        if ($code === '000000000') {
            return 'De tenaamstellingscode is onbekend of onjuist (sandbox-testgeval).';
        }

        return null;
    }

    private function bewijsnummer(string $kenteken): string
    {
        return 'VRW' . Carbon::now()->format('ymd') . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
