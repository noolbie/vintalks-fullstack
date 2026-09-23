<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\MentorProfile;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        Setting::set('commission_rate', 15);
    }

    // Helper: buat mentor (kembalikan User + profil mentor-nya).
    private function makeMentorUser(): array
    {
        $profile = $this->makeMentor();
        /** @var MentorProfile $profile */
        return [$profile->user, $profile];
    }

    private function makeCompletedBooking(User $mentor, User $participant, float $price = 150000): Booking
    {
        return Booking::create([
            'booking_code' => Booking::generateBookingCode(),
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->id,
            'session_date' => now()->subDays(5)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'price' => $price,
            'booking_status' => BookingStatus::Completed->value,
            'payment_status' => PaymentStatus::Verified->value,
            'confirmed_at' => now()->subDays(7),
            'completed_at' => now()->subDay(),
        ]);
    }

    public function test_commission_formula_is_15_percent_of_price(): void
    {
        [$mentor] = $this->makeMentorUser();
        $booking = $this->makeCompletedBooking($mentor, $this->makeParticipant());

        // 150.000 x 15% = 22.500 potongan, mentor menerima 127.500.
        $this->assertSame(15.0, $booking->commission_rate);
        $this->assertSame(22500.0, $booking->commission_amount);
        $this->assertSame(127500.0, $booking->mentor_net);
        $this->assertFalse($booking->isPaidToMentor());
    }

    public function test_admin_can_mark_completed_booking_as_paid(): void
    {
        [$mentor] = $this->makeMentorUser();
        $booking = $this->makeCompletedBooking($mentor, $this->makeParticipant());
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.transactions.mark-paid', $booking))
            ->assertRedirect();

        $booking->refresh();
        $this->assertTrue($booking->isPaidToMentor());
        $this->assertNotNull($booking->mentor_paid_at);
    }

    public function test_admin_transactions_page_lists_completed_sessions_with_commission(): void
    {
        [$mentor] = $this->makeMentorUser();
        $booking = $this->makeCompletedBooking($mentor, $this->makeParticipant());
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.transactions.index'));

        $response->assertOk();
        $response->assertSee($booking->booking_code);
        $response->assertSee('Tandai Dibayar');
        // Untuk mentor = 150.000 - (15% = 22.500) = 127.500.
        $response->assertSee('127.500');
    }

    public function test_non_admin_cannot_mark_booking_as_paid(): void
    {
        [$mentor] = $this->makeMentorUser();
        $booking = $this->makeCompletedBooking($mentor, $this->makeParticipant());
        $participant = $this->makeParticipant();

        $this->actingAs($participant)
            ->post(route('admin.transactions.mark-paid', $booking))
            ->assertForbidden();

        $booking->refresh();
        $this->assertNull($booking->mentor_paid_at);
    }

    public function test_mentor_earnings_page_shows_totals_and_transaction_detail(): void
    {
        [$mentor] = $this->makeMentorUser();
        $participant = $this->makeParticipant();
        $this->makeCompletedBooking($mentor, $participant, 100000);

        // Admin tandai satu sesi sebagai dibayar.
        $paid = $this->makeCompletedBooking($mentor, $participant, 200000);
        $this->actingAs($this->makeAdmin())
            ->post(route('admin.transactions.mark-paid', $paid));

        $response = $this->actingAs($mentor)->get(route('mentor.earnings.index'));

        $response->assertOk();
        // Net sesi harga 200.000 (15% = 30.000) -> 170.000 sudah dibayar.
        $response->assertSee('170.000');
        $this->assertSame(2, $mentor->mentorBookings()->where('booking_status', BookingStatus::Completed->value)->count());
    }
}