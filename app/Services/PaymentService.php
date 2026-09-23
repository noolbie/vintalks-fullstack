<?php

namespace App\Services;

use App\Enums\PaymentStatus; // Enum status pembayaran (pending, waiting_verification, verified, rejected, dll).
use App\Models\Booking;
use App\Models\Document;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentRejected;
use App\Notifications\PaymentSubmitted;
use App\Notifications\PaymentVerified;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    // Constructor: menyuntikkan BookingService (ubah status booking) & DocumentService (simpan bukti bayar).
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly DocumentService $documentService,
    ) {}

    // Simpan bukti bayar, catat pembayaran, lalu ubah booking jadi menunggu verifikasi admin + kirim notifikasi.
    public function submitProof(Booking $booking, UploadedFile $file, array $data = []): Payment
    {
        throw_if($booking->paymentIsVerified(), ValidationException::withMessages([
            'payment' => 'Pembayaran booking ini sudah terverifikasi.',
        ]));

        $document = $this->documentService->store($booking->participant, $booking, 'payment_proof', $file);

        return \DB::transaction(function () use ($booking, $document, $data) {
            $payment = $booking->payment()->firstOrNew([]);

            $payment->fill([
                'booking_id' => $booking->id,
                'amount' => data_get($data, 'amount', $booking->price),
                'payment_method' => data_get($data, 'payment_method', 'transfer_bank'),
                'payment_reference' => data_get($data, 'payment_reference'),
                'proof_document_id' => $document->id,
                'submitted_at' => now(),
                'status' => 'pending',
            ])->save();

            if ($payment->status !== 'rejected') {
                $this->bookingService->submitPaymentProof($booking);
                $payment->forceFill(['status' => PaymentStatus::WaitingVerification->value])->save();
            }

            $booking->participant->notify(new PaymentSubmitted($booking));

            return $payment->fresh();
        });
    }

    // Verifikasi pembayaran oleh admin lalu konfirmasi booking, dan kirim notifikasi ke peserta.
    public function verify(Payment $payment, User $admin): void
    {
        throw_if($payment->status === PaymentStatus::Verified->value, ValidationException::withMessages([
            'payment' => 'Pembayaran sudah diverifikasi.',
        ]));

        \DB::transaction(function () use ($payment, $admin) {
            $payment->forceFill([
                'status' => PaymentStatus::Verified->value,
                'verified_at' => now(),
                'verified_by' => $admin->id,
                'rejection_reason' => null,
            ])->save();

            $this->bookingService->confirm($payment->booking);
        });

        $payment->booking->participant->notify(new PaymentVerified($payment->booking));
    }

    // Tolak pembayaran dengan alasan, batalkan booking, dan kirim notifikasi ke peserta.
    public function reject(Payment $payment, User $admin, string $reason): void
    {
        throw_if(trim($reason) === '', ValidationException::withMessages([
            'rejection_reason' => 'Alasan penolakan wajib diisi.',
        ]));

        \DB::transaction(function () use ($payment, $admin, $reason) {
            $payment->forceFill([
                'status' => PaymentStatus::Rejected->value,
                'verified_at' => now(),
                'verified_by' => $admin->id,
                'rejection_reason' => $reason,
            ])->save();

            $this->bookingService->reject($payment->booking);
        });

        $payment->booking->participant->notify(new PaymentRejected($payment->booking, $reason));
    }
}