<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Teacher> */
class TeacherFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory()->teacher(), 'teacher_code' => fake()->unique()->bothify('GV-####??'), 'specialization' => 'Tiếng Anh giao tiếp', 'degree' => 'Cử nhân Ngôn ngữ Anh'];
    }
}
