<?php

namespace App\Console\Commands;

use App\Models\MarketListing;
use App\Services\Valuation\Sources\CsvMarketSource;
use App\Services\Valuation\Sources\MarketSource;
use Illuminate\Console\Command;

/**
 * Imports market listings used by the valuation engine.
 *   php artisan market:ingest storage/app/markt.csv
 *   php artisan market:ingest storage/app/markt.csv --truncate
 */
class IngestMarketData extends Command
{
    protected $signature = 'market:ingest {file : Pad naar het CSV-bestand} {--source=csv} {--truncate : Leeg de tabel eerst}';

    protected $description = 'Importeer marktadvertenties (CSV) voor de taxatie-engine';

    public function handle(): int
    {
        $source = $this->resolveSource();
        if (!$source) {
            $this->error("Onbekende bron: {$this->option('source')}");
            return self::FAILURE;
        }

        if ($this->option('truncate')) {
            MarketListing::truncate();
            $this->warn('Tabel market_listings geleegd.');
        }

        $count = 0;
        foreach ($source->listings() as $row) {
            MarketListing::updateOrCreate(
                ['source' => $source->name(), 'external_id' => $row['external_id']],
                [
                    'merk' => $row['merk'],
                    'model' => $row['model'],
                    'bouwjaar' => $row['bouwjaar'],
                    'brandstof' => $row['brandstof'],
                    'kilometerstand' => $row['kilometerstand'],
                    'prijs' => $row['prijs'],
                    'captured_at' => now(),
                ],
            );
            $count++;
            if ($count % 500 === 0) {
                $this->info("  {$count} verwerkt...");
            }
        }

        $this->info("Klaar: {$count} advertenties geïmporteerd. Totaal in database: " . MarketListing::count());
        return self::SUCCESS;
    }

    private function resolveSource(): ?MarketSource
    {
        return match ($this->option('source')) {
            'csv' => new CsvMarketSource($this->argument('file')),
            default => null,
        };
    }
}
