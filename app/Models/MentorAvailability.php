<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model MentorAvailability: slot jadwal kosong yang dibuka mentor untuk di-book peserta.
 */
class MentorAvailability extends Model
{
    /** @use HasFactory<\Database\Factories\MentorAvailabilityFactory> */
    use HasFactory;

    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'mentor_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    // Konversi otomatis tipe data saat dibaca.
    protected $casts = [
        'date' => 'date',
    ];

    // ==== Relasi antar tabel ====

    // Slot ketersediaan dimiliki oleh 1 mentor (lewat MentorProfile).
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(MentorProfile::class, 'mentor_id');
    }

    // Slot ketersediaan bisa dipakai oleh banyak booking.
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}