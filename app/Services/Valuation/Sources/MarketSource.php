<?php

namespace App\Services\Valuation\Sources;

/**
 * A source of used-car market listings for the valuation engine.
 * Implementations: CsvMarketSource (now). A future HttpMarketSource can pull a
 * licensed feed or aggregator without touching the engine.
 */
interface MarketSource
{
    /**
     * @return iterable<array{merk:string, model:?string, bouwjaar:?int, brandstof:?string, kilometerstand:?int, prijs:int, external_id:?string}>
     *         prijs is in cents.
     */
    public function listings(): iterable;

    public function name(): string;
}
