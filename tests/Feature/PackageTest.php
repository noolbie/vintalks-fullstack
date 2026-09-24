<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\MentorAvailability;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesRoles;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use CreatesRoles;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    // Helper: paket starter dengan potongan Rp20.000 (normal 50.000 -> promo 30.000).
    private function makePackage(array $attributes = []): Package
    {
        return Package::create(array_merge([
            'slug' => 'starter',
            'name' => 'Starter Package',
            'description' => 'Persiapan dasar karier internasional.',
            'old_price' => 50000,
            'price' => 30000,
            'benefits' => ['Konsultasi privat 1-on-1 (45 menit).', 'Skill Assessment awal.'],
            'is_popular' => false,
            'is_active' => true,
        ], $attributes));
    }

    // Helper: mentor aktif + slot tersedia besok jam 10-11.
    private function makeMentorWithSlot(): array
    {
        $profile = $this->makeMentor();
        $profile->update(['price' => 150000]);

        $slot = MentorAvailability::create([
            'mentor_id' => $profile->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'status' => 'available',
        ]);

        return [$profile->user, $profile, $slot];
    }

    // Helper: buat booking lewat form peserta dengan paket tertentu.
    private function createBookingWithPackage(Package $package): Booking
    {
        [, $profile, $slot] = $this->makeMentorWithSlot();
        $participant = $this->makeParticipant();

        $this->actingAs($participant)->post(route('participant.mentors.bookings.store', $profile), [
            'slot_id' => $slot->id,
            'package_id' => $package->id,
            'session_date' => $slot->date->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ])->assertRedirect();

        return Booking::where('participant_id', $participant->id)->firstOrFail();
    }

    public function test_landing_page_shows_packages_from_database(): void
    {
        $package = $this->makePackage();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee($package->name);
        $response->assertSee('Rp50.000');
        $response->assertSee('Rp30.000');
        $response->assertSee('Konsultasi privat 1-on-1 (45 menit).');
        $response->assertSee(route('packages.select', $package));
    }

    public function test_select_package_stores_choice_in_session(): void
    {
        $package = $this->makePackage();

        // Tamu diarahkan ke register tapi pilihan tersimpan di session.
        $this->get(route('packages.select', $package))
            ->assertRedirect(route('register'));
        $this->assertEquals($package->slug, session('selected_package'));

        // Peserta yang login diarahkan ke daftar mentor.
        $participant = $this->makeParticipant();
        $this->actingAs($participant)->get(route('packages.select', $package))
            ->assertRedirect(route('participant.mentors.index'));
        $this->assertEquals($package->slug, session('selected_package'));
    }

    public function test_booking_with_package_applies_discount_and_pending_approval(): void
    {
        $package = $this->makePackage();

        $booking = $this->createBookingWithPackage($package);

        // Harga mentor 150.000 - potongan 20.000 = 130.000, status menunggu persetujuan.
        $this->assertTrue($booking->hasPackage());
        $this->assertTrue($booking->isPackagePending());
        $this->assertSame(20000.0, (float) $booking->discount_amount);
        $this->assertSame(130000.0, (float) $booking->price);
        $this->assertSame(150000.0, $booking->package_base_price);
    }

    public function test_booking_without_package_has_no_discount(): void
    {
        $package = $this->makePackage();

        [, $profile, $slot] = $this->makeMentorWithSlot();
        $participant = $this->makeParticipant();

        $this->actingAs($participant)->post(route('participant.mentors.bookings.store', $profile), [
            'slot_id' => $slot->id,
            'session_date' => $slot->date->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ])->assertRedirect();

        $booking = Booking::where('participant_id', $participant->id)->firstOrFail();

        $this->assertFalse($booking->hasPackage());
        $this->assertSame(150000.0, (float) $booking->price);
        $this->assertNull($booking->discount_amount);
    }

    public function test_admin_can_approve_package(): void
    {
        $package = $this->makePackage();
        $booking = $this->createBookingWithPackage($package);
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.package-approvals.approve', $booking))
            ->assertRedirect();

        $booking->refresh();
        $this->assertTrue($booking->isPackageApproved());
        $this->assertNotNull($booking->package_approved_at);
        $this->assertSame(130000.0, (float) $booking->price);
    }

    public function test_admin_can_reject_package_and_price_reverts(): void
    {
        $package = $this->makePackage();
        $booking = $this->createBookingWithPackage($package);
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.package-approvals.reject', $booking), [
            'reason' => 'Kelas tidak tersedia minggu ini.',
        ])->assertRedirect();

        $booking->refresh();
        $this->assertTrue($booking->isPackageRejected());
        $this->assertSame('Kelas tidak tersedia minggu ini.', $booking->package_rejection_reason);
        $this->assertNotNull($booking->package_rejected_at);
        $this->assertSame(150000.0, (float) $booking->price);
        $this->assertNull($booking->discount_amount);
    }

    public function test_admin_approval_index_lists_pending_packages(): void
    {
        $package = $this->makePackage();
        $booking = $this->createBookingWithPackage($package);
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.package-approvals.index'));

        $response->assertOk();
        $response->assertSee($booking->booking_code);
        $response->assertSee($package->name);
        $response->assertSee('Setujui Paket');
    }

    public function test_mentor_sees_package_and_benefits_on_booking_detail(): void
    {
        $package = $this->makePackage();
        $booking = $this->createBookingWithPackage($package);
        $mentorUser = $booking->mentor;

        // Setelah disetujui admin, mentor lihat nominal & benefit.
        $this->actingAs($this->makeAdmin())->post(route('admin.package-approvals.approve', $booking));

        $response = $this->actingAs($mentorUser)->get(route('mentor.bookings.show', $booking));

        $response->assertOk();
        $response->assertSee($package->name);
        $response->assertSee('Konsultasi privat 1-on-1 (45 menit).');
        $response->assertSee('Rp20.000');
        $response->assertSee('Disetujui');
    }

    public function test_admin_can_edit_package_prices_and_benefits(): void
    {
        $package = $this->makePackage();
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->put(route('admin.packages.update', $package), [
            'slug' => $package->slug,
            'name' => 'Starter Upgrade',
            'description' => 'Deskripsi baru.',
            'old_price' => 100000,
            'price' => 70000,
            'benefits' => ['Benefit baru 1', 'Benefit baru 2'],
            'is_popular' => 1,
            'is_active' => 1,
        ])->assertRedirect();

        $package->refresh();
        $this->assertSame('Starter Upgrade', $package->name);
        $this->assertSame('70000.00', (string) $package->price);
        $this->assertSame(['Benefit baru 1', 'Benefit baru 2'], $package->benefits);
        $this->assertTrue($package->is_popular);
    }

    public function test_participant_cannot_pick_another_package_later(): void
    {
        $pkg1 = $this->makePackage(['slug' => 'starter', 'name' => 'Starter Package']);
        $pkg2 = $this->makePackage(['slug' => 'premium', 'name' => 'Premium Package', 'old_price' => 350000, 'price' => 300000]);

        $booking = $this->createBookingWithPackage($pkg1);
        $participant = $booking->participant;

        // Booking kedua mencoba memakai paket lain -> ditolak dengan pesan.
        [, $profile, $slot] = $this->makeMentorWithSlot();
        $this->actingAs($participant)->post(route('participant.mentors.bookings.store', $profile), [
            'slot_id' => $slot->id,
            'package_id' => $pkg2->id,
            'session_date' => $slot->date->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ])->assertSessionHasErrors('package');

        // Tidak ada booking baru yang terbentuk.
        $this->assertSame(1, Booking::where('participant_id', $participant->id)->count());
        $this->assertSame(1, Booking::where('participant_id', $participant->id)->whereNotNull('package_id')->count());
    }

    public function test_booking_form_notifies_participant_who_already_used_a_package(): void
    {
        $pkg1 = $this->makePackage(['slug' => 'starter', 'name' => 'Starter Package']);
        $booking = $this->createBookingWithPackage($pkg1);
        $participant = $booking->participant;

        $response = $this->actingAs($participant)->get(route('participant.bookings.create', $booking->mentorProfile));

        $response->assertOk();
        $response->assertSee('sudah menggunakan paket');
        $response->assertSee('Starter Package');
        $response->assertDontSee('name="package_id"');
    }

    public function test_participant_can_try_another_package_after_one_was_rejected(): void
    {
        $pkg1 = $this->makePackage(['slug' => 'starter', 'name' => 'Starter Package']);
        $pkg2 = $this->makePackage(['slug' => 'premium', 'name' => 'Premium Package', 'old_price' => 350000, 'price' => 300000]);

        $booking = $this->createBookingWithPackage($pkg1);
        $participant = $booking->participant;

        // Admin menolak paket pertama.
        $this->actingAs($this->makeAdmin())->post(route('admin.package-approvals.reject', $booking), [
            'reason' => 'Kelas penuh.',
        ])->assertRedirect();

        // Booking berikutnya dengan paket lain boleh dipakai kembali.
        [, $profile, $slot] = $this->makeMentorWithSlot();
        $this->actingAs($participant)->post(route('participant.mentors.bookings.store', $profile), [
            'slot_id' => $slot->id,
            'package_id' => $pkg2->id,
            'session_date' => $slot->date->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
        ])->assertRedirect();

        $second = Booking::where('participant_id', $participant->id)->latest('id')->first();
        $this->assertSame(2, Booking::where('participant_id', $participant->id)->count());
        $this->assertTrue($second->hasPackage());
        $this->assertTrue($second->isPackagePending());
        $this->assertSame((float) $pkg2->discount, (float) $second->discount_amount);
        $this->assertSame(100000.0, (float) $second->price);
    }
}