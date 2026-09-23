<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransactionController extends Controller
{
    // ==== Transaksi mentor (potongan & pembayaran ke mentor) ====

    // Daftar sesi selesai yang akan dibayarkan ke mentor, lengkap dengan perhitungan komisi.
    public function index(Request $request): View
    {
        $transactions = Booking::query()
            ->with('participant', 'mentorProfile')
            ->where('booking_status', BookingStatus::Completed->value)
            ->when($request->filled('status') && $request->input('status') === 'paid', fn ($q) => $q->whereNotNull('mentor_paid_at'))
            ->when($request->filled('status') && $request->input('status') === 'pending', fn ($q) => $q->whereNull('mentor_paid_at'))
            ->orderByDesc('completed_at')
            ->paginate(15)
            ->withQueryString();

        $commissionRate = Setting::get('commission_rate', 15);

        return view('admin.transactions.index', compact('transactions', 'commissionRate'));
    }

    // Tandai sesi selesai sebagai sudah dibayarkan ke mentor.
    public function markPaid(Booking $booking): RedirectResponse
    {
        abort_unless($booking->booking_status === BookingStatus::Completed->value, 404);

        try {
            if ($booking->isPaidToMentor()) {
                throw ValidationException::withMessages(['status' => 'Transaksi ini sudah ditandai dibayar.']);
            }

            $booking->forceFill(['mentor_paid_at' => now()])->save();
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', "Pembayaran ke mentor untuk {$booking->booking_code} ditandai sebagai sudah dibayar.");
    }
}