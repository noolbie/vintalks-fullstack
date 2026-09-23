<?php

namespace App\Http\Controllers\Mentor;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Tampilkan ringkasan sesi mentor (sesi hari ini, akan datang, selesai, hasil belum diisi, dan pendapatan).
    public function index(): View
    {
        $user = Auth::user();
        $mentorProfile = $user->mentorProfile;

        $todaySessions = $user->mentorBookings()
            ->with('participant')
            ->where('session_date', now()->today())
            ->whereIn('booking_status', [BookingStatus::Confirmed->value])
            ->orderBy('start_time')
            ->get();

        $upcomingSessions = $user->mentorBookings()
            ->with('participant')
            ->whereIn('booking_status', BookingStatus::activeStatuses())
            ->whereDate('session_date', '>=', now()->today())
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        $completedBookings = $user->mentorBookings()
            ->where('booking_status', BookingStatus::Completed->value)
            ->get();

        $completedSessions = $completedBookings->count();

        // Total pendapatan = jumlah harga semua sesi yang sudah selesai.
        $totalEarnings = $completedBookings->sum(fn ($b) => (float) $b->price);

        $totalParticipants = $user->mentorBookings()
            ->where('booking_status', BookingStatus::Completed->value)
            ->distinct('participant_id')
            ->count('participant_id');

        $pendingResults = $user->mentorBookings()
            ->where('booking_status', BookingStatus::Completed->value)
            ->whereDoesntHave('consultationResult')
            ->count();

        $recentBookings = $user->mentorBookings()
            ->with('participant')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('mentor.dashboard', compact(
            'mentorProfile',
            'todaySessions',
            'upcomingSessions',
            'completedSessions',
            'totalEarnings',
            'totalParticipants',
            'pendingResults',
            'recentBookings',
        ));
    }
}