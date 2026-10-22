<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseLearningProfile extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'learning_reason', 'consultation_opt_in', 'consented_at', 'profile_completed_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(CourseLearningGoal::class);
    }

    protected function casts(): array
    {
        return [
            'consultation_opt_in' => 'boolean',
            'consented_at' => 'datetime',
            'profile_completed_at' => 'datetime',
        ];
    }
}
