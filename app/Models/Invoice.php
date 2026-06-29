<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'company_id', 'customer_id', 'car_id', 'number', 'sequence', 'year',
    'status', 'vat_scheme', 'prices_include_vat', 'date', 'due_date',
    'subtotal', 'vat_amount', 'total', 'margin_base', 'amount_paid',
    'notes', 'footer', 'bill_to_name', 'bill_to_address', 'sent_at', 'paid_at',
])]
class Invoice extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'concept' => ['label' => 'Concept', 'icon' => 'fa-pen', 'bg' => 'bg-gray-100', 'text' => 'text-gray-600'],
        'verzonden' => ['label' => 'Verzonden', 'icon' => 'fa-paper-plane', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
        'deels_betaald' => ['label' => 'Deels betaald', 'icon' => 'fa-hourglass-half', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
        'betaald' => ['label' => 'Betaald', 'icon' => 'fa-circle-check', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
        'vervallen' => ['label' => 'Vervallen', 'icon' => 'fa-triangle-exclamation', 'bg' => 'bg-red-50', 'text' => 'text-red-600'],
        'geannuleerd' => ['label' => 'Geannuleerd', 'icon' => 'fa-ban', 'bg' => 'bg-gray-100', 'text' => 'text-gray-400'],
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'due_date' => 'date',
            'sent_at' => 'datetime',
            'paid_at' => 'datetime',
            'prices_include_vat' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class)->orderBy('position');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class)->orderBy('date');
    }

    /** Format an amount in cents as a Dutch euro string. */
    public static function eur(int $cents): string
    {
        return '€ ' . number_format($cents / 100, 2, ',', '.');
    }

    /** Recalculate all totals from the lines, honouring the marge or btw scheme. */
    public function recalculate(): void
    {
        $subtotal = 0;
        $vat = 0;
        $total = 0;
        $margin = 0;

        foreach ($this->lines as $line) {
            $qty = (float) $line->quantity;
            $base = (int) round(((int) $line->unit_price) * $qty); // cents

            if ($this->vat_scheme === 'marge') {
                // Margin scheme: no VAT shown on the invoice. We track the gross
                // margin (sale minus purchase) for the VAT return.
                $line->line_total = $base;
                $subtotal += $base;
                $total += $base;
                if ($line->purchase_price !== null) {
                    $margin += max(0, $base - (int) round(((int) $line->purchase_price) * $qty));
                }
            } elseif ($this->prices_include_vat) {
                $gross = $base;
                $net = (int) round($gross / (1 + ((int) $line->vat_rate) / 100));
                $line->line_total = $net;
                $subtotal += $net;
                $vat += $gross - $net;
                $total += $gross;
            } else {
                $net = $base;
                $lineVat = (int) round($net * ((int) $line->vat_rate) / 100);
                $line->line_total = $net;
                $subtotal += $net;
                $vat += $lineVat;
                $total += $net + $lineVat;
            }

            $line->save();
        }

        $this->subtotal = $subtotal;
        $this->vat_amount = $vat;
        $this->total = $total;
        $this->margin_base = $margin;
        $this->save();
    }

    /** Assign the next gap-free invoice number for this company and year (on finalising). */
    public function assignNumber(): void
    {
        if ($this->number) {
            return;
        }

        $year = ($this->date ?: now())->year;
        $prefix = $this->company->invoice_prefix ?: '';
        $sequence = (int) static::where('company_id', $this->company_id)->where('year', $year)->max('sequence') + 1;

        $this->year = $year;
        $this->sequence = $sequence;
        $this->number = sprintf('%s%d-%04d', $prefix, $year, $sequence);
    }

    /** Recompute the paid amount and status from the registered payments. */
    public function syncPaymentStatus(): void
    {
        $this->amount_paid = (int) $this->payments()->sum('amount');

        if (in_array($this->status, ['concept', 'geannuleerd'], true)) {
            $this->save();

            return;
        }

        if ($this->total > 0 && $this->amount_paid >= $this->total) {
            $this->status = 'betaald';
            $this->paid_at = $this->paid_at ?: now();
        } elseif ($this->amount_paid > 0) {
            $this->status = 'deels_betaald';
        } elseif ($this->due_date && $this->due_date->isPast()) {
            $this->status = 'vervallen';
        } else {
            $this->status = 'verzonden';
        }

        $this->save();
    }

    public function getOutstandingAttribute(): int
    {
        return max(0, (int) $this->total - (int) $this->amount_paid);
    }

    public function isConcept(): bool
    {
        return $this->status === 'concept';
    }

    public function isPaid(): bool
    {
        return $this->status === 'betaald';
    }

    public function getStatusBadgeAttribute(): array
    {
        return self::STATUSES[$this->status] ?? self::STATUSES['concept'];
    }

    /** The legally required note for margin-scheme invoices. */
    public function getMargeNoteAttribute(): ?string
    {
        return $this->vat_scheme === 'marge'
            ? 'Margeregeling auto\'s van toepassing. BTW is niet aftrekbaar.'
            : null;
    }
}
