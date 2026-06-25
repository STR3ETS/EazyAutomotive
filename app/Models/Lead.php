<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'company_id', 'car_id', 'type', 'naam', 'email', 'telefoon', 'bericht',
    'status', 'source', 'assigned_to', 'follow_up_at', 'last_contacted_at',
    'notes', 'data', 'ip_address', 'user_agent',
])]
class Lead extends Model
{
    use SoftDeletes;

    /** Pipeline statuses, in order, with display + badge styling. */
    public const STATUSES = [
        'nieuw' => ['label' => 'Nieuw', 'icon' => 'fa-circle-dot', 'bg' => 'bg-blue-50', 'text' => 'text-blue-500'],
        'contact' => ['label' => 'Contact gehad', 'icon' => 'fa-phone', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
        'afspraak' => ['label' => 'Afspraak', 'icon' => 'fa-calendar-check', 'bg' => 'bg-eazy-50', 'text' => 'text-eazy-dark'],
        'gewonnen' => ['label' => 'Gewonnen', 'icon' => 'fa-trophy', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
        'verloren' => ['label' => 'Verloren', 'icon' => 'fa-circle-xmark', 'bg' => 'bg-red-50', 'text' => 'text-red-500'],
    ];

    /** Lead sources/types, with display + icon. */
    public const TYPES = [
        'proefrit' => ['label' => 'Proefrit', 'icon' => 'fa-calendar-check'],
        'contact' => ['label' => 'Contact', 'icon' => 'fa-envelope'],
        'inruil' => ['label' => 'Inruil', 'icon' => 'fa-right-left'],
        'financiering' => ['label' => 'Financiering', 'icon' => 'fa-coins'],
        'overig' => ['label' => 'Overig', 'icon' => 'fa-tag'],
    ];

    protected function casts(): array
    {
        return [
            'follow_up_at' => 'datetime',
            'last_contacted_at' => 'datetime',
            'data' => 'array',
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

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['gewonnen', 'verloren']);
    }

    public function getStatusBadgeAttribute(): array
    {
        return self::STATUSES[$this->status] ?? self::STATUSES['nieuw'];
    }

    public function getTypeMetaAttribute(): array
    {
        return self::TYPES[$this->type] ?? self::TYPES['overig'];
    }
}
