<?php

namespace App\Console\Commands;

use App\Models\MarketListing;
use App\Services\Valuation\Sources\CsvMarketSource;
use App\Services\Valuation\Sources\HttpMarketSource;
use App\Services\Valuation\Sources\InventoryMarketSource;
use App\Services\Valuation\Sources\MarketSource;
use Illuminate\Console\Command;

/**
 * Feeds the valuation engine with market listings.
 *   php artisan market:ingest                       # alle live bronnen + opschonen
 *   php artisan market:ingest --source=inventory    # alleen eigen voorraad
 *   php artisan market:ingest --source=http          # alleen de live feed
 *   php artisan market:ingest --source=csv --file=storage/app/markt.csv
 *   php artisan market:ingest --prune=90             # verwijder advertenties > 90 dagen
 */
class IngestMarketData extends Command
{
    protected $signature = 'market:ingest
        {--source= : csv | http | inventory (leeg = alle live bronnen)}
        {--file= : Pad naar het CSV-bestand (alleen bij --source=csv)}
        {--truncate : Leeg de tabel eerst}
        {--prune= : Verwijder advertenties ouder dan N dagen}';

    protected $description = 'Ververst de marktdata voor de taxatie-engine (voorraad, live feed of CSV)';

    public function handle(): int
    {
        if ($this->option('truncate')) {
            MarketListing::truncate();
            $this->warn('Tabel market_listings geleegd.');
        }

        $sources = $this->resolveSources();
        if ($sources === []) {
            $this->error('Geen bron beschikbaar. Zet MARKET_FEED_URL of gebruik --source=inventory / --source=csv.');

            return self::FAILURE;
        }

        $total = 0;
        foreach ($sources as $source) {
            $this->line("Bron: {$source->name()}");
            try {
                $total += $this->ingest($source);
            } catch (\Throwable $e) {
                report($e);
                $this->error("  {$source->name()} mislukt: {$e->getMessage()}");
            }
        }

        if ($this->option('prune') !== null) {
            $days = max(1, (int) $this->option('prune'));
            $deleted = MarketListing::where('captured_at', '<', now()->subDays($days))->delete();
            $this->info("Opgeschoond: {$deleted} advertenties ouder dan {$days} dagen verwijderd.");
        }

        $this->info("Klaar: {$total} advertenties verwerkt. Totaal in database: " . MarketListing::count());

        return self::SUCCESS;
    }

    private function ingest(MarketSource $source): int
    {
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
                $this->line("  {$count} verwerkt...");
            }
        }
        $this->info("  {$source->name()}: {$count} verwerkt.");

        return $count;
    }

    /** @return array<int, MarketSource> */
    private function resolveSources(): array
    {
        $minPrice = (int) config('valuation.inventory_min_price', 50000);
        $feedUrl = config('valuation.http_feed_url');
        $feedToken = config('valuation.http_feed_token');

        return match ($this->option('source')) {
            'csv' => $this->option('file') ? [new CsvMarketSource($this->option('file'))] : [],
            'http' => $feedUrl ? [new HttpMarketSource($feedUrl, $feedToken)] : [],
            'inventory' => [new InventoryMarketSource($minPrice)],
            null, '' => array_values(array_filter([
                new InventoryMarketSource($minPrice),
                $feedUrl ? new HttpMarketSource($feedUrl, $feedToken) : null,
            ])),
            default => [],
        };
    }
}
