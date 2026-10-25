<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'student_code', 'date_of_birth', 'gender', 'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function classRooms(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'enrollments')
            ->withPivot(['enrollment_date', 'paid_tuition', 'payment_status', 'midterm_score', 'final_score', 'result_status', 'remarks'])
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    protected function casts(): array
    {
        return ['date_of_birth' => 'date:Y-m-d'];
    }
}
