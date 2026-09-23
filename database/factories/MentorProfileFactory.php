<?php

namespace Database\Factories;

use App\Models\MentorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MentorProfile>
 */
class MentorProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'display_name' => fake()->name(),
            'expertise' => fake()->jobTitle(),
            'photo' => null,
            'bio' => fake()->paragraph(2),
            'experience' => fake()->sentence(),
            'price' => fake()->numberBetween(65000, 350000),
            'is_active' => true,
        ];
    }
}