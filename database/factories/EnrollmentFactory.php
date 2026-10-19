<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Enrollment> */
class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return ['class_room_id' => ClassRoom::factory(), 'student_id' => Student::factory(), 'enrollment_date' => '2026-09-25', 'paid_tuition' => '0.00', 'payment_status' => 'unpaid', 'result_status' => 'studying'];
    }
}
