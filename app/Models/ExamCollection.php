<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamCollection extends Model
{
    protected $fillable = [
        'title', 'slug', 'publication_year', 'description', 'is_active',
    ];

    public function examPapers(): HasMany
    {
        return $this->hasMany(ExamPaper::class);
    }

    protected function casts(): array
    {
        return [
            'publication_year' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
