<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\MentorAvailability;
use App\Models\MentorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    private function bookableSlot(): array
    {
        $mentor = $this->makeMentor();
        $date = now()->addDays(2)->format('Y-m-d');
        $slot = MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'status' => 'available',
        ]);

        return [$mentor, $slot, $date];
    }

    private function bookingPayload(MentorAvailability $slot, string $date): array
    {
        return [
            'slot_id' => $slot->id,
            'session_date' => $date,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'topic_id' => null,
            'career_goal' => 'Mendapatkan pekerjaan remote.',
            'consultation_topic' => 'Review portofolio',
        ];
    }

    public function test_participant_can_book_an_available_slot(): void
    {
        [$mentor, $slot, $date] = $this->bookableSlot();
        $participant = $this->makeParticipant();

        $response = $this->actingAs($participant)
            ->post(route('participant.mentors.bookings.store', $mentor), $this->bookingPayload($slot, $date));

        $response->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->user_id,
            'mentor_availability_id' => $slot->id,
            'booking_status' => 'payment_pending',
            'payment_status' => 'unpaid',
        ]);
    }

    public function test_booking_matches_slot_even_with_mixed_time_format(): void
    {
        $mentor = $this->makeMentor();
        $date = now()->addDays(2)->format('Y-m-d');
        $slot = MentorAvailability::create([
            'mentor_id' => $mentor->id,
            'date' => $date,
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'status' => 'available',
        ]);
        $participant = $this->makeParticipant();

        $payload = $this->bookingPayload($slot, $date);
        $payload['start_time'] = '10:00';
        $payload['end_time'] = '11:00';

        $response = $this->actingAs($participant)
            ->post(route('participant.mentors.bookings.store', $mentor), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'participant_id' => $participant->id,
            'mentor_availability_id' => $slot->id,
        ]);
    }

    public function test_slots_endpoint_returns_plain_date_and_hour_minute(): void
    {
        [$mentor, $slot, $date] = $this->bookableSlot();

        $response = $this->actingAs($this->makeParticipant())
            ->getJson(route('participant.mentors.slots', ['mentor' => $mentor, 'date' => $date]));

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.0.id', $slot->id);
        $response->assertJsonPath('data.0.date', $date);
        $response->assertJsonPath('data.0.start_time', '10:00');
        $response->assertJsonPath('data.0.end_time', '11:00');
        $response->assertJsonPath('data.0.available', true);
    }

    public function test_slot_cannot_be_booked_twice(): void
    {
        [$mentor, $slot, $date] = $this->bookableSlot();
        $participant = $this->makeParticipant();

        $this->actingAs($participant)
            ->post(route('participant.mentors.bookings.store', $mentor), $this->bookingPayload($slot, $date))
            ->assertRedirect();

        $participant2 = $this->makeParticipant();
        $response = $this->actingAs($participant2)
            ->post(route('participant.mentors.bookings.store', $mentor), $this->bookingPayload($slot, $date));

        $response->assertSessionHasErrors('slot');
        $this->assertSame(1, Booking::where('mentor_availability_id', $slot->id)->count());
    }

    public function test_mentor_cannot_create_a_booking(): void
    {
        [$mentor, $slot, $date] = $this->bookableSlot();
        $otherMentor = $this->makeMentor();

        $response = $this->actingAs($otherMentor->user)
            ->post(route('participant.mentors.bookings.store', $mentor), $this->bookingPayload($slot, $date));

        $response->assertForbidden();
    }

    public function test_participant_cannot_view_another_participants_booking(): void
    {
        [$mentor, $slot, $date] = $this->bookableSlot();
        $owner = $this->makeParticipant();

        $booking = Booking::create([
            'booking_code' => Booking::generateBookingCode(),
            'participant_id' => $owner->id,
            'mentor_id' => $mentor->user_id,
            'mentor_availability_id' => $slot->id,
            'session_date' => $date,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'price' => $mentor->price,
            'booking_status' => 'payment_pending',
            'payment_status' => 'unpaid',
        ]);

        $intruder = $this->makeParticipant();
        $response = $this->actingAs($intruder)->get(route('participant.bookings.show', $booking));

        $response->assertForbidden();
    }
}