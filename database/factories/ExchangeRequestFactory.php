<?php

namespace Database\Factories;

use App\Models\ExchangeRequest;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ExchangeRequest> */
class ExchangeRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'sender_user_skill_id' => UserSkill::factory(),
            'receiver_user_skill_id' => UserSkill::factory(),
            'learning_format' => fake()->randomElement(['online', 'onsite', 'either']),
            'preferred_schedule' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'status' => 'pending',
            'responded_at' => null,
            'completed_at' => null,
        ];
    }
}
