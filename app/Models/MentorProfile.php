<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model MentorProfile: profil publik mentor (display_name, tarif, keahlian, dll).
 */
class MentorProfile extends Model
{
    /** @use HasFactory<\Database\Factories\MentorProfileFactory> */
    use HasFactory;

    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'user_id',
        'display_name',
        'slug',
        'photo',
        'bio',
        'experience',
        'expertise',
        'price',
        'is_active',
    ];

    // Konversi otomatis tipe data saat dibaca.
    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Saat profil disimpan, buat slug otomatis dari display_name jika belum diisi.
    protected static function booted(): void
    {
        static::saving(function (MentorProfile $mentor) {
            if (empty($mentor->slug)) {
                $mentor->slug = Str::slug($mentor->display_name);
            }
        });
    }

    // ==== Relasi antar tabel ====

    // Profil mentor dimiliki oleh 1 user.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Mentor punya banyak topik (melalui tabel pivot `mentor_topic`).
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'mentor_topic', 'mentor_id', 'topic_id');
    }

    // Mentor punya banyak slot ketersediaan.
    public function availabilities(): HasMany
    {
        return $this->hasMany(MentorAvailability::class, 'mentor_id');
    }

    // Mentor punya banyak booking.
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'mentor_id');
    }

    // ==== Accessor format untuk tampilan ====

    // URL lengkap foto profil mentor di storage publik.
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        return asset('storage/'.$this->photo);
    }

    // Tarif mentor dalam format Rupiah, mis. "Rp150.000".
    public function getPriceFormattedAttribute(): string
    {
        return 'Rp'.number_format((float) $this->price, 0, ',', '.');
    }
}