<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamAttempt extends Model
{
    protected $fillable = [
        'user_id', 'exam_paper_id', 'mode', 'status', 'title_snapshot', 'started_at', 'time_limit_seconds', 'submitted_at', 'graded_at', 'elapsed_seconds', 'earned_points', 'max_points', 'scaled_score',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examPaper(): BelongsTo
    {
        return $this->belongsTo(ExamPaper::class);
    }

    public function selectedSections(): HasMany
    {
        return $this->hasMany(ExamAttemptSection::class)->orderBy('position');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class)->orderBy('position');
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'time_limit_seconds' => 'integer',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'elapsed_seconds' => 'integer',
            'earned_points' => 'decimal:2',
            'max_points' => 'decimal:2',
            'scaled_score' => 'decimal:2',
        ];
    }
}
