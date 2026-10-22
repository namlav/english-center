<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ExamPaper extends Model
{
    protected $fillable = [
        'learning_category_id', 'created_by', 'title', 'slug', 'description', 'skill', 'duration_seconds', 'status', 'published_at', 'exam_collection_id',
    ];

    public function learningCategory(): BelongsTo
    {
        return $this->belongsTo(LearningCategory::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(ExamCollection::class, 'exam_collection_id');
    }

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'lesson_exam_papers')->withPivot(['position', 'is_required'])->withTimestamps();
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ExamSection::class)->orderBy('position');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(Question::class, ExamSection::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ExamComment::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('published_at', '<=', now())
            ->whereHas('learningCategory', fn (Builder $category): Builder => $category->where('is_active', true))
            ->where(fn (Builder $collection): Builder => $collection->whereNull('exam_collection_id')
                ->orWhereHas('collection', fn (Builder $parent): Builder => $parent->where('is_active', true)));
    }

    public function scopeCatalogFilter(Builder $query, array $filters): Builder
    {
        $keyword = trim($filters['keyword'] ?? '');

        return $query->when($keyword !== '', fn (Builder $search): Builder => $search->where(fn (Builder $match): Builder => $match
            ->where('title', 'like', '%'.$keyword.'%')
            ->orWhereHas('learningCategory',fn(Builder $category):Builder=>$category->where('name','like','%'.$keyword.'%'))
            ->orWhereHas('collection', fn (Builder $collection): Builder => $collection->where('title', 'like', '%'.$keyword.'%'))
            ->orWhereHas('sections.tags', fn (Builder $tag): Builder => $tag->where('name', 'like', '%'.$keyword.'%'))))
            ->when(! empty($filters['category']), fn (Builder $search): Builder => $search->whereHas('learningCategory', fn (Builder $category): Builder => $category->where('slug', $filters['category'])))
            ->when(! empty($filters['skill']), fn (Builder $search): Builder => $search->where('skill', $filters['skill']))
            ->when(! empty($filters['collection']), fn (Builder $search): Builder => $search->whereHas('collection', fn (Builder $collection): Builder => $collection->where('slug', $filters['collection'])))
            ->when(! empty($filters['tag']), fn (Builder $search): Builder => $search->whereHas('sections.tags', fn (Builder $tag): Builder => $tag->where('slug', $filters['tag'])));
    }

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'published_at' => 'datetime',
        ];
    }
}
