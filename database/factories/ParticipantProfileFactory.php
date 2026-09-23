<?php

namespace Database\Factories;

use App\Models\ParticipantProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParticipantProfile>
 */
class ParticipantProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => fake()->numerify('08##########'),
            'gender' => fake()->randomElement(['male', 'female']),
            'occupation' => fake()->jobTitle(),
            'institution' => fake()->company(),
            'linkedin_url' => fake()->url(),
        ];
    }
}