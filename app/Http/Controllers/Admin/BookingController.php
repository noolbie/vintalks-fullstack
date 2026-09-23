<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminMeetingRequest;
use App\Services\BookingService;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    // ==== Manajemen booking ====

    // Daftar booking dengan filter pencarian, status, mentor, dan tanggal sesi.
    public function index(Request $request): View
    {
        $bookings = Booking::with(['participant', 'mentorProfile'])
            ->when($request->filled('q'), fn ($q) => $q->where('booking_code', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('booking_status', $request->input('status')))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->input('payment_status')))
            ->when($request->filled('mentor'), fn ($q) => $q->where('mentor_id', $request->integer('mentor')))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('session_date', $request->input('date')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'mentors' => User::role('mentor')->with('mentorProfile')->get(),
        ]);
    }

    // Detail booking lengkap (peserta, mentor, syarat, pembayaran, hasil konsultasi, dokumen).
    public function show(Booking $booking): View
    {
        $booking->load([
            'participant.participantProfile',
            'mentorProfile',
            'topic',
            'requirement',
            'payment.proofDocument',
            'consultationResult.documents',
            'documents',
        ]);

        return view('admin.bookings.show', compact('booking'));
    }

    // Simpan link meeting (oleh admin) lalu beri tahu peserta via notifikasi.
    public function updateMeeting(AdminMeetingRequest $request, Booking $booking): \Illuminate\Http\RedirectResponse
    {
        $booking->update($request->validated());

        $booking->participant->notify(new \App\Notifications\BookingConfirmed($booking));

        return back()->with('success', 'Link meeting berhasil disimpan dan peserta telah diberitahu.');
    }

    // Batalkan booking oleh admin dengan alasan (via BookingService).
    public function cancel(Request $request, Booking $booking, BookingService $bookingService): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['cancellation_reason' => ['required', 'string', 'max:1000']]);

        try {
            $bookingService->cancel($booking, $request->input('cancellation_reason'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Booking dibatalkan.');
    }
}