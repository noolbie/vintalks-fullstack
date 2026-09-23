<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ParticipantProfile: profil tambahan milik peserta (1 baris per user).
 */
class ParticipantProfile extends Model
{
    /** @use HasFactory<\Database\Factories\ParticipantProfileFactory> */
    use HasFactory;

    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'user_id',
        'phone',
        'gender',
        'birth_date',
        'occupation',
        'institution',
        'linkedin_url',
        'profile_photo',
        'address',
    ];

    // Konversi otomatis tipe data saat dibaca.
    protected $casts = [
        'birth_date' => 'date',
    ];

    // ==== Relasi antar tabel ====

    // Profil peserta dimiliki oleh 1 user.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}