<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case PaymentPending = 'payment_pending';
    case PaymentVerification = 'payment_verification';
    case Confirmed = 'confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::PaymentPending => 'Menunggu Pembayaran',
            self::PaymentVerification => 'Menunggu Verifikasi Pembayaran',
            self::Confirmed => 'Confirmed',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::Rejected => 'Rejected',
        };
    }

    public static function activeStatuses(): array
    {
        return [
            self::Pending->value,
            self::PaymentPending->value,
            self::PaymentVerification->value,
            self::Confirmed->value,
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}