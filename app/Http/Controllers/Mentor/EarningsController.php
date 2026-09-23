<?php

namespace App\Http\Controllers\Mentor;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EarningsController extends Controller
{
    // ==== Pendapatan mentor ====

    // Tampilkan ringkasan pendapatan + rincian per sesi selesai (harga, potongan, bersih, status dibayar).
    public function index(): View
    {
        $transactions = Auth::user()->mentorBookings()
            ->with('participant')
            ->where('booking_status', BookingStatus::Completed->value)
            ->orderByDesc('completed_at')
            ->get();

        // Total nominal yang SUDAH dibayarkan admin ke mentor (sesi yang ditandai dibayar).
        $totalReceived = $transactions
            ->filter(fn (Booking $b) => $b->isPaidToMentor())
            ->sum(fn (Booking $b) => $b->mentor_net);

        // Total nominal yang masih menunggu pembayaran admin.
        $totalPending = $transactions
            ->reject(fn (Booking $b) => $b->isPaidToMentor())
            ->sum(fn (Booking $b) => $b->mentor_net);

        $totalSessions = $transactions->count();
        $totalFee = $transactions->sum(fn (Booking $b) => $b->commission_amount);
        $commissionRate = Setting::get('commission_rate', 15);

        return view('mentor.earnings.index', compact(
            'transactions',
            'totalReceived',
            'totalPending',
            'totalSessions',
            'totalFee',
            'commissionRate',
        ));
    }
}