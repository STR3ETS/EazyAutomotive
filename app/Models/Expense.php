<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'company_id', 'car_id', 'date', 'supplier', 'description', 'category',
    'amount_excl', 'vat_rate', 'vat_amount', 'amount_incl',
    'attachment_path', 'attachment_name', 'notes',
])]
class Expense extends Model
{
    use SoftDeletes;

    public const CATEGORIES = [
        'voertuig_marge' => 'Inkoop voertuig (marge)',
        'voertuig_btw' => 'Inkoop voertuig (met BTW)',
        'onderhoud' => 'Onderhoud / reparatie',
        'transport' => 'Transport / logistiek',
        'marketing' => 'Marketing / advertenties',
        'kantoor' => 'Kantoor / administratie',
        'huur' => 'Huur / huisvesting',
        'verzekering' => 'Verzekering',
        'gereedschap' => 'Gereedschap / materiaal',
        'overig' => 'Overig',
    ];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Overig';
    }
}
