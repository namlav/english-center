<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionAnswerKey extends Model
{
    protected $fillable = [
        'question_id', 'accepted_answers', 'case_sensitive', 'rubric', 'explanation',
    ];

    protected $hidden = ['accepted_answers', 'case_sensitive', 'rubric', 'explanation'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    protected function casts(): array
    {
        return [
            'accepted_answers' => 'array',
            'case_sensitive' => 'boolean',
            'rubric' => 'array',
        ];
    }
}
