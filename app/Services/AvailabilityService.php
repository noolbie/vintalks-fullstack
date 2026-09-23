<?php

namespace App\Services;

use App\Enums\BookingStatus; // Enum status sesi booking (dipakai untuk menentukan slot aktif/ter-book).
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\MentorAvailability;
use App\Models\MentorProfile;
use Illuminate\Support\Carbon;

class AvailabilityService
{
    // ==== Slot ketersediaan ====
    // Slot pada suatu tanggal dihitung dari jadwal mentor dikurangi booking aktif yang sudah mengambil slot tsb.

    // Daftar slot untuk 1 tanggal: status ketersediaan dihitung dari mentor_availabilities dikurangi booking yang terisi.
    /**
     * Slots for a given date. Slot availability is derived from mentor_availabilities
     * minus bookings that already took the slot (bookings are the source of truth).
     */
    public function slotsForDate(MentorProfile $mentor, \Carbon\CarbonInterface $date): array
    {
        $rows = $mentor->availabilities()
            ->whereDate('date', $date->toDateString())
            ->where('status', 'available')
            ->orderBy('start_time')
            ->get();

        $taken = $this->takenSlotTimes($mentor, $date);

        return $rows->map(fn (MentorAvailability $row) => [
            'id' => $row->id,
            'date' => $row->date->toDateString(),
            'start_time' => $this->formatTime($row->start_time),
            'end_time' => $this->formatTime($row->end_time),
            'available' => ! $this->overlaps($row, $taken),
        ])->all();
    }

    // Semua tanggal ke depan yang masih punya minimal 1 slot kosong (untuk dipilih peserta).
    /**
     * All upcoming available dates for a mentor (dates having at least one free slot).
     */
    public function availableDates(MentorProfile $mentor, int $daysAhead = 90): array
    {
        return $mentor->availabilities()
            ->where('status', 'available')
            ->whereDate('date', '>=', Carbon::today()->toDateString())
            ->whereDate('date', '<=', Carbon::today()->addDays($daysAhead)->toDateString())
            ->select('date')
            ->distinct()
            ->orderBy('date')
            ->pluck('date')
            ->filter(fn (string $date) => $this->hasFreeSlot($mentor, Carbon::parse($date)))
            ->values()
            ->all();
    }

    // Cek apakah pada tanggal tertentu masih tersedia minimal satu slot yang belum di-book.
    public function hasFreeSlot(MentorProfile $mentor, \Carbon\CarbonInterface $date): bool
    {
        $taken = $this->takenSlotTimes($mentor, $date);

        return $mentor->availabilities()
            ->whereDate('date', $date->toDateString())
            ->where('status', 'available')
            ->get()
            ->contains(fn (MentorAvailability $row) => ! $this->overlaps($row, $taken));
    }

    // Cari baris slot yang bisa di-book sesuai tanggal & jam yang diminta (throw error jika tidak ada).
    /**
     * Find a bookable availability row matching the requested date/time.
     *
     * Time strings are compared in normalized H:i:s format so both
     * previously-seeded ("10:00:00") and mentor-created ("10:00") slots match.
     */
    public function findBookableSlot(MentorProfile $mentor, string $sessionDate, string $startTime, string $endTime): MentorAvailability
    {
        $startTime = $this->normalizeTime($startTime);
        $endTime = $this->normalizeTime($endTime);

        $slot = $mentor->availabilities()
            ->whereDate('date', $sessionDate)
            ->where('status', 'available')
            ->get()
            ->first(fn (MentorAvailability $row) => $this->normalizeTime($row->start_time) === $startTime
                && $this->normalizeTime($row->end_time) === $endTime);

        if (! $slot) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'slot' => 'Jadwal yang Anda pilih tidak tersedia. Silakan pilih jadwal lain.',
            ]);
        }

        return $slot;
    }

    // Cek apakah sudah ada booking aktif yang menempati jendela waktu yang diminta.
    /**
     * Determine whether an active booking already occupies the requested time window.
     */
    public function hasActiveBookingOnSlot(
        MentorProfile $mentor,
        string $sessionDate,
        string $startTime,
        string $endTime,
        ?int $availabilityId = null,
    ): bool {
        return Booking::query()
            ->where('mentor_id', $mentor->user_id)
            ->whereDate('session_date', $sessionDate)
            ->whereIn('booking_status', BookingStatus::activeStatuses())
            ->when($availabilityId !== null, fn ($q) => $q->where('mentor_availability_id', $availabilityId))
            ->when($availabilityId === null, fn ($q) => $q->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)->where('end_time', '>', $startTime);
                });
            }))
            ->exists();
    }

    // ==== Helper internal ====

    // Kembalikan daftar jendela waktu [start, end] yang sudah terisi booking aktif pada tanggal tertentu.
    /**
     * Returns list of [start, end] time windows already taken by active bookings on a date.
     */
    private function takenSlotTimes(MentorProfile $mentor, \Carbon\CarbonInterface $date): array
    {
        return Booking::query()
            ->where('mentor_id', $mentor->user_id)
            ->whereDate('session_date', $date->toDateString())
            ->whereIn('booking_status', BookingStatus::activeStatuses())
            ->get(['start_time', 'end_time'])
            ->map(fn (Booking $b) => [$b->start_time, $b->end_time])
            ->all();
    }

    // Cek apakah jadwal slot bersinggungan (overlap) dengan salah satu waktu yang sudah terisi.
    private function overlaps(MentorAvailability $row, array $taken): bool
    {
        foreach ($taken as [$start, $end]) {
            if ($row->start_time < $end && $row->end_time > $start) {
                return true;
            }
        }

        return false;
    }

    // Format jam menjadi "H:i" untuk ditampilkan ke user, mis. "10:00".
    private function formatTime(string $time): string
    {
        return Carbon::createFromFormat('H:i:s', $this->normalizeTime($time))->format('H:i');
    }

    // Normalisasi format jam menjadi H:i:s (mis. "10:00" -> "10:00:00") agar bisa dibandingkan.
    private function normalizeTime(string $time): string
    {
        $time = trim($time);

        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $m)) {
            $hour = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $seconds = $m[3] ?? '00';

            return $hour.':'.$m[2].':'.$seconds;
        }

        return $time;
    }
}