<?php

namespace App\Services\Rdw\Orv;

/**
 * Koppeling met de RDW ORV-dienst (Online Registratie Voorraad / Vrijwaren).
 *
 * Twee implementaties:
 *   - SandboxOrvClient: simuleert de RDW lokaal, zodat de hele flow nu al te
 *     testen is zonder erkenning of certificaat.
 *   - SoapOrvClient: praat met de echte RDW-webservice (acceptatie of productie).
 *
 * Welke actief is, bepaalt config('services.rdw.orv.mode').
 */
interface OrvClient
{
    /**
     * Neemt een voertuig op in de bedrijfsvoorraad. Vrijwaart de vorige eigenaar
     * en levert een vrijwaringsbewijs.
     */
    public function vrijwaar(string $kenteken, string $tenaamstellingscode): OrvResult;

    /**
     * Geeft een voertuig uit de bedrijfsvoorraad (tenaamstelling naar de koper).
     */
    public function uitVoorraad(string $kenteken, string $tenaamstellingscode): OrvResult;

    /** Is deze koppeling bruikbaar (credentials/certificaat aanwezig)? */
    public function isConfigured(): bool;

    /** 'sandbox' of 'soap'. */
    public function mode(): string;
}
