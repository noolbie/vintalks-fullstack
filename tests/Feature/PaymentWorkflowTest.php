<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\MentorAvailability;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\CreatesRoles;
use Tests\TestCase;

class PaymentWorkflowTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        Setting::set('payment_bank_name', 'BCA');
        Setting::set('payment_account_name', 'VinTalks');
        Setting::set('payment_account_number', '081329393939');
        Setting::set('payment_methods', ['qris', 'transfer_bank', 'digital_wallet']);
        Storage::fake('private');
    }

    private function makePendingBooking(User $participant): Booking
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

        return Booking::create([
            'booking_code' => Booking::generateBookingCode(),
            'participant_id' => $participant->id,
            'mentor_id' => $mentor->user_id,
            'mentor_availability_id' => $slot->id,
            'session_date' => $date,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'price' => $mentor->price,
            'booking_status' => BookingStatus::PaymentPending->value,
            'payment_status' => PaymentStatus::Unpaid->value,
        ]);
    }

    public function test_participant_can_submit_payment_proof(): void
    {
        $participant = $this->makeParticipant();
        $booking = $this->makePendingBooking($participant);

        $response = $this->actingAs($participant)
            ->post(route('participant.payments.store', $booking), [
                'proof' => UploadedFile::fake()->image('bukti.jpg', 100, 100),
                'payment_method' => 'transfer_bank',
                'payment_reference' => 'TRF-12345',
            ]);

        $response->assertRedirect(route('participant.bookings.show', $booking));

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => 'payment_verification',
            'payment_status' => 'waiting_verification',
        ]);

        $payment = $booking->payment()->first();
        $this->assertNotNull($payment);
        $this->assertSame('waiting_verification', $payment->status);
        $this->assertNotNull($payment->proof_document_id);
        Storage::disk('private')->assertExists($payment->proofDocument->file_path);
    }

    public function test_another_participant_cannot_pay_for_someone_elses_booking(): void
    {
        $owner = $this->makeParticipant();
        $booking = $this->makePendingBooking($owner);
        $intruder = $this->makeParticipant();

        $response = $this->actingAs($intruder)
            ->post(route('participant.payments.store', $booking), [
                'proof' => UploadedFile::fake()->image('bukti.jpg', 100, 100),
                'payment_method' => 'qris',
            ]);

        $response->assertForbidden();
        $this->assertNull($booking->payment()->first());
    }

    public function test_admin_can_verify_payment_and_booking_is_confirmed(): void
    {
        $participant = $this->makeParticipant();
        $booking = $this->makePendingBooking($participant);

        $this->actingAs($participant)
            ->post(route('participant.payments.store', $booking), [
                'proof' => UploadedFile::fake()->image('bukti.jpg', 100, 100),
                'payment_method' => 'transfer_bank',
            ])
            ->assertRedirect();

        $payment = $booking->payment()->first();
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.payments.verify', $payment));

        $response->assertRedirect();

        $booking->refresh();
        $payment->refresh();
        $this->assertSame(BookingStatus::Confirmed->value, $booking->booking_status);
        $this->assertSame(PaymentStatus::Verified->value, $payment->status);
        $this->assertSame($admin->id, $payment->verified_by);
        $this->assertNotNull($booking->confirmed_at);
    }

    public function test_admin_can_open_payment_detail_in_waiting_verification(): void
    {
        $participant = $this->makeParticipant();
        $booking = $this->makePendingBooking($participant);

        $this->actingAs($participant)
            ->post(route('participant.payments.store', $booking), [
                'proof' => UploadedFile::fake()->image('bukti.jpg', 100, 100),
                'payment_method' => 'transfer_bank',
            ])
            ->assertRedirect();

        $payment = $booking->payment()->first();
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->get(route('admin.payments.show', $payment));

        $response->assertOk();
        $response->assertSee('Pembayaran');
    }

    public function test_admin_can_reject_payment_and_participant_can_resubmit(): void
    {
        $participant = $this->makeParticipant();
        $booking = $this->makePendingBooking($participant);

        $this->actingAs($participant)
            ->post(route('participant.payments.store', $booking), [
                'proof' => UploadedFile::fake()->image('bukti.jpg', 100, 100),
                'payment_method' => 'qris',
            ])
            ->assertRedirect();

        $payment = $booking->payment()->first();
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.payments.reject', $payment), ['rejection_reason' => 'Bukti tidak terbaca.'])
            ->assertRedirect();

        $booking->refresh();
        $payment->refresh();
        $this->assertSame(PaymentStatus::Rejected->value, $payment->status);
        $this->assertSame(BookingStatus::Rejected->value, $booking->booking_status);
        $this->assertSame('Bukti tidak terbaca.', $payment->rejection_reason);

        // participant can upload a new proof afterwards
        $response = $this->actingAs($participant)
            ->post(route('participant.payments.store', $booking), [
                'proof' => UploadedFile::fake()->image('bukti-baru.jpg', 100, 100),
                'payment_method' => 'qris',
            ]);

        $response->assertRedirect(route('participant.bookings.show', $booking));
        $booking->refresh();
        $this->assertSame(BookingStatus::PaymentVerification->value, $booking->booking_status);
    }
}