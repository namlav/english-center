<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamComment extends Model
{
    protected $fillable = [
        'exam_paper_id', 'user_id', 'content', 'status',
    ];

    public function examPaper(): BelongsTo
    {
        return $this->belongsTo(ExamPaper::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
