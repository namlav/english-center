<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttemptSection extends Model
{
    protected $fillable = [
        'exam_attempt_id', 'exam_paper_id', 'exam_section_id', 'title_snapshot', 'instructions_snapshot', 'position',
    ];

    public function examAttempt(): BelongsTo
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function examPaper(): BelongsTo
    {
        return $this->belongsTo(ExamPaper::class);
    }

    public function examSection(): BelongsTo
    {
        return $this->belongsTo(ExamSection::class);
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
