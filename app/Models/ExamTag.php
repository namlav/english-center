<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExamTag extends Model
{
    protected $fillable = [
        'name', 'slug', 'kind',
    ];

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(ExamSection::class, 'exam_section_tags')->withTimestamps();
    }
}
