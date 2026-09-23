<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvailabilityRequest;
use App\Models\MentorAvailability;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    // ==== Jadwal ketersediaan ====

    // Tampilkan jadwal ketersediaan per bulan beserta slot yang sudah ter-book.
    public function index(Request $request, AvailabilityService $availabilityService): View
    {
        $mentor = Auth::user()->mentorProfile;

        $month = $request->input('month');
        try {
            $from = $month ? \Carbon\Carbon::parse($month)->startOfMonth() : now()->startOfMonth();
        } catch (\Throwable) {
            $from = now()->startOfMonth();
        }
        $to = $from->copy()->endOfMonth();

        $availabilities = $mentor->availabilities()
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $bookedSlots = $this->bookedSlots($mentor->user_id, $from, $to);

        return view('mentor.availability.index', compact('availabilities', 'from', 'bookedSlots'));
    }

    // Simpan satu atau beberapa slot ketersediaan baru (skip slot dengan jam tidak valid).
    public function store(AvailabilityRequest $request): \Illuminate\Http\RedirectResponse
    {
        $mentor = Auth::user()->mentorProfile;

        foreach ($request->input('slots') as $slot) {
            $start = $this->normalizeTime($slot['start_time']);
            $end = $this->normalizeTime($slot['end_time']);

            if ($end <= $start) {
                continue;
            }

            MentorAvailability::create([
                'mentor_id' => $mentor->id,
                'date' => $slot['date'],
                'start_time' => $start,
                'end_time' => $end,
                'status' => $slot['status'] ?? 'available',
            ]);
        }

        return back()->with('success', 'Jadwal ketersediaan berhasil ditambahkan.');
    }

    // Hapus slot ketersediaan milik mentor sendiri.
    public function destroy(MentorAvailability $availability): \Illuminate\Http\RedirectResponse
    {
        abort_unless($availability->mentor_id === Auth::user()->mentorProfile->id, 403);

        $availability->delete();

        return back()->with('success', 'Slot ketersediaan dihapus.');
    }

    // ==== Helper internal ====

    // Normalisasi format jam menjadi H:i:s (mis. "10:00" -> "10:00:00").
    private function normalizeTime(string $time): string
    {
        $time = trim($time);

        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $m)) {
            $hour = str_pad($m[1], 2, '0', STR_PAD_LEFT);

            return $hour.':'.$m[2].':'.($m[3] ?? '00');
        }

        return $time;
    }

    // Ambil daftar slot yang sudah dipakai booking aktif dalam rentang tanggal (untuk penanda "sudah ter-book").
    private function bookedSlots(int $mentorUserId, \Carbon\Carbon $from, \Carbon\Carbon $to): \Illuminate\Support\Collection
    {
        return \App\Models\Booking::query()
            ->where('mentor_id', $mentorUserId)
            ->whereBetween('session_date', [$from->toDateString(), $to->toDateString()])
            ->whereIn('booking_status', \App\Enums\BookingStatus::activeStatuses())
            ->with('participant:id,name')
            ->get(['session_date', 'start_time', 'end_time']);
    }
}