<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\ConsultationRequirement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConsultationRequirement>
 */
class ConsultationRequirementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'linkedin_url' => fake()->url(),
            'career_goal' => fake()->paragraph(),
            'consultation_topic' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'additional_notes' => fake()->optional()->sentence(),
        ];
    }
}