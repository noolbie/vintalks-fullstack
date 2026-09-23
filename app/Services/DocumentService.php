<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    // Simpan dokumen sensitif ke disk privat lalu simpan metadata-nya di tabel documents.
    /**
     * Store a sensitive document to the private disk and persist its metadata.
     */
    public function store(User $user, ?Booking $booking, string $documentType, UploadedFile $file): Document
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid().'.'.$extension;
        $path = $file->storeAs('documents/'.$documentType, $fileName, 'private');

        return Document::create([
            'user_id' => $user->id,
            'booking_id' => $booking?->id,
            'document_type' => $documentType,
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    // Hapus file dokumen dari disk privat dan hapus record-nya dari database.
    public function delete(Document $document): void
    {
        Storage::disk('private')->delete($document->file_path);
        $document->delete();
    }
}