<?php

/**
 * Dutch vehicle fiscal reference tables. Verified against Belastingdienst / CBS
 * (tariefjaar 2026). Update yearly. These drive the report's fiscal section;
 * amounts derived from them are shown as an "indicatie".
 *
 * Sources: belastingdienst.nl (BPM forfaitaire tabel, bijtelling), CBS 80889ned
 * (provinciale opcenten), milieuzones per gemeente.
 */
return [
    'tariefjaar' => 2026,

    // BPM forfaitaire afschrijvingstabel: cumulative depreciation percentage of
    // the original gross BPM, by period (in months) since datum eerste toelating.
    'bpm_afschrijving' => [
        ['to' => 1, 'pct' => 0],
        ['to' => 2, 'pct' => 12],
        ['to' => 3, 'pct' => 16],
        ['to' => 4, 'pct' => 19.5],
        ['to' => 5, 'pct' => 23],
        ['to' => 6, 'pct' => 24.5],
        ['to' => 9, 'pct' => 26],
        ['to' => 10, 'pct' => 27],
        ['to' => 11, 'pct' => 28],
        ['to' => 12, 'pct' => 29],
        ['to' => 15, 'pct' => 30],
        ['to' => 18, 'pct' => 33],
        ['to' => 24, 'pct' => 36],
        ['to' => 30, 'pct' => 42],
        ['to' => 36, 'pct' => 48],
        ['to' => 42, 'pct' => 51],
        ['to' => 48, 'pct' => 54],
        ['to' => 54, 'pct' => 57],
        ['to' => 60, 'pct' => 59.52],
        ['to' => 66, 'pct' => 62],
        ['to' => 72, 'pct' => 64.52],
        ['to' => 78, 'pct' => 67],
        ['to' => 84, 'pct' => 69.52],
        ['to' => 90, 'pct' => 72],
        ['to' => 96, 'pct' => 73.5],
        ['to' => 102, 'pct' => 75],
        ['to' => 108, 'pct' => 76.5],
        ['to' => 114, 'pct' => 78],
        ['to' => 120, 'pct' => 78.95],
        ['to' => PHP_INT_MAX, 'pct' => 81],
    ],

    // Provinciale opcenten percentage on the MRB rijksdeel (2026, CBS preliminary).
    'opcenten' => [
        'Groningen' => 95.7,
        'Friesland' => 92.1,
        'Drenthe' => 92.0,
        'Overijssel' => 82.2,
        'Flevoland' => 84.7,
        'Gelderland' => 98.3,
        'Utrecht' => 86.4,
        'Noord-Holland' => 82.1,
        'Zuid-Holland' => 104.4,
        'Zeeland' => 84.4,
        'Noord-Brabant' => 87.0,
        'Limburg' => 88.5,
    ],

    // Bijtelling (private use of a company car), tariefjaar 2026.
    'bijtelling' => [
        'standaard_pct' => 22,
        'ev_pct' => 18,
        'ev_drempel_eur' => 30000,
        'ev_pct_boven_drempel' => 22,
        'youngtimer_jaar' => 16,
        'youngtimer_pct' => 35, // over de dagwaarde
    ],

    // Milieuzone access for passenger cars: minimum diesel EURO class per city.
    // Petrol / LPG / CNG / electric have unrestricted access.
    'milieuzone' => [
        'diesel_min_euro' => [
            'Amsterdam' => 5,
            'Arnhem' => 5,
            'Den Haag' => 4,
            'Utrecht' => 5,
        ],
    ],

    // Official calculator for the exact MRB amount (we show determinants, not a fabricated figure).
    'mrb_rekenhulp_url' => 'https://www.belastingdienst.nl/rekenhulpen/motorrijtuigenbelasting/',
];
