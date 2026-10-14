<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'code', 'name', 'level', 'duration_hours', 'description', 'is_active'
    ];

    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }
}
