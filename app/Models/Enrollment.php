<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'class_room_id', 'student_id', 'enrollment_date',
        'paid_tuition', 'payment_status', 'midterm_score',
        'final_score', 'result_status', 'remarks'
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
