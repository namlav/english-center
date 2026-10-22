<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseLearningGoal extends Model
{
    protected $fillable = [
        'course_learning_profile_id', 'score_scale_code', 'min_score', 'max_score', 'score_step', 'current_score', 'target_score', 'target_exam_date',
    ];

    public function courseLearningProfile(): BelongsTo
    {
        return $this->belongsTo(CourseLearningProfile::class);
    }

    public function scoreScale(): BelongsTo
    {
        return $this->belongsTo(LearningScoreScale::class, 'score_scale_code', 'code');
    }

    protected function casts(): array
    {
        return [
            'min_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'score_step' => 'decimal:2',
            'current_score' => 'decimal:2',
            'target_score' => 'decimal:2',
            'target_exam_date' => 'date:Y-m-d',
        ];
    }
}
