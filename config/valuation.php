<?php

return [
    // Optional live market feed (a licensed feed or partner endpoint). Must return
    // JSON: a top-level array (or {"data": [...]}) of listings with keys
    // merk, model, bouwjaar, brandstof, kilometerstand, prijs (euros), id.
    'http_feed_url' => env('MARKET_FEED_URL'),
    'http_feed_token' => env('MARKET_FEED_TOKEN'),

    // Listings not refreshed within this window are pruned, so the data stays live
    // (sold or removed cars drop out).
    'freshness_days' => (int) env('MARKET_FRESHNESS_DAYS', 90),

    // Ignore junk inventory prices below this (cents) when snapshotting own stock.
    'inventory_min_price' => (int) env('MARKET_MIN_PRICE', 50000),
];
