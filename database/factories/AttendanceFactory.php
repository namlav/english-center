<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Attendance> */
class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        return ['class_room_id' => ClassRoom::factory(), 'student_id' => fn (array $attributes): int => Enrollment::factory()->create(['class_room_id' => $attributes['class_room_id']])->student_id, 'attendance_date' => '2026-10-01', 'status' => 'present'];
    }
}
