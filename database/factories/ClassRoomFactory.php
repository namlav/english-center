<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ClassRoom> */
class ClassRoomFactory extends Factory
{
    public function definition(): array
    {
        return ['code' => fake()->unique()->bothify('LH-####??'), 'name' => 'Lớp Tiếng Anh', 'course_id' => Course::factory(), 'teacher_id' => Teacher::factory(), 'start_date' => '2026-10-01', 'schedule' => 'Thứ 2, 4 — 18:00–19:30', 'tuition_fee' => '2500000.00', 'max_students' => 25, 'status' => 'upcoming'];
    }
}
