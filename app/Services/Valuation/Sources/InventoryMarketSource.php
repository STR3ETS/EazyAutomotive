<?php

namespace App\Services\Valuation\Sources;

use App\Models\Car;

/**
 * Live market data from the platform's own inventory: the active cars of every
 * EazyAutomotive dealer are real asking prices. This source needs no external
 * feed, works immediately, and gets richer as more dealers join. Prices are
 * already stored in cents on the car.
 */
class InventoryMarketSource implements MarketSource
{
    public function __construct(private int $minPriceCents = 50000) {}

    public function name(): string
    {
        return 'inventory';
    }

    public function listings(): iterable
    {
        $cars = Car::query()
            ->where('status', 'active')
            ->whereNotNull('prijs')
            ->where('prijs', '>=', $this->minPriceCents)
            ->select('id', 'merk', 'handelsbenaming', 'bouwjaar', 'brandstof_omschrijving', 'kilometerstand', 'prijs')
            ->cursor();

        foreach ($cars as $car) {
            if (! $car->merk) {
                continue;
            }

            yield [
                'merk' => strtoupper($car->merk),
                'model' => $car->handelsbenaming ?: null,
                'bouwjaar' => $car->bouwjaar ? (int) $car->bouwjaar : null,
                'brandstof' => $car->brandstof_omschrijving ?: null,
                'kilometerstand' => $car->kilometerstand ? (int) $car->kilometerstand : null,
                'prijs' => (int) $car->prijs, // already in cents
                'external_id' => 'car-' . $car->id,
            ];
        }
    }
}
