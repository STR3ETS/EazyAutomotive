<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Services\RdwService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Voorraad-import / onboarding. Een dealer die overstapt heeft zijn auto's al
 * ergens staan; hier zet hij ze in een keer in het platform:
 *   1. Kentekens plakken -> RDW vult merk/model/bouwjaar/brandstof automatisch aan.
 *   2. CSV-upload (bijv. export uit een ander systeem), optioneel verrijkt met RDW.
 * Geimporteerde auto's krijgen status "concept", zodat ze eerst nagekeken worden.
 */
class VoorraadImportController extends Controller
{
    /** Max aantal regels/rijen per import, zodat een verzoek niet te lang duurt. */
    private const MAX_ROWS = 100;

    /** Herkende CSV-kolomnamen -> ons veld. */
    private const CSV_ALIASES = [
        'kenteken' => 'kenteken', 'license' => 'kenteken', 'plate' => 'kenteken',
        'merk' => 'merk', 'brand' => 'merk', 'make' => 'merk',
        'model' => 'handelsbenaming', 'handelsbenaming' => 'handelsbenaming', 'type' => 'handelsbenaming',
        'prijs' => 'prijs', 'price' => 'prijs', 'vraagprijs' => 'prijs',
        'kilometerstand' => 'kilometerstand', 'km' => 'kilometerstand', 'mileage' => 'kilometerstand', 'tellerstand' => 'kilometerstand',
        'bouwjaar' => 'bouwjaar', 'jaar' => 'bouwjaar', 'year' => 'bouwjaar', 'bj' => 'bouwjaar',
        'brandstof' => 'brandstof_omschrijving', 'fuel' => 'brandstof_omschrijving',
        'kleur' => 'eerste_kleur', 'color' => 'eerste_kleur', 'colour' => 'eerste_kleur',
        'titel' => 'titel', 'title' => 'titel',
        'beschrijving' => 'beschrijving', 'omschrijving' => 'beschrijving', 'description' => 'beschrijving',
    ];

    public function __construct(private RdwService $rdw) {}

    public function index()
    {
        return view('company.import.index');
    }

    /** Kentekens plakken (een per regel), optioneel met prijs en km erachter. */
    public function kentekens(Request $request)
    {
        $data = $request->validate([
            'kentekens' => 'required|string|max:20000',
            'status' => 'required|in:draft,active',
        ]);

        @set_time_limit(600);

        $lines = preg_split('/\r?\n/', trim($data['kentekens']));
        $result = ['created' => 0, 'duplicates' => [], 'notfound' => [], 'errors' => []];
        $seen = [];
        $processed = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if ($processed >= self::MAX_ROWS) {
                $result['errors'][] = 'Maximaal ' . self::MAX_ROWS . ' kentekens per keer. De rest is overgeslagen.';
                break;
            }
            $processed++;

            $parts = preg_split('/[;,\t]/', $line);
            $kenteken = $this->rdw->normalizeKenteken($parts[0] ?? '');
            if ($kenteken === '' || isset($seen[$kenteken])) {
                continue;
            }
            $seen[$kenteken] = true;

            $prijs = isset($parts[1]) ? $this->parsePrijs($parts[1]) : null;
            $km = isset($parts[2]) ? $this->parseInt($parts[2]) : null;

            if ($this->bestaatAl($request, $kenteken)) {
                $result['duplicates'][] = $kenteken;
                continue;
            }

            $rdwData = $this->rdw->fetchByKenteken($kenteken);
            if (! $rdwData) {
                $result['notfound'][] = $kenteken;
                continue;
            }

            $attrs = $this->rdw->mapToCarAttributes($rdwData);
            $attrs['kenteken'] = $kenteken;
            $attrs['company_id'] = $request->user()->company_id;
            $attrs['status'] = $data['status'];
            if ($prijs !== null) {
                $attrs['prijs'] = $prijs;
            }
            if ($km !== null) {
                $attrs['kilometerstand'] = $km;
            }

            Car::create($attrs);
            $result['created']++;
        }

        return back()->with('import_result', $result);
    }

    /** CSV-upload, optioneel aangevuld met RDW op basis van het kenteken. */
    public function csv(Request $request)
    {
        $data = $request->validate([
            'csv' => 'required|file|mimes:csv,txt|max:5120',
            'status' => 'required|in:draft,active',
            'verrijk_rdw' => 'nullable|boolean',
        ]);

        @set_time_limit(600);

        $rows = $this->leesCsv($request->file('csv')->getRealPath());
        if ($rows === []) {
            return back()->with('error', 'Het CSV-bestand is leeg of kon niet gelezen worden. Zorg voor een kopregel met kolomnamen.');
        }

        $verrijk = $request->boolean('verrijk_rdw');
        $result = ['created' => 0, 'duplicates' => [], 'notfound' => [], 'errors' => []];
        $seen = [];
        $processed = 0;

        foreach ($rows as $i => $row) {
            if ($processed >= self::MAX_ROWS) {
                $result['errors'][] = 'Maximaal ' . self::MAX_ROWS . ' rijen per keer. De rest is overgeslagen.';
                break;
            }
            $processed++;

            $kenteken = isset($row['kenteken']) ? $this->rdw->normalizeKenteken($row['kenteken']) : '';

            if ($kenteken !== '') {
                if (isset($seen[$kenteken]) || $this->bestaatAl($request, $kenteken)) {
                    $result['duplicates'][] = $kenteken;
                    continue;
                }
                $seen[$kenteken] = true;
            }

            $attrs = [];
            if ($verrijk && $kenteken !== '') {
                $rdwData = $this->rdw->fetchByKenteken($kenteken);
                if ($rdwData) {
                    $attrs = $this->rdw->mapToCarAttributes($rdwData);
                } else {
                    $result['notfound'][] = $kenteken;
                }
            }

            // CSV-waarden hebben voorrang op RDW.
            $attrs = array_merge($attrs, $this->rowToAttributes($row));

            if (empty($attrs['merk']) && empty($attrs['titel']) && $kenteken === '') {
                $result['errors'][] = 'Rij ' . ($i + 2) . ' overgeslagen: geen kenteken, merk of titel.';
                continue;
            }

            $attrs['kenteken'] = $kenteken !== '' ? $kenteken : null;
            $attrs['company_id'] = $request->user()->company_id;
            $attrs['status'] = $data['status'];

            Car::create($attrs);
            $result['created']++;
        }

        return back()->with('import_result', $result);
    }

    /** Downloadbaar CSV-sjabloon met de herkende kolommen. */
    public function template(): StreamedResponse
    {
        $headers = ['kenteken', 'merk', 'model', 'bouwjaar', 'brandstof', 'kilometerstand', 'prijs', 'kleur', 'titel', 'beschrijving'];
        $voorbeeld = ['12-ABC-3', 'Volkswagen', 'Golf', '2019', 'Benzine', '89000', '18950', 'Grijs', 'Volkswagen Golf 1.0 TSI', 'Netjes onderhouden, NAP.'];

        return response()->streamDownload(function () use ($headers, $voorbeeld) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers, ';');
            fputcsv($out, $voorbeeld, ';');
            fclose($out);
        }, 'voorraad-import-sjabloon.csv', ['Content-Type' => 'text/csv']);
    }

    private function bestaatAl(Request $request, string $kenteken): bool
    {
        return Car::where('company_id', $request->user()->company_id)
            ->where('kenteken', $kenteken)
            ->exists();
    }

    /** @return array<string, mixed> */
    private function rowToAttributes(array $row): array
    {
        $attrs = [];
        if (! empty($row['merk'])) {
            $attrs['merk'] = trim($row['merk']);
        }
        if (! empty($row['handelsbenaming'])) {
            $attrs['handelsbenaming'] = trim($row['handelsbenaming']);
        }
        if (! empty($row['brandstof_omschrijving'])) {
            $attrs['brandstof_omschrijving'] = trim($row['brandstof_omschrijving']);
        }
        if (! empty($row['eerste_kleur'])) {
            $attrs['eerste_kleur'] = trim($row['eerste_kleur']);
        }
        if (! empty($row['titel'])) {
            $attrs['titel'] = trim($row['titel']);
        }
        if (! empty($row['beschrijving'])) {
            $attrs['beschrijving'] = trim($row['beschrijving']);
        }
        if (isset($row['bouwjaar']) && $this->parseInt($row['bouwjaar'])) {
            $attrs['bouwjaar'] = $this->parseInt($row['bouwjaar']);
        }
        if (isset($row['kilometerstand']) && $this->parseInt($row['kilometerstand']) !== null) {
            $attrs['kilometerstand'] = $this->parseInt($row['kilometerstand']);
        }
        if (isset($row['prijs']) && $this->parsePrijs($row['prijs']) !== null) {
            $attrs['prijs'] = $this->parsePrijs($row['prijs']);
        }

        return $attrs;
    }

    /** @return array<int, array<string, string>> */
    private function leesCsv(string $path): array
    {
        $raw = file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return [];
        }
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw); // BOM weg

        $firstLine = strtok($raw, "\n");
        $delimiter = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';

        $rows = [];
        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $raw);
        rewind($handle);

        $header = null;
        while (($cells = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($cells === [null] || $cells === false) {
                continue;
            }
            if ($header === null) {
                $header = array_map(fn ($h) => self::CSV_ALIASES[strtolower(trim((string) $h))] ?? null, $cells);
                continue;
            }
            $row = [];
            foreach ($cells as $idx => $value) {
                $field = $header[$idx] ?? null;
                if ($field !== null) {
                    $row[$field] = $value;
                }
            }
            if (array_filter($row, fn ($v) => trim((string) $v) !== '') !== []) {
                $rows[] = $row;
            }
        }
        fclose($handle);

        return $rows;
    }

    private function parsePrijs(?string $value): ?int
    {
        $value = preg_replace('/[^\d,.\-]/', '', trim((string) $value));
        if ($value === '' || $value === null) {
            return null;
        }

        $hasDot = str_contains($value, '.');
        $hasComma = str_contains($value, ',');

        if ($hasDot && $hasComma) {
            // Duizendtal + decimaal: het laatst voorkomende teken is de decimaal.
            if (strrpos($value, ',') > strrpos($value, '.')) {
                $value = str_replace(',', '.', str_replace('.', '', $value)); // NL: 18.950,00
            } else {
                $value = str_replace(',', '', $value);                        // EN: 18,950.00
            }
        } elseif ($hasComma) {
            $value = str_replace(',', '.', $value);                           // alleen komma = decimaal
        } elseif ($hasDot) {
            // Alleen een punt: duizendtal-scheiding bij meerdere punten of precies 3 cijfers erna (18.950).
            $decimalen = strlen(substr(strrchr($value, '.'), 1));
            if (substr_count($value, '.') > 1 || $decimalen === 3) {
                $value = str_replace('.', '', $value);
            }
        }

        $euros = (float) $value;

        return $euros > 0 ? (int) round($euros * 100) : null;
    }

    private function parseInt(?string $value): ?int
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        return $digits === '' ? null : (int) $digits;
    }
}
