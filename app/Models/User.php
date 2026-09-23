<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Model User: pengguna aplikasi (peserta, mentor, atau admin) yang memakai role via Spatie.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    // Kolom yang boleh diisi user (mass assignment).
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Kolom yang disembunyikan saat model diserialisasi (mis. menjadi JSON).
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Konversi otomatis tipe data saat dibaca (password otomatis di-hash, dll).
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==== Relasi antar tabel ====

    // User punya 1 profil peserta (jika ber-role participant).
    public function participantProfile(): HasOne
    {
        return $this->hasOne(ParticipantProfile::class);
    }

    // User punya 1 profil mentor (jika ber-role mentor).
    public function mentorProfile(): HasOne
    {
        return $this->hasOne(MentorProfile::class);
    }

    // User (sebagai peserta) punya banyak booking.
    public function participantBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'participant_id');
    }

    // User (sebagai mentor) punya banyak booking.
    public function mentorBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'mentor_id');
    }

    // User punya banyak dokumen (bukti bayar, syarat, dll).
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // ==== Cek peran user ====

    // Cek apakah user ber-role participant.
    public function isParticipant(): bool
    {
        return $this->hasRole('participant');
    }

    // Cek apakah user ber-role mentor.
    public function isMentor(): bool
    {
        return $this->hasRole('mentor');
    }

    // Cek apakah user ber-role admin.
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    // ==== Accessor ====

    // Nama tampilan user (pakai display_name mentor jika ada, selain itu nama asli).
    public function getDisplayNameAttribute(): string
    {
        return $this->mentorProfile?->display_name ?? $this->name;
    }
}