<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    // ==== Booking mentor ====

    // Daftar booking milik mentor dengan filter status & pencarian kode.
    public function index(Request $request): View
    {
        $bookings = Auth::user()->mentorBookings()
            ->with(['participant.participantProfile', 'payment'])
            ->when($request->filled('status'), fn ($q) => $q->where('booking_status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where('booking_code', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('mentor.bookings.index', compact('bookings'));
    }

    // Detail booking milik mentor (peserta, syarat, pembayaran, dan hasil konsultasi).
    public function show(Booking $booking): View
    {
        abort_unless($booking->isOwnedByMentor(Auth::id()), 403);

        $booking->load([
            'participant.participantProfile',
            'requirement',
            'payment.proofDocument',
            'topic',
            'documents',
            'consultationResult.documents',
        ]);

        return view('mentor.bookings.show', [
            'booking' => $booking,
        ]);
    }

    // Tandai sesi selesai (via BookingService) lalu beri tahu peserta.
    public function complete(Request $request, Booking $booking, BookingService $bookingService): \Illuminate\Http\RedirectResponse
    {
        abort_unless($booking->isOwnedByMentor(Auth::id()), 403);

        try {
            $bookingService->complete($booking);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        $booking->participant->notify(new \App\Notifications\ConsultationCompleted($booking));

        return back()->with('success', 'Sesi ditandai selesai.');
    }
}