<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_participant_pages_render(): void
    {
        $user = User::where('email', 'participant@vintalks.net')->firstOrFail();

        $this->actingAs($user)->get(route('participant.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('participant.mentors.index'))->assertOk();
        $this->actingAs($user)->get(route('participant.bookings.history'))->assertOk();
        $this->actingAs($user)->get(route('participant.notifications'))->assertOk();
        $this->actingAs($user)->get(route('participant.profile.edit'))->assertOk();

        $booking = $user->participantBookings()->firstOrFail();
        $this->actingAs($user)->get(route('participant.bookings.show', $booking))->assertOk();

        $mentor = \App\Models\MentorProfile::where('is_active', true)->firstOrFail();
        $this->actingAs($user)->get(route('participant.mentors.show', $mentor))->assertOk();
        if ($mentor->availabilities()->exists()) {
            $this->actingAs($user)->get(route('participant.mentors.slots', ['mentor' => $mentor, 'date' => now()->addDays(2)->format('Y-m-d')]))->assertJson(['success' => true]);
            $this->actingAs($user)->get(route('participant.bookings.create', $mentor))->assertOk();
        }

        $unverifiedBooking = $user->participantBookings()->where('payment_status', '!=', 'verified')->first();
        if ($unverifiedBooking) {
            $this->actingAs($user)->get(route('participant.payments.create', $unverifiedBooking))->assertOk();
        }
    }

    public function test_mentor_pages_render(): void
    {
        $user = User::where('email', 'diyanarif@vintalks.net')->firstOrFail();

        $this->actingAs($user)->get(route('mentor.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('mentor.profile.edit'))->assertOk();
        $this->actingAs($user)->get(route('mentor.availability.index'))->assertOk();
        $this->actingAs($user)->get(route('mentor.bookings.index'))->assertOk();
        $this->actingAs($user)->get(route('mentor.earnings.index'))->assertOk();
    }

    public function test_admin_pages_render(): void
    {
        $user = User::where('email', 'admin@vintalks.net')->firstOrFail();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.participants.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.mentors.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.topics.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.bookings.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.payments.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.transactions.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.consultation-results.index'))->assertOk();

        $booking = \App\Models\Booking::first();
        if ($booking) {
            $this->actingAs($user)->get(route('admin.bookings.show', $booking))->assertOk();
        }
        $payment = \App\Models\Payment::first();
        if ($payment) {
            $this->actingAs($user)->get(route('admin.payments.show', $payment))->assertOk();
        }
    }
}