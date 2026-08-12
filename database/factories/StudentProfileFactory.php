<?php

namespace Database\Factories;

use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<StudentProfile> */
class StudentProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'faculty' => fake()->randomElement(['วิศวกรรมศาสตร์', 'วิทยาศาสตร์']),
            'major' => fake()->word(),
            'year_level' => fake()->numberBetween(1, 8),
            'bio' => fake()->sentence(),
            'phone' => fake()->numerify('0#########'),
            'contact_channel' => fake()->userName(),
            'profile_image' => null,
        ];
    }
}
