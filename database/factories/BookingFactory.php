<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\MentorAvailability;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        $availability = MentorAvailability::factory()->create();

        return [
            'booking_code' => \App\Models\Booking::generateBookingCode(),
            'participant_id' => User::factory(),
            'mentor_id' => $availability->mentor->user_id,
            'mentor_availability_id' => $availability->id,
            'topic_id' => Topic::factory(),
            'session_date' => $availability->date,
            'start_time' => $availability->start_time,
            'end_time' => $availability->end_time,
            'price' => $availability->mentor->price,
            'booking_status' => BookingStatus::PaymentPending->value,
            'payment_status' => PaymentStatus::Unpaid->value,
        ];
    }

    public function confirmed(): static
    {
        return $this->state([
            'booking_status' => BookingStatus::Confirmed->value,
            'payment_status' => PaymentStatus::Verified->value,
            'meeting_provider' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'confirmed_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state([
            'booking_status' => BookingStatus::Completed->value,
            'payment_status' => PaymentStatus::Verified->value,
            'meeting_provider' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/abc-defg-hij',
            'confirmed_at' => now()->subDays(2),
            'completed_at' => now()->subDay(),
        ]);
    }
}