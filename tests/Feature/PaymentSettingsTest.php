<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class PaymentSettingsTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_default_payment_settings_are_seeded(): void
    {
        $this->seedDatabaseDefaults();

        $this->assertSame('BCA', Setting::get('payment_bank_name'));
        $this->assertSame('VinTalks', Setting::get('payment_account_name'));
        $this->assertSame('081329393939', Setting::get('payment_account_number'));
        $this->assertSame(['qris', 'transfer_bank', 'digital_wallet'], Setting::paymentMethodValues());
    }

    public function test_admin_can_update_payment_settings(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)
            ->put(route('admin.settings.update'), [
                'payment_bank_name' => 'Mandiri',
                'payment_account_name' => 'PT VinTalks Indonesia',
                'payment_account_number' => '1234567890',
                'payment_methods' => ['qris', 'transfer_bank'],
                'commission_rate' => 20,
            ]);

        $response->assertRedirect();
        $this->assertSame('Mandiri', Setting::get('payment_bank_name'));
        $this->assertSame('PT VinTalks Indonesia', Setting::get('payment_account_name'));
        $this->assertSame('1234567890', Setting::get('payment_account_number'));
        $this->assertSame(['qris', 'transfer_bank'], Setting::paymentMethodValues());
        $this->assertSame('20', Setting::get('commission_rate'));
    }

    public function test_non_admin_cannot_update_payment_settings(): void
    {
        $participant = $this->makeParticipant();

        $this->actingAs($participant)
            ->put(route('admin.settings.update'), [
                'payment_bank_name' => 'BCA',
                'payment_account_name' => 'VinTalks',
                'payment_account_number' => '081329393939',
                'payment_methods' => ['qris'],
            ])
            ->assertForbidden();
    }

    public function test_participant_payment_page_shows_account_number_and_methods(): void
    {
        $this->seedDatabaseDefaults();

        $participant = $this->makeParticipant();
        $booking = $this->bookingInPaymentPending($participant);

        $response = $this->actingAs($participant)
            ->get(route('participant.payments.create', $booking));

        $response->assertOk();
        $response->assertSee('Transfer ke rekening VinTalks');
        $response->assertSee('081329393939');
        $response->assertSee('QRIS');
        $response->assertSee('Transfer Bank');
        $response->assertSee('Dompet Digital');
        $response->assertSee('name="payment_method"', false);
    }

    public function test_payment_method_must_be_an_enabled_method(): void
    {
        $this->seedDatabaseDefaults();
        Setting::set('payment_methods', ['qris']);

        $participant = $this->makeParticipant();
        $booking = $this->bookingInPaymentPending($participant);

        $response = $this->actingAs($participant)
            ->post(route('participant.payments.store', $booking), [
                'proof' => \Illuminate\Http\UploadedFile::fake()->image('bukti.jpg', 100, 100),
                'payment_method' => 'transfer_bank',
            ]);

        $response->assertSessionHasErrors('payment_method');
        $this->assertNull($booking->payment()->first());
    }

    public function test_disabled_method_is_not_shown_on_payment_page(): void
    {
        $this->seedDatabaseDefaults();
        Setting::set('payment_methods', ['qris']);

        $participant = $this->makeParticipant();
        $booking = $this->bookingInPaymentPending($participant);

        $response = $this->actingAs($participant)
            ->get(route('participant.payments.create', $booking));

        $response->assertOk();
        $response->assertDontSee('Transfer Bank');
        $response->assertDontSee('Dompet Digital');
    }

    private function seedDatabaseDefaults(): void
    {
        Setting::set('payment_bank_name', 'BCA');
        Setting::set('payment_account_name', 'VinTalks');
        Setting::set('payment_account_number', '081329393939');
        Setting::set('payment_methods', ['qris', 'transfer_bank', 'digital_wallet']);
    }

    private function bookingInPaymentPending(User $participant): Booking
    {
        $mentor = $this->makeMentor();
        $date = now()->addDays(2)->format('Y-m-d');
        $slot = \App\Models\MentorAvailability::create([
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
            'booking_status' => \App\Enums\BookingStatus::PaymentPending->value,
            'payment_status' => \App\Enums\PaymentStatus::Unpaid->value,
        ]);
    }
}