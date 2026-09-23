<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ConsultationResult;
use App\Models\User;
use App\Notifications\ConsultationResultAvailable;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ConsultationService
{
    // Constructor: menyuntikkan BookingService untuk menandai booking selesai.
    public function __construct(
        private readonly BookingService $bookingService,
    ) {}

    // Buat/perbarui hasil konsultasi, tandai booking selesai, simpan lampiran, lalu beri tahu peserta.
    public function createResult(Booking $booking, User $mentor, array $data, array $files = []): ConsultationResult
    {
        if (! $booking->isOwnedByMentor($mentor->id)) {
            abort(403);
        }

        if ($booking->booking_status === 'completed' && $booking->consultationResult()->exists()) {
            throw ValidationException::withMessages([
                'booking' => 'Hasil konsultasi untuk sesi ini sudah ada.',
            ]);
        }

        $result = \DB::transaction(function () use ($booking, $mentor, $data, $files) {
            $result = ConsultationResult::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'mentor_id' => $booking->mentor_id,
                    'participant_id' => $booking->participant_id,
                    'summary' => data_get($data, 'summary'),
                    'mentor_notes' => data_get($data, 'mentor_notes'),
                ]
            );

            if ($booking->booking_status !== 'completed') {
                $this->bookingService->complete($booking);
            }

            foreach ($files as $file) {
                $this->addDocument($result, data_get($file, 'title', 'Hasil Konsultasi'), data_get($file, 'document_type', 'consultation_result'), $file['file']);
            }

            return $result;
        });

        $booking->participant->notify(new ConsultationResultAvailable($booking));

        return $result->load('documents');
    }

    // Simpan 1 dokumen lampiran ke hasil konsultasi (di disk privat).
    public function addDocument(ConsultationResult $result, string $title, string $documentType, UploadedFile $file): void
    {
        $path = $file->store('consultation-results', 'private');

        $result->documents()->create([
            'title' => $title,
            'document_type' => $documentType,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }
}