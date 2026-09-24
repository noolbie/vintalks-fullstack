<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\MeetingProvider;
use App\Enums\PackageApprovalStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * Model Booking: mewakili 1 sesi konsultasi (1 baris di tabel `bookings`).
 */
class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    // Kolom yang boleh diisi user (mass assignment).
    protected $fillable = [
        'booking_code',
        'participant_id',
        'mentor_id',
        'mentor_availability_id',
        'topic_id',
        'package_id',
        'session_date',
        'start_time',
        'end_time',
        'price',
        'discount_amount',
        'package_approval_status',
        'package_approved_at',
        'package_rejected_at',
        'package_rejection_reason',
        'booking_status',
        'payment_status',
        'meeting_provider',
        'meeting_url',
        'confirmed_at',
        'completed_at',
        'mentor_paid_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    // Konversi otomatis tipe data saat dibaca (tanggal -> Carbon, dll).
    protected $casts = [
        'session_date' => 'date',
        'price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'package_approved_at' => 'datetime',
        'package_rejected_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'mentor_paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Accessor format untuk tampilan, mis. $booking->start_time_label -> "10:00".
    public function getStartTimeLabelAttribute(): string
    {
        return $this->start_time ? \Carbon\Carbon::createFromFormat('H:i:s', $this->start_time)->format('H:i') : '';
    }

    public function getEndTimeLabelAttribute(): string
    {
        return $this->end_time ? \Carbon\Carbon::createFromFormat('H:i:s', $this->end_time)->format('H:i') : '';
    }

    // Membuat kode booking unik, mis. "VT-202609-AB12CD".
    public static function generateBookingCode(): string
    {
        return 'VT-'.now()->format('Ym').'-'.strtoupper(Str::random(6));
    }

    // ==== Relasi antar tabel ====

    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function mentorProfile(): BelongsTo
    {
        return $this->belongsTo(MentorProfile::class, 'mentor_id', 'user_id');
    }

    public function mentorAvailability(): BelongsTo
    {
        return $this->belongsTo(MentorAvailability::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    // Paket potongan harga yang dipilih peserta (opsional).
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // Booking punya 1 syarat konsultasi.
    public function requirement(): HasOne
    {
        return $this->hasOne(ConsultationRequirement::class);
    }

    // Booking punya 1 pembayaran.
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // Booking punya 1 hasil konsultasi.
    public function consultationResult(): HasOne
    {
        return $this->hasOne(ConsultationResult::class);
    }

    // Booking punya banyak dokumen (bukti bayar, syarat, hasil konsultasi).
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // ==== Fungsi bantu untuk cek hak akses & status ====

    public function isOwnedByParticipant(int $userId): bool
    {
        return $this->participant_id === $userId;
    }

    public function isOwnedByMentor(int $userId): bool
    {
        return $this->mentor_id === $userId;
    }

    public function isActive(): bool
    {
        return in_array($this->booking_status, BookingStatus::activeStatuses(), true);
    }

    public function paymentIsVerified(): bool
    {
        return $this->payment_status === PaymentStatus::Verified->value;
    }

    public function hasMeetingUrl(): bool
    {
        return $this->booking_status === BookingStatus::Confirmed->value && ! empty($this->meeting_url);
    }

    // ==== Accessor format untuk tampilan (currency & label status) ====

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp'.number_format((float) $this->price, 0, ',', '.');
    }

    public function getMeetingProviderLabelAttribute(): ?string
    {
        return $this->meeting_provider ? MeetingProvider::from($this->meeting_provider)->label() : null;
    }

    public function getBookingStatusLabelAttribute(): string
    {
        return BookingStatus::from($this->booking_status)->label();
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return PaymentStatus::from($this->payment_status)->label();
    }

    // ==== Paket potongan harga ====

    // Apakah booking memakai paket potongan.
    public function hasPackage(): bool
    {
        return $this->package_id !== null;
    }

    // Status persetujuan paket (menunggu admin / disetujui / ditolak).
    public function getPackageStatusLabelAttribute(): string
    {
        return PackageApprovalStatus::from($this->package_approval_status)->label();
    }

    // Harga sesi sebelum dipotong paket (harga normal mentor).
    public function getPackageBasePriceAttribute(): float
    {
        return round(((float) $this->price) + (float) ($this->discount_amount ?? 0), 2);
    }

    public function getDiscountAmountFormattedAttribute(): string
    {
        return 'Rp'.number_format((float) ($this->discount_amount ?? 0), 0, ',', '.');
    }

    public function getPackageBasePriceFormattedAttribute(): string
    {
        return 'Rp'.number_format($this->package_base_price, 0, ',', '.');
    }

    public function isPackagePending(): bool
    {
        return $this->package_approval_status === PackageApprovalStatus::Pending->value;
    }

    public function isPackageApproved(): bool
    {
        return $this->package_approval_status === PackageApprovalStatus::Approved->value;
    }

    public function isPackageRejected(): bool
    {
        return $this->package_approval_status === PackageApprovalStatus::Rejected->value;
    }

    // ==== Komisi & pendapatan mentor ====

    // Persentase potongan platform (setting, mis. 15 = 15%).
    public function getCommissionRateAttribute(): float
    {
        return (float) Setting::get('commission_rate', 15);
    }

    // Nominal potongan: harga sesi x persen komisi.
    public function getCommissionAmountAttribute(): float
    {
        return round(((float) $this->price) * $this->commission_rate / 100, 2);
    }

    // Nominal yang diterima mentor: harga sesi - potongan komisi.
    public function getMentorNetAttribute(): float
    {
        return round(((float) $this->price) - $this->commission_amount, 2);
    }

    // Apakah pendapatan sesi ini sudah dibayarkan admin ke mentor.
    public function isPaidToMentor(): bool
    {
        return $this->mentor_paid_at !== null;
    }
}