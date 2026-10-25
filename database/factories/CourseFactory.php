<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Course> */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        return ['code' => fake()->unique()->bothify('KH-####??'), 'name' => 'Tiếng Anh cơ bản', 'level' => 'A1', 'duration_hours' => 48, 'is_active' => true];
    }
}
