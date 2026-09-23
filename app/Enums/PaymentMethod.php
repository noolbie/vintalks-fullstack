<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Qris = 'qris';
    case TransferBank = 'transfer_bank';
    case DigitalWallet = 'digital_wallet';

    public function label(): string
    {
        return match ($this) {
            self::Qris => 'QRIS',
            self::TransferBank => 'Transfer Bank',
            self::DigitalWallet => 'Dompet Digital',
        };
    }

    public function isBankTransfer(): bool
    {
        return $this === self::TransferBank;
    }
}