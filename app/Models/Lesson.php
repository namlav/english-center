<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    protected $fillable = [
        'course_section_id', 'title', 'slug', 'type', 'content', 'media_path', 'duration_seconds', 'position', 'is_preview', 'is_published',
    ];

    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function examPapers(): BelongsToMany
    {
        return $this->belongsToMany(ExamPaper::class, 'lesson_exam_papers')->withPivot(['position', 'is_required'])->withTimestamps()->orderByPivot('position');
    }

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'position' => 'integer',
            'is_preview' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
