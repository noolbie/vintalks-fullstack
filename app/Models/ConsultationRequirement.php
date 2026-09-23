<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ConsultationRequirement: syarat/informasi yang diisi peserta sebelum konsultasi.
 */
class ConsultationRequirement extends Model
{
    /** @use HasFactory<\Database\Factories\ConsultationRequirementFactory> */
    use HasFactory;

    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'booking_id',
        'linkedin_url',
        'career_goal',
        'consultation_topic',
        'description',
        'additional_notes',
    ];

    // ==== Relasi antar tabel ====

    // Syarat konsultasi ini dimiliki oleh 1 booking.
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}