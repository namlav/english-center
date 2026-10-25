<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Student> */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory()->student(), 'student_code' => fake()->unique()->bothify('HV-####??'), 'date_of_birth' => '2005-01-15', 'gender' => 'other', 'address' => 'TP. Hồ Chí Minh'];
    }
}
