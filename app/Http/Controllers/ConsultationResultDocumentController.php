<?php

namespace App\Http\Controllers;

use App\Models\ConsultationResultDocument;
use Illuminate\Support\Facades\Storage;

class ConsultationResultDocumentController extends Controller
{
    // Unduh dokumen lampiran hasil konsultasi (hanya admin, mentor, atau peserta terkait).
    public function show(ConsultationResultDocument $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $result = $document->consultationResult;

        abort_if(! $result || ! request()->user(), 403);
        abort_unless(request()->user()->isAdmin()
            || $result->participant_id === request()->user()->id
            || $result->mentor_id === request()->user()->id, 403, 'Anda tidak berhak mengakses dokumen ini.');

        abort_if(! Storage::disk('private')->exists($document->file_path), 404, 'File tidak ditemukan.');

        return Storage::disk('private')->download($document->file_path, $document->original_name);
    }
}