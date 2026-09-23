<?php

namespace App\Enums;

enum DocumentType: string
{
    case Cv = 'cv';
    case Linkedin = 'linkedin';
    case SupportingDocument = 'supporting_document';
    case PaymentProof = 'payment_proof';
    case ConsultationResult = 'consultation_result';
    case Assessment = 'assessment';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cv => 'CV',
            self::Linkedin => 'LinkedIn',
            self::SupportingDocument => 'Dokumen Pendukung',
            self::PaymentProof => 'Bukti Pembayaran',
            self::ConsultationResult => 'Hasil Konsultasi',
            self::Assessment => 'Assessment',
            self::Other => 'Lainnya',
        };
    }
}