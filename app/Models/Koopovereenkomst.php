<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Een koop-/verkoopovereenkomst tussen de dealer en een koper voor een voertuig.
 * Koper en voertuig staan als snapshot in JSON, zodat het getekende document niet
 * verandert als de onderliggende auto of klant later wordt aangepast.
 */
#[Fillable([
    'company_id', 'car_id', 'customer_id', 'nummer', 'verkoopprijs', 'btw_type',
    'inruil_omschrijving', 'inruil_bedrag', 'leverdatum', 'garantie',
    'bijzonderheden', 'koper', 'voertuig', 'status',
])]
class Koopovereenkomst extends Model
{
    protected $table = 'koopovereenkomsten';

    protected function casts(): array
    {
        return [
            'koper' => 'array',
            'voertuig' => 'array',
            'leverdatum' => 'date',
            'verkoopprijs' => 'integer',
            'inruil_bedrag' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** Netto te betalen na aftrek van een eventuele inruil. */
    public function teBetalen(): int
    {
        return max(0, $this->verkoopprijs - (int) $this->inruil_bedrag);
    }

    /** Gap-vrij volgnummer per bedrijf per jaar, bijv. VK-2026-0001. */
    public function assignNumber(): void
    {
        if ($this->nummer) {
            return;
        }

        $jaar = now()->format('Y');
        $laatste = static::where('company_id', $this->company_id)
            ->where('nummer', 'like', "VK-{$jaar}-%")
            ->orderByDesc('nummer')
            ->value('nummer');

        $volgnr = $laatste ? ((int) substr($laatste, -4)) + 1 : 1;
        $this->nummer = sprintf('VK-%s-%04d', $jaar, $volgnr);
    }

    public static function euro(?int $centen): string
    {
        return '€ ' . number_format((int) $centen / 100, 2, ',', '.');
    }
}
