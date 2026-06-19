<?php

namespace App\Services\Valuation\Sources;

use RuntimeException;

/**
 * Reads market listings from a CSV file. Expected header (semicolon or comma):
 *   merk;model;bouwjaar;brandstof;kilometerstand;prijs
 * "prijs" is in euros (e.g. 18950). Lets a dealer feed real market data now,
 * exported from any source, without waiting for a live feed.
 */
class CsvMarketSource implements MarketSource
{
    public function __construct(private string $path) {}

    public function name(): string
    {
        return 'csv';
    }

    public function listings(): iterable
    {
        if (!is_file($this->path)) {
            throw new RuntimeException("CSV-bestand niet gevonden: {$this->path}");
        }

        $handle = fopen($this->path, 'r');
        if ($handle === false) {
            throw new RuntimeException("Kan CSV-bestand niet openen: {$this->path}");
        }

        try {
            $delimiter = $this->detectDelimiter($handle);
            $header = $this->readRow($handle, $delimiter);
            if (!$header) {
                return;
            }
            // Strip a UTF-8 BOM from the first header cell (Excel/Windows exports add it),
            // otherwise the first column name becomes "\xEF\xBB\xBFmerk" and every row is skipped.
            if (isset($header[0])) {
                $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header[0]);
            }
            $map = array_flip(array_map(fn ($h) => strtolower(trim($h)), $header));

            if (!isset($map['merk'])) {
                throw new RuntimeException("Vereiste kolom 'merk' ontbreekt in de CSV-header.");
            }

            $lineNo = 0;
            $row = null;
            while (($row = $this->readRow($handle, $delimiter)) !== null) {
                $lineNo++;
                $get = fn (string $key) => isset($map[$key]) && isset($row[$map[$key]]) ? trim($row[$map[$key]]) : null;

                $merk = $get('merk');
                $prijsEur = $get('prijs');
                if (!$merk || $prijsEur === null || $prijsEur === '') {
                    continue;
                }

                // Strip currency symbols/spaces first, then multiply before rounding so cents survive.
                $prijsClean = preg_replace('/[^0-9,.]/', '', $prijsEur);
                $prijs = (int) round(((float) str_replace(['.', ','], ['', '.'], $prijsClean)) * 100);
                if ($prijs <= 0) {
                    continue;
                }

                yield [
                    'merk' => strtoupper($merk),
                    'model' => $get('model') ?: null,
                    'bouwjaar' => ($b = $get('bouwjaar')) ? (int) $b : null,
                    'brandstof' => $get('brandstof') ?: null,
                    'kilometerstand' => ($k = $get('kilometerstand')) !== null && $k !== '' ? (int) preg_replace('/\D/', '', $k) : null,
                    'prijs' => $prijs,
                    'external_id' => $get('id') ?: ('row-' . $lineNo . '-' . substr(md5(implode('|', $row)), 0, 16)),
                ];
            }
        } finally {
            fclose($handle);
        }
    }

    private function detectDelimiter($handle): string
    {
        $pos = ftell($handle);
        $firstLine = fgets($handle);
        fseek($handle, $pos);
        return (substr_count((string) $firstLine, ';') >= substr_count((string) $firstLine, ',')) ? ';' : ',';
    }

    private function readRow($handle, string $delimiter): ?array
    {
        $row = fgetcsv($handle, 0, $delimiter, '"', '');
        if ($row === false || $row === null) {
            return null;
        }
        if (count($row) === 1 && ($row[0] === null || trim((string) $row[0]) === '')) {
            return $this->readRow($handle, $delimiter);
        }
        return $row;
    }
}
