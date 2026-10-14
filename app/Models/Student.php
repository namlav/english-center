<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'student_code', 'date_of_birth', 'gender', 'address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'enrollments')
                    ->withPivot(['enrollment_date', 'paid_tuition', 'payment_status', 'midterm_score', 'final_score', 'result_status', 'remarks'])
                    ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
