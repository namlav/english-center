<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_room_id', 'student_id', 'enrollment_date',
        'paid_tuition', 'payment_status', 'midterm_score',
        'final_score', 'result_status', 'remarks',
    ];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    protected function casts(): array
    {
        return ['enrollment_date' => 'date:Y-m-d', 'paid_tuition' => 'decimal:2', 'midterm_score' => 'decimal:2', 'final_score' => 'decimal:2'];
    }
}
