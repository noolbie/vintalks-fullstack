<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultationResultRequest;
use App\Services\ConsultationService;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConsultationResultController extends Controller
{
    // ==== Hasil konsultasi mentor ====

    // Tampilkan form pengisian hasil konsultasi untuk booking milik mentor yang sudah selesai.
    public function create(Booking $booking): View
    {
        abort_unless($booking->isOwnedByMentor(Auth::id()), 403);
        abort_unless($booking->booking_status === 'completed', 403, 'Booking belum selesai.');

        $booking->load(['participant.participantProfile', 'requirement', 'consultationResult.documents']);

        return view('mentor.consultation-results.create', [
            'booking' => $booking,
        ]);
    }

    // Simpan hasil konsultasi + dokumen lampiran (via ConsultationService) lalu beri tahu peserta.
    public function store(ConsultationResultRequest $request, Booking $booking, ConsultationService $consultationService): \Illuminate\Http\RedirectResponse
    {
        abort_unless($booking->isOwnedByMentor(Auth::id()), 403);

        $files = collect($request->input('documents', []))->map(function ($doc, $index) use ($request) {
            return [
                'title' => $doc['title'] ?? null,
                'document_type' => $doc['document_type'] ?? 'consultation_result',
                'file' => $request->file('documents')[$index]['file'] ?? null,
            ];
        })->filter(fn ($doc) => $doc['file'] !== null)->all();

        $consultationService->createResult($booking, Auth::user(), $request->only(['summary', 'mentor_notes']), $files);

        return redirect()->route('mentor.bookings.show', $booking)
            ->with('success', 'Hasil konsultasi berhasil disimpan.');
    }
}