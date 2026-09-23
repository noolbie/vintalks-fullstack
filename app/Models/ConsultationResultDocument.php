<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\URL;

/**
 * Model ConsultationResultDocument: file lampiran dari suatu hasil konsultasi.
 */
class ConsultationResultDocument extends Model
{
    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'consultation_result_id',
        'title',
        'document_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    // ==== Relasi antar tabel ====

    // Dokumen lampiran dimiliki oleh 1 hasil konsultasi.
    public function consultationResult(): BelongsTo
    {
        return $this->belongsTo(ConsultationResult::class);
    }

    // ==== Accessor ====

    // URL signed sementara (berlaku 30 menit) untuk mengunduh dokumen privat.
    public function getSignedUrlAttribute(): string
    {
        return URL::temporarySignedRoute(
            'consultation-results.documents.show',
            now()->addMinutes(30),
            ['document' => $this->id]
        );
    }
}