<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Question extends Model
{
    protected $fillable = [
        'question_group_id', 'exam_section_id', 'position', 'prompt', 'type', 'max_points',
    ];

    protected $hidden = ['answerKey'];

    public function questionGroup(): BelongsTo
    {
        return $this->belongsTo(QuestionGroup::class);
    }

    public function examSection(): BelongsTo
    {
        return $this->belongsTo(ExamSection::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('position');
    }

    public function answerKey(): HasOne
    {
        return $this->hasOne(QuestionAnswerKey::class);
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'max_points' => 'decimal:2',
        ];
    }
}
