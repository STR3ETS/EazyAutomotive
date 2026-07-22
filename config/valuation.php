<?php

return [
    // Optional live market feed (a licensed feed or partner endpoint). Must return
    // JSON: a top-level array (or {"data": [...]}) of listings with keys
    // merk, model, bouwjaar, brandstof, kilometerstand, prijs (euros), id.
    'http_feed_url' => env('MARKET_FEED_URL'),
    'http_feed_token' => env('MARKET_FEED_TOKEN'),

    // Live "kijkende" taxatie: bij een taxatie worden echte vergelijkbare
    // advertenties van Marktplaats opgehaald en de waarde daaruit berekend.
    // Uitschakelen kan met MARKET_LIVE_LOOKUP=false.
    'live_lookup' => (bool) env('MARKET_LIVE_LOOKUP', true),

    // Inruilindicatie voor de taxatie-widget: de marktwaarde is een VRAAGprijs
    // (retail). Een inruilwaarde ligt daaronder. Dit is de factor van retail naar
    // inruil (0.85 = inruil ligt ~15% onder de marktvraagprijs).
    'inruil_factor' => (float) env('MARKET_INRUIL_FACTOR', 0.85),

    // Listings not refreshed within this window are pruned, so the data stays live
    // (sold or removed cars drop out).
    'freshness_days' => (int) env('MARKET_FRESHNESS_DAYS', 90),

    // Ignore junk inventory prices below this (cents) when snapshotting own stock.
    'inventory_min_price' => (int) env('MARKET_MIN_PRICE', 50000),
];
