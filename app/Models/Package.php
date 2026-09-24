<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Package: paket layanan konsultasi (Starter/Professional/Premium) yang bisa diedit admin.
 * Potongan rupiah paket = selisih harga normal (old_price) dikurangi harga promo (price).
 */
class Package extends Model
{
    // Kolom yang boleh diisi (mass assignment).
    protected $fillable = [
        'slug',
        'name',
        'description',
        'old_price',
        'price',
        'benefits',
        'image',
        'is_popular',
        'is_active',
    ];

    // Konversi tipe saat dibaca: harga decimal, benefits JSON, flag boolean.
    protected $casts = [
        'old_price' => 'decimal:2',
        'price' => 'decimal:2',
        'benefits' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ==== Accessor format untuk tampilan ====

    // Besaran potongan rupiah: harga normal - harga promo (minimal 0).
    public function getDiscountAttribute(): float
    {
        return max(0, (float) $this->old_price - (float) $this->price);
    }

    public function getDiscountFormattedAttribute(): string
    {
        return 'Rp'.number_format($this->discount, 0, ',', '.');
    }

    public function getOldPriceFormattedAttribute(): string
    {
        return 'Rp'.number_format((float) $this->old_price, 0, ',', '.');
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp'.number_format((float) $this->price, 0, ',', '.');
    }

    // ==== Relasi ====

    // Paket dipakai oleh banyak booking (sesi konsultasi).
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}