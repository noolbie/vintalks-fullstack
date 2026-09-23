<?php

namespace App\Http\Controllers\Participant;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use App\Services\DocumentService;
use App\Models\Booking;
use App\Models\MentorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BookingController extends Controller
{
    // ==== Booking peserta ====

    // Tampilkan form booking untuk mentor tertentu (pilih topik, slot, dan isi syarat).
    public function create(MentorProfile $mentor, AvailabilityService $availabilityService): View
    {
        abort_if(! $mentor->is_active, 404);

        return view('participant.bookings.create', [
            'mentor' => $mentor->load('topics', 'user'),
            'topics' => $mentor->topics->sortBy('name'),
            'availableDates' => $availabilityService->availableDates($mentor),
        ]);
    }

    // Proses pembuatan booking + syarat konsultasi + upload dokumen awal.
    public function store(BookingRequest $request, MentorProfile $mentor, BookingService $bookingService, DocumentService $documentService): \Illuminate\Http\RedirectResponse
    {
        $mentor = MentorProfile::where('id', $mentor->id)->firstOrFail();
        $participant = Auth::user();

        $booking = $bookingService->create($mentor, $participant, [
            'slot_id' => $request->integer('slot_id'),
            'topic_id' => $request->input('topic_id'),
            'session_date' => $request->input('session_date'),
            'start_time' => $request->input('start_time'),
            'end_time' => $request->input('end_time'),
        ]);

        $booking->requirement()->create($request->only([
            'linkedin_url',
            'career_goal',
            'consultation_topic',
            'description',
            'additional_notes',
        ]));

        foreach ($request->file('documents', []) as $type => $file) {
            if ($file) {
                $documentService->store($participant, $booking, $type, $file);
            }
        }

        return redirect()->route('participant.bookings.show', $booking)
            ->with('success', 'Booking berhasil dibuat. Silakan lakukan pembayaran.');
    }

    // Detail booking milik peserta (atau admin yang melihat).
    public function show(Booking $booking): View
    {
        abort_unless($booking->isOwnedByParticipant(Auth::id()) || Auth::user()->isAdmin(), 403);

        $booking->load([
            'participant.participantProfile',
            'mentorProfile',
            'topic',
            'requirement',
            'payment.proofDocument',
            'consultationResult.documents',
            'documents',
        ]);

        return view('participant.bookings.show', [
            'booking' => $booking,
        ]);
    }

    // Riwayat booking peserta dengan filter status & pencarian kode.
    public function history(Request $request): View
    {
        $bookings = Auth::user()->participantBookings()
            ->with(['mentorProfile', 'payment'])
            ->when($request->filled('status'), fn ($q) => $q->where('booking_status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where('booking_code', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('participant.bookings.history', [
            'bookings' => $bookings,
        ]);
    }

    // Batalkan booking oleh peserta (opsional sertakan alasan).
    public function cancel(Request $request, Booking $booking, BookingService $bookingService): \Illuminate\Http\RedirectResponse
    {
        abort_unless($booking->isOwnedByParticipant(Auth::id()), 403);

        $request->validate(['cancellation_reason' => ['nullable', 'string', 'max:1000']]);

        try {
            $bookingService->cancel($booking, $request->input('cancellation_reason'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    // Tampilkan halaman konfirmasi pembatalan (meminta alasan sebelum cancel).
    public function cancelReason(Booking $booking): View
    {
        abort_unless($booking->isOwnedByParticipant(Auth::id()), 403);

        return view('participant.bookings.cancel-reason', ['booking' => $booking]);
    }
}