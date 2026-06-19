<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'merk', 'model', 'bouwjaar', 'brandstof', 'kilometerstand',
    'prijs', 'source', 'external_id', 'captured_at',
])]
class MarketListing extends Model
{
    protected function casts(): array
    {
        return [
            'captured_at' => 'datetime',
        ];
    }
}
