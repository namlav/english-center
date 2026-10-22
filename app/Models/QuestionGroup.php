<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionGroup extends Model
{
    protected $fillable = [
        'exam_section_id', 'title', 'position', 'instructions', 'passage', 'transcript', 'audio_path', 'image_path', 'response_format', 'answer_word_limit', 'rendering_data',
    ];

    protected $hidden = ['transcript'];

    public function examSection(): BelongsTo
    {
        return $this->belongsTo(ExamSection::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'answer_word_limit' => 'integer',
            'rendering_data' => 'array',
        ];
    }
}
