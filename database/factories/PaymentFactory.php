<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Document;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'amount' => 100000,
            'payment_method' => fake()->randomElement(['qris', 'transfer_bank', 'digital_wallet']),
            'payment_reference' => fake()->optional()->bothify('??????'),
            'submitted_at' => now(),
            'status' => 'pending',
        ];
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => 'rejected',
            'verified_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    public function verified(): static
    {
        return $this->state([
            'status' => 'verified',
            'verified_at' => now(),
        ]);
    }
}