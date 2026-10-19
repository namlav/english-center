<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    use HasFactory;

    public const STATUSES = ['upcoming' => 'Sắp khai giảng', 'ongoing' => 'Đang học', 'completed' => 'Đã kết thúc', 'cancelled' => 'Đã hủy'];

    protected $fillable = [
        'code', 'name', 'course_id', 'teacher_id', 'start_date',
        'schedule', 'tuition_fee', 'max_students', 'status',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments')
            ->withPivot(['id', 'enrollment_date', 'paid_tuition', 'payment_status', 'midterm_score', 'final_score', 'result_status', 'remarks'])
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (trim($filters['keyword'] ?? '') !== '') {
            $query->where(function (Builder $q) use ($filters): void {
                $q->where('name', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('code', 'like', '%'.$filters['keyword'].'%');
            });
        }
        if (! empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }
        if (! empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    protected function casts(): array
    {
        return ['start_date' => 'date:Y-m-d', 'tuition_fee' => 'decimal:2', 'max_students' => 'integer'];
    }
}
