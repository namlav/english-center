<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id', 'teacher_code', 'specialization', 'degree', 'bio'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }
}
