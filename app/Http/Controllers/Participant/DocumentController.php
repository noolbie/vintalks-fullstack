<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use App\Models\Booking;
use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    // ==== Dokumen peserta ====

    // Unggah dokumen pendukung untuk sebuah booking (mis. syarat tambahan).
    public function store(DocumentUploadRequest $request, Booking $booking, DocumentService $documentService): \Illuminate\Http\RedirectResponse
    {
        abort_if($booking->participant_id !== Auth::id(), 403);

        $documentService->store(Auth::user(), $booking, $request->input('document_type'), $request->file('document'));

        return back()->with('success', 'Dokumen berhasil diunggah.');
    }

    // Hapus dokumen milik peserta pada booking yang bersangkutan.
    public function destroy(Booking $booking, Document $document, DocumentService $documentService): \Illuminate\Http\RedirectResponse
    {
        abort_unless($document->user_id === Auth::id(), 403);
        abort_unless($booking->id === $document->booking_id, 403);

        $documentService->delete($document);

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}