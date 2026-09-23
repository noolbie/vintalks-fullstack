<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Payment: catatan pembayaran untuk 1 booking (1 baris di tabel `payments`).
 */
class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'payment_reference',
        'proof_document_id',
        'submitted_at',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'status',
    ];

    // Konversi otomatis tipe data saat dibaca.
    protected $casts = [
        'amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    // ==== Relasi antar tabel ====

    // Pembayaran milik 1 booking.
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Dokumen bukti bayar yang dilampirkan peserta.
    public function proofDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'proof_document_id');
    }

    // Admin/verifikator yang memverifikasi pembayaran.
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ==== Accessor format untuk tampilan ====

    // Jumlah pembayaran dalam format Rupiah, mis. "Rp150.000".
    public function getAmountFormattedAttribute(): string
    {
        return 'Rp'.number_format((float) $this->amount, 0, ',', '.');
    }

    // Label metode pembayaran (mis. "Transfer Bank") dari enum PaymentMethod.
    public function getPaymentMethodLabelAttribute(): string
    {
        return PaymentMethod::tryFrom((string) $this->payment_method)?->label() ?? (string) $this->payment_method;
    }
}