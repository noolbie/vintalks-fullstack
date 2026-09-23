<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Booking $booking,
        public readonly string $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pembayaran Belum Dapat Diverifikasi — '.$this->booking->booking_code)
            ->greeting('Halo '.$this->booking->participant->name.',')
            ->line('Pembayaran Anda belum dapat diverifikasi. Silakan periksa alasan penolakan dan kirim ulang bukti pembayaran.')
            ->line('Alasan penolakan: '.$this->reason)
            ->action('Kirim Ulang Bukti', url(route('participant.payments.create', $this->booking)));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message' => 'Pembayaran Anda belum dapat diverifikasi. Alasan: '.$this->reason,
        ];
    }
}