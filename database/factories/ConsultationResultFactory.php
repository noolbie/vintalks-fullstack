<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\ConsultationResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConsultationResult>
 */
class ConsultationResultFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'mentor_id' => 1,
            'participant_id' => 1,
            'summary' => fake()->paragraphs(2, true),
            'mentor_notes' => fake()->optional()->paragraph(),
        ];
    }
}