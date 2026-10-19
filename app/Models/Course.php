<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'level', 'duration_hours', 'description', 'is_active',
    ];

    public function classRooms(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'duration_hours' => 'integer'];
    }
}
