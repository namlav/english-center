<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'level', 'duration_hours', 'description', 'is_active',
        'learning_category_id', 'slug', 'delivery_mode', 'summary', 'thumbnail_path',
        'price', 'sale_price', 'access_days', 'published_at', 'learning_outcomes', 'is_for_sale',
        'catalog_key', 'intro_video_url', 'intro_video_path', 'audience', 'curriculum_outline',
    ];

    public function classRooms(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function learningCategory(): BelongsTo
    {
        return $this->belongsTo(LearningCategory::class);
    }

    public function learningProfiles(): HasMany
    {
        return $this->hasMany(CourseLearningProfile::class);
    }

    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_instructors')->withPivot('position')->withTimestamps()->orderByPivot('position');
    }

    public function skillNames(): array
    {
        return DB::table('course_skills')->where('course_id', $this->id)->orderBy('position')->pluck('skill')->all();
    }

    public function scopeForSkill(Builder $query, string $skill): Builder
    {
        return $query->whereIn('courses.id', DB::table('course_skills')->select('course_id')->where('skill', $skill));
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('position');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, CourseSection::class);
    }

    public function onlineEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    public function flashcardDecks(): HasMany
    {
        return $this->hasMany(FlashcardDeck::class);
    }

    public function scopePublishedOnline(Builder $query): Builder
    {
        return $query->where('delivery_mode', 'online')->where('is_active', true)
            ->where('published_at', '<=', now())
            ->whereHas('learningCategory', fn (Builder $category): Builder => $category->where('is_active', true));
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean', 'is_for_sale'=>'boolean', 'duration_hours' => 'integer',
            'price' => 'decimal:2', 'sale_price' => 'decimal:2',
            'access_days' => 'integer', 'published_at' => 'datetime',
            'learning_outcomes' => 'array', 'curriculum_outline' => 'array',
        ];
    }
}
