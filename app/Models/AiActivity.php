<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit trail of every mutating action the AI colleague performed, with the data
 * needed to undo it. This is the safety net for autonomous mode.
 */
class AiActivity extends Model
{
    protected $fillable = [
        'company_id', 'user_id', 'ai_conversation_id',
        'tool', 'summary', 'subject_type', 'subject_id', 'undo_data', 'undone_at',
    ];

    protected function casts(): array
    {
        return [
            'undo_data' => 'array',
            'undone_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isUndone(): bool
    {
        return $this->undone_at !== null;
    }
}
