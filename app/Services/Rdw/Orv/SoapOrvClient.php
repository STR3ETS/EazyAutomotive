<?php

namespace App\Services\Rdw\Orv;

/**
 * Praat met de echte RDW ORV-webservice (Online Registratie Voorraad / Vrijwaren)
 * over SOAP, met het clientcertificaat van het erkende bedrijf.
 *
 * De exacte operatie- en veldnamen komen uit de technische RDW/A2SP-documentatie
 * die alleen erkende bedrijven krijgen. Die zitten daarom in config
 * (services.rdw.orv.op_*) zodat ze zonder codewijziging kloppend gemaakt worden.
 * Zolang WSDL + certificaat ontbreken is deze client "niet geconfigureerd" en
 * gebruikt de app de sandbox.
 */
class SoapOrvClient implements OrvClient
{
    /** @param array<string, mixed> $config */
    public function __construct(private array $config) {}

    public function vrijwaar(string $kenteken, string $tenaamstellingscode): OrvResult
    {
        return $this->call((string) ($this->config['op_vrijwaren'] ?? ''), $kenteken, $tenaamstellingscode);
    }

    public function uitVoorraad(string $kenteken, string $tenaamstellingscode): OrvResult
    {
        return $this->call((string) ($this->config['op_uitvoorraad'] ?? ''), $kenteken, $tenaamstellingscode);
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['wsdl'])
            && ! empty($this->config['certificate'])
            && is_file((string) $this->config['certificate'])
            && ! empty($this->config['erkenning']);
    }

    public function mode(): string
    {
        return 'soap';
    }

    private function call(string $operation, string $kenteken, string $tenaamstellingscode): OrvResult
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('RDW ORV is nog niet geconfigureerd: WSDL, certificaat en erkenningsnummer ontbreken.');
        }
        if ($operation === '') {
            throw new \RuntimeException('Geen RDW ORV-operatie ingesteld voor deze mutatie.');
        }
        if (! class_exists(\SoapClient::class)) {
            throw new \RuntimeException('PHP SOAP-extensie is niet beschikbaar op deze server.');
        }

        try {
            $client = new \SoapClient((string) $this->config['wsdl'], array_filter([
                'local_cert' => $this->config['certificate'],
                'passphrase' => $this->config['certificate_passphrase'] ?? null,
                'soap_version' => SOAP_1_1,
                'exceptions' => true,
                'trace' => true,
                'connection_timeout' => 20,
            ], fn ($v) => $v !== null));

            // Aanvraagstructuur volgens de RDW ORV-spec. De precieze veldnamen
            // kunnen per WSDL verschillen; pas hier aan op basis van de RDW-docs.
            $params = [
                'Erkenning' => $this->config['erkenning'],
                'Volgnummer' => $this->config['volgnummer'] ?? null,
                'Kenteken' => $kenteken,
                'Meldcode' => $tenaamstellingscode, // tenaamstellingscode
            ];

            $response = $client->__soapCall($operation, [array_filter($params, fn ($v) => $v !== null)]);

            return $this->mapResponse($response);
        } catch (\SoapFault $e) {
            return OrvResult::fout('RDW gaf een fout terug: ' . $e->getMessage());
        }
    }

    private function mapResponse(mixed $response): OrvResult
    {
        // Best-effort mapping; maak kloppend met de echte RDW-responsevelden.
        $get = function (array $keys) use ($response) {
            foreach ($keys as $key) {
                if (is_object($response) && isset($response->{$key})) {
                    return (string) $response->{$key};
                }
                if (is_array($response) && isset($response[$key])) {
                    return (string) $response[$key];
                }
            }

            return null;
        };

        $foutmelding = $get(['Foutmelding', 'FoutOmschrijving', 'ErrorMessage']);
        if ($foutmelding) {
            return OrvResult::fout($foutmelding);
        }

        return OrvResult::ok(
            vrijwaringsbewijs: $get(['Vrijwaringsbewijsnummer', 'Vrijwaringsnummer', 'VrijwaringsbewijsNummer']),
            datum: $get(['Datum', 'TransactieDatum', 'Tijdstip']),
            referentie: $get(['Transactienummer', 'Kenmerk', 'Referentie']),
        );
    }
}
