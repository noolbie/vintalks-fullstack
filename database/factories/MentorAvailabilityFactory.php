<?php

namespace Database\Factories;

use App\Models\MentorAvailability;
use App\Models\MentorProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MentorAvailability>
 */
class MentorAvailabilityFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->numberBetween(8, 16);

        return [
            'mentor_id' => MentorProfile::factory(),
            'date' => fake()->dateTimeBetween('today', '+30 days')->format('Y-m-d'),
            'start_time' => sprintf('%02d:00:00', $start),
            'end_time' => sprintf('%02d:00:00', $start + 1),
            'status' => 'available',
        ];
    }
}