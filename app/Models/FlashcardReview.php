<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashcardReview extends Model
{
    protected $fillable = [
        'user_id', 'flashcard_id', 'ease_factor', 'interval_days', 'repetitions', 'last_rating', 'due_at', 'last_reviewed_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }

    public function scopeDueFor(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id)->where('due_at', '<=', now())->orderBy('due_at');
    }

    protected function casts(): array
    {
        return [
            'ease_factor' => 'decimal:2',
            'interval_days' => 'integer',
            'repetitions' => 'integer',
            'last_rating' => 'integer',
            'due_at' => 'datetime',
            'last_reviewed_at' => 'datetime',
        ];
    }
}
