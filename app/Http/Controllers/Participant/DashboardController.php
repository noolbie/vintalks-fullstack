<?php

namespace App\Http\Controllers\Participant;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Tampilkan dashboard peserta: sesi terdekat, booking aktif, riwayat, dan hasil konsultasi.
    public function index(): View
    {
        $user = Auth::user();

        $upcomingSession = $user->participantBookings()
            ->with(['mentorProfile', 'payment'])
            ->whereIn('booking_status', [BookingStatus::Confirmed->value, BookingStatus::PaymentVerification->value, BookingStatus::PaymentPending->value])
            ->whereDate('session_date', '>=', now()->today())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->first();

        $activeBookings = $user->participantBookings()
            ->with(['mentorProfile', 'payment'])
            ->whereIn('booking_status', BookingStatus::activeStatuses())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $history = $user->participantBookings()
            ->with(['mentorProfile', 'consultationResult'])
            ->whereIn('booking_status', [BookingStatus::Completed->value, BookingStatus::Cancelled->value, BookingStatus::Rejected->value])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $consultationResults = $user->participantBookings()
            ->with('consultationResult.documents')
            ->whereHas('consultationResult')
            ->orderByDesc('completed_at')
            ->limit(5)
            ->get()
            ->map(fn (Booking $b) => $b->consultationResult);

        return view('participant.dashboard', compact(
            'upcomingSession',
            'activeBookings',
            'history',
            'consultationResults',
        ));
    }

    // Tampilkan daftar notifikasi peserta.
    public function notifications(): View
    {
        $user = Auth::user();

        return view('participant.notifications', [
            'notifications' => $user->notifications()->paginate(20),
        ]);
    }

    // Tandai satu notifikasi sebagai sudah dibaca.
    public function markNotificationRead(string $id): \Illuminate\Http\RedirectResponse
    {
        Auth::user()->notifications()->where('id', $id)->get()->each->markAsRead();

        return back();
    }
}