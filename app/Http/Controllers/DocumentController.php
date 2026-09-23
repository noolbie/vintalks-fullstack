<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    // Unduh dokumen privat hanya lewat URL signed yang masih berlaku dan user yang berhak.
    /**
     * Download a private document via temporary signed URL.
     */
    public function show(Document $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        abort_if(! request()->user() || ! request()->user()->can('view', $document), 403, 'Anda tidak berhak mengakses dokumen ini.');

        abort_if(! Storage::disk('private')->exists($document->file_path), 404, 'File tidak ditemukan.');

        return Storage::disk('private')->download($document->file_path, $document->original_name);
    }
}