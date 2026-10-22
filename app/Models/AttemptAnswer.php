<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttemptAnswer extends Model
{
    protected $fillable = [
        'exam_attempt_id', 'exam_section_id', 'question_id', 'position', 'type_snapshot', 'prompt_snapshot', 'group_snapshot', 'options_snapshot', 'grading_snapshot', 'response', 'is_correct', 'awarded_points', 'max_points', 'answered_at',
    ];

    protected $hidden = ['grading_snapshot'];

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function examSection(): BelongsTo
    {
        return $this->belongsTo(ExamSection::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'group_snapshot' => 'array',
            'options_snapshot' => 'array',
            'grading_snapshot' => 'array',
            'response' => 'array',
            'is_correct' => 'boolean',
            'awarded_points' => 'decimal:2',
            'max_points' => 'decimal:2',
            'answered_at' => 'datetime',
        ];
    }
}
