<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // Tampilkan ringkasan statistik admin (jumlah user, booking, pembayaran menunggu, revenue, dll).
    public function index(): View
    {
        $totalParticipants = User::role('participant')->count();
        $totalMentors = User::role('mentor')->count();
        $totalBookings = Booking::count();

        $pendingVerification = Booking::where('payment_status', PaymentStatus::WaitingVerification->value)->count();
        $confirmedBookings = Booking::where('booking_status', BookingStatus::Confirmed->value)->count();
        $completedSessions = Booking::where('booking_status', BookingStatus::Completed->value)->count();
        $upcomingSessions = Booking::where('booking_status', BookingStatus::Confirmed->value)
            ->whereDate('session_date', '>=', now()->today())
            ->count();

        $revenue = Booking::where('booking_status', BookingStatus::Completed->value)
            ->join('payments', 'payments.booking_id', '=', 'bookings.id')
            ->where('payments.status', PaymentStatus::Verified->value)
            ->sum('payments.amount');

        $recentBookings = Booking::with(['participant', 'mentorProfile'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentPayments = \App\Models\Payment::with(['booking.participant'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalParticipants',
            'totalMentors',
            'totalBookings',
            'pendingVerification',
            'confirmedBookings',
            'completedSessions',
            'revenue',
            'upcomingSessions',
            'recentBookings',
            'recentPayments',
        ));
    }
}