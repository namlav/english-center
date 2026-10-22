<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flashcard extends Model
{
    protected $fillable = [
        'flashcard_deck_id', 'front', 'back', 'pronunciation', 'example', 'audio_path', 'image_path', 'position',
    ];

    public function flashcardDeck(): BelongsTo
    {
        return $this->belongsTo(FlashcardDeck::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class);
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
