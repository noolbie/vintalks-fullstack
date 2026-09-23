<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Document: file yang diunggah user (bukti bayar, syarat, dll) di disk privat.
 */
class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
    use HasFactory;

    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'user_id',
        'booking_id',
        'document_type',
        'original_name',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    // ==== Relasi antar tabel ====

    // Dokumen dimiliki oleh 1 user yang mengunggahnya.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Dokumen bisa terkait dengan 1 booking (konsultasi).
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Dokumen bisa menjadi bukti bayar di 1 pembayaran.
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'id', 'proof_document_id');
    }

    // ==== Accessor ====

    // URL signed sementara (berlaku 30 menit) untuk mengunduh dokumen privat.
    public function getSignedUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'documents.show',
            now()->addMinutes(30),
            ['document' => $this->id]
        );
    }
}