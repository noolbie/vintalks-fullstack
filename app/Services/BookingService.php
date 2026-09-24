<?php

namespace App\Services;

use App\Enums\BookingStatus; // Enum status sesi booking (pending, payment_pending, confirmed, completed, dll).
use App\Enums\PackageApprovalStatus; // Enum status persetujuan paket potongan.
use App\Enums\PaymentStatus; // Enum status pembayaran (unpaid, waiting_verification, verified, rejected, dll).
use App\Models\Booking;
use App\Models\MentorProfile;
use App\Models\Package;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class BookingService
{
    // Constructor: menyuntikkan dependensi untuk pembuatan kode booking unik & pengecekan slot.
    public function __construct(
        private readonly BookingCodeService $bookingCodeService,
        private readonly AvailabilityService $availabilityService,
    ) {}

    // Buat booking baru secara aman (transaksi DB + row lock) agar tidak terjadi double-booking.
    /**
     * Create a booking securely (transaction + row lock) to prevent double booking.
     *
     * @throws ValidationException
     */
    public function create(MentorProfile $mentorProfile, User $participant, array $data): Booking
    {
        if (! $mentorProfile->is_active) {
            throw ValidationException::withMessages([
                'mentor' => 'Mentor tidak tersedia saat ini.',
            ]);
        }

        $sessionDate = data_get($data, 'session_date');
        $startTime = data_get($data, 'start_time');
        $endTime = data_get($data, 'end_time');

        // Paket potongan: nominal disimpan = selisih harga normal vs promo; harga sesi = harga mentor - potongan.
        $package = null;
        $discount = 0.0;
        $packageId = data_get($data, 'package_id');
        if ($packageId) {
            $package = Package::find($packageId);
            if ($package) {
                // Satu peserta hanya boleh memakai 1 paket; paket yang DITOLAK admin tetap boleh mencoba paket lain.
                $alreadyUsed = $participant->participantBookings()
                    ->whereNotNull('package_id')
                    ->whereIn('package_approval_status', [
                        PackageApprovalStatus::Pending->value,
                        PackageApprovalStatus::Approved->value,
                    ])
                    ->exists();

                if ($alreadyUsed) {
                    throw ValidationException::withMessages([
                        'package' => "Kamu sudah menggunakan paket layanan; booking berikutnya memakai harga standar.",
                    ]);
                }

                $discount = (float) $package->discount;
            }
        }

        $basePrice = data_get($data, 'price', $mentorProfile->price);
        $finalPrice = max(0, (float) $basePrice - $discount);

        return \DB::transaction(function () use ($mentorProfile, $participant, $sessionDate, $startTime, $endTime, $data, $package, $discount, $finalPrice) {
            $slot = $this->availabilityService->findBookableSlot($mentorProfile, $sessionDate, $startTime, $endTime);

            // Lock the availability row to serialize concurrent bookings on the same slot.
            $slotLocked = $mentorProfile->availabilities()
                ->whereKey($slot->id)
                ->lockForUpdate()
                ->first();

            if (! $slotLocked || $slotLocked->status !== 'available') {
                throw ValidationException::withMessages([
                    'slot' => 'Jadwal yang Anda pilih sudah tidak tersedia. Silakan pilih jadwal lain.',
                ]);
            }

            $conflict = $this->availabilityService->hasActiveBookingOnSlot(
                $mentorProfile,
                $sessionDate,
                $startTime,
                $endTime,
                $slot->id,
            );

            if ($conflict) {
                throw ValidationException::withMessages([
                    'slot' => 'Jadwal yang Anda pilih sudah tidak tersedia. Silakan pilih jadwal lain.',
                ]);
            }

            $booking = Booking::create([
                'booking_code' => $this->bookingCodeService->generateUnique(),
                'participant_id' => $participant->id,
                'mentor_id' => $mentorProfile->user_id,
                'mentor_availability_id' => $slot->id,
                'topic_id' => data_get($data, 'topic_id'),
                'package_id' => $package?->id,
                'discount_amount' => $discount > 0 ? $discount : null,
                'package_approval_status' => $package
                    ? PackageApprovalStatus::Pending->value
                    : PackageApprovalStatus::None->value,
                'session_date' => $sessionDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price' => $finalPrice,
                'booking_status' => BookingStatus::PaymentPending->value,
                'payment_status' => PaymentStatus::Unpaid->value,
            ]);

            return $booking->fresh();
        });
    }

    // Setelah bukti bayar dikirim: ubah booking jadi PaymentVerification / pembayaran menunggu verifikasi.
    public function submitPaymentProof(Booking $booking): void
    {
        $this->transition($booking, BookingStatus::PaymentVerification);
        $booking->forceFill(['payment_status' => PaymentStatus::WaitingVerification->value])->save();
    }

    // Konfirmasi booking setelah pembayaran terverifikasi dan catat waktu konfirmasi.
    public function confirm(Booking $booking): void
    {
        $this->transition($booking, BookingStatus::Confirmed);
        $booking->forceFill([
            'payment_status' => PaymentStatus::Verified->value,
            'confirmed_at' => now(),
        ])->save();
    }

    // Tandai booking selesai (Completed) dan catat waktu penyelesaian sesi.
    public function complete(Booking $booking): void
    {
        $this->transition($booking, BookingStatus::Completed);
        $booking->forceFill(['completed_at' => now()])->save();
    }

    // Batalkan booking dan catat waktu + alasan pembatalan.
    public function cancel(Booking $booking, ?string $reason = null): void
    {
        $this->transition($booking, BookingStatus::Cancelled);
        $booking->forceFill([
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ])->save();
    }

    // Tolak booking (mis. pembayaran ditolak) dan tandai status pembayaran sebagai rejected.
    public function reject(Booking $booking): void
    {
        $this->transition($booking, BookingStatus::Rejected);
        $booking->forceFill([
            'payment_status' => PaymentStatus::Rejected->value,
        ])->save();
    }

    // ==== Helper internal ====

    // Validasi bahwa transisi status booking yang diminta legal, lalu simpan status baru.
    /**
     * Validate a legal status transition.
     */
    private function transition(Booking $booking, BookingStatus $target): void
    {
        $current = BookingStatus::from($booking->booking_status);

        $allowed = match ($target) {
            BookingStatus::PaymentVerification => [
                BookingStatus::PaymentPending,
                BookingStatus::Rejected,
            ],
            BookingStatus::Confirmed => [
                BookingStatus::PaymentVerification,
                BookingStatus::Pending,
            ],
            BookingStatus::Completed => [
                BookingStatus::Confirmed,
            ],
            BookingStatus::Cancelled => [
                BookingStatus::PaymentPending,
                BookingStatus::PaymentVerification,
                BookingStatus::Pending,
                BookingStatus::Confirmed,
            ],
            BookingStatus::Rejected => [
                BookingStatus::PaymentPending,
                BookingStatus::PaymentVerification,
            ],
            BookingStatus::Pending => [BookingStatus::PaymentPending],
            BookingStatus::PaymentPending => [BookingStatus::Rejected],
        };

        if (! in_array($current, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => "Status booking tidak dapat diubah dari '{$current->label()}' menjadi '{$target->label()}'.",
            ]);
        }

        $booking->forceFill(['booking_status' => $target->value])->save();
    }
}