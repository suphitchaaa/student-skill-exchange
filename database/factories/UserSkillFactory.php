<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<UserSkill> */
class UserSkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'skill_id' => Skill::factory(),
            'skill_type' => fake()->randomElement(['offered', 'wanted']),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
