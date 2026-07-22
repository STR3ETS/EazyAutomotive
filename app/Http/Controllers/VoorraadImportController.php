<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Services\RdwService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        'fotos' => '_fotos', 'foto' => '_fotos', 'afbeelding' => '_fotos', 'afbeeldingen' => '_fotos',
        'images' => '_fotos', 'image' => '_fotos', 'photo' => '_fotos', 'photos' => '_fotos',
        'image_url' => '_fotos', 'foto_url' => '_fotos', 'imageurl' => '_fotos',
    ];

    /** Max foto's per auto bij import. */
    private const MAX_FOTOS_PER_CAR = 30;

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

            $car = Car::create($attrs);
            $result['created']++;

            if (! empty($row['_fotos'])) {
                $this->attachUrls($car, (string) $row['_fotos']);
            }
        }

        return back()->with('import_result', $result);
    }

    /**
     * Foto's koppelen via een ZIP. De bestandsnaam begint met het kenteken
     * (bijv. 12ABC3_1.jpg), zo weet het platform bij welke auto de foto hoort.
     * Importeer dus eerst de auto's, upload daarna de foto's.
     */
    public function fotos(Request $request)
    {
        $request->validate([
            'zip' => 'required|file|mimes:zip|max:204800', // 200 MB
        ]);

        if (! class_exists(\ZipArchive::class)) {
            return back()->with('error', 'ZIP-verwerking is niet beschikbaar op deze server (de zip-extensie ontbreekt).');
        }

        @set_time_limit(600);

        $zip = new \ZipArchive();
        if ($zip->open($request->file('zip')->getRealPath()) !== true) {
            return back()->with('error', 'Kon het ZIP-bestand niet openen.');
        }

        // Kentekens van dit bedrijf, langste eerst voor nauwkeurige prefix-matching.
        $cars = Car::where('company_id', $request->user()->company_id)
            ->whereNotNull('kenteken')->where('kenteken', '!=', '')
            ->get(['id', 'kenteken'])->keyBy('kenteken');
        $kentekens = $cars->keys()->sortByDesc(fn ($k) => strlen((string) $k))->values();

        // Entries op naam sorteren zodat _1, _2, ... in volgorde gekoppeld worden.
        $entries = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === false || str_ends_with($name, '/') || str_contains($name, '__MACOSX')) {
                continue;
            }
            $entries[] = ['i' => $i, 'name' => $name];
        }
        usort($entries, fn ($a, $b) => strnatcasecmp($a['name'], $b['name']));

        $attached = 0;
        $perCar = [];
        $unmatched = [];

        foreach ($entries as $entry) {
            $base = pathinfo($entry['name'], PATHINFO_FILENAME);
            $norm = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $base));
            if ($norm === '') {
                continue;
            }

            $kenteken = $kentekens->first(fn ($k) => str_starts_with($norm, (string) $k));
            if (! $kenteken) {
                $unmatched[$base] = true;
                continue;
            }
            if (($perCar[$kenteken] ?? 0) >= self::MAX_FOTOS_PER_CAR) {
                continue;
            }

            $bytes = $zip->getFromIndex($entry['i']);
            if ($bytes === false) {
                continue;
            }
            $ext = $this->imageExtension($bytes);
            if ($ext === null) {
                continue;
            }

            $this->storeImage($cars[$kenteken], $bytes, basename($entry['name']), $ext);
            $perCar[$kenteken] = ($perCar[$kenteken] ?? 0) + 1;
            $attached++;
        }
        $zip->close();

        return back()->with('foto_result', [
            'attached' => $attached,
            'cars' => count($perCar),
            'unmatched' => array_slice(array_keys($unmatched), 0, 30),
            'unmatched_total' => count($unmatched),
        ]);
    }

    /** Downloadbaar CSV-sjabloon met de herkende kolommen. */
    public function template(): StreamedResponse
    {
        $headers = ['kenteken', 'merk', 'model', 'bouwjaar', 'brandstof', 'kilometerstand', 'prijs', 'kleur', 'titel', 'beschrijving', 'fotos'];
        $voorbeeld = ['12-ABC-3', 'Volkswagen', 'Golf', '2019', 'Benzine', '89000', '18950', 'Grijs', 'Volkswagen Golf 1.0 TSI', 'Netjes onderhouden, NAP.', 'https://site.nl/foto1.jpg | https://site.nl/foto2.jpg'];

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

    /** Download en koppel foto's uit een cel met een of meer URL's. */
    private function attachUrls(Car $car, string $cell): void
    {
        $urls = preg_split('/[\s,|]+/', trim($cell)) ?: [];
        $count = 0;

        foreach ($urls as $url) {
            if ($count >= self::MAX_FOTOS_PER_CAR) {
                break;
            }
            $url = trim($url);
            if ($url === '' || ! $this->isSafeUrl($url)) {
                continue;
            }

            try {
                $response = Http::timeout(15)->withHeaders(['User-Agent' => 'EazyAutomotive/1.0'])->get($url);
            } catch (\Throwable) {
                continue;
            }
            if (! $response->successful()) {
                continue;
            }

            $bytes = $response->body();
            if (strlen($bytes) < 100 || strlen($bytes) > 10 * 1024 * 1024) {
                continue;
            }
            $ext = $this->imageExtension($bytes);
            if ($ext === null) {
                continue;
            }

            $name = basename((string) parse_url($url, PHP_URL_PATH)) ?: 'foto.' . $ext;
            $this->storeImage($car, $bytes, $name, $ext);
            $count++;
        }
    }

    /** Slaat foto-bytes op bij een auto en maakt de eerste foto de hoofdfoto. */
    private function storeImage(Car $car, string $bytes, string $originalName, string $ext): void
    {
        $filename = Str::random(40) . '.' . $ext;
        $path = "cars/{$car->id}/{$filename}";
        Storage::disk('public')->put($path, $bytes);

        $isFirst = ! $car->images()->exists();
        $car->images()->create([
            'path' => $path,
            'filename' => $originalName ?: $filename,
            'sort_order' => ($car->images()->max('sort_order') ?? -1) + 1,
            'is_primary' => $isFirst,
        ]);
    }

    /** Valideert dat de bytes een echte afbeelding zijn en geeft de extensie. */
    private function imageExtension(string $bytes): ?string
    {
        $info = @getimagesizefromstring($bytes);
        if ($info === false) {
            return null;
        }

        return match ($info['mime'] ?? '') {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
    }

    /** Basale SSRF-bescherming: alleen http(s) en geen interne/prive adressen. */
    private function isSafeUrl(string $url): bool
    {
        $parts = parse_url($url);
        if (! $parts || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            return false;
        }
        $host = $parts['host'] ?? '';
        if ($host === '') {
            return false;
        }

        $ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : gethostbyname($host);
        if (filter_var($ip, FILTER_VALIDATE_IP)
            && ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        return true;
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
