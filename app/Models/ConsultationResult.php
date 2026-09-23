<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model ConsultationResult: ringkasan hasil konsultasi yang ditulis mentor.
 */
class ConsultationResult extends Model
{
    /** @use HasFactory<\Database\Factories\ConsultationResultFactory> */
    use HasFactory;

    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'booking_id',
        'mentor_id',
        'participant_id',
        'summary',
        'mentor_notes',
    ];

    // ==== Relasi antar tabel ====

    // Hasil konsultasi milik 1 booking.
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Mentor penulis hasil konsultasi.
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    // Peserta penerima hasil konsultasi.
    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    // Hasil konsultasi punya banyak dokumen lampiran.
    public function documents(): HasMany
    {
        return $this->hasMany(ConsultationResultDocument::class);
    }
}