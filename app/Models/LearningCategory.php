<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'is_active',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function examPapers(): HasMany
    {
        return $this->hasMany(ExamPaper::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
