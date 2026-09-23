<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsultationResultAvailable extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Hasil Konsultasi Tersedia — '.$this->booking->booking_code)
            ->greeting('Halo '.$this->booking->participant->name.',')
            ->line('Hasil konsultasi Anda untuk booking '.$this->booking->booking_code.' telah tersedia.')
            ->line('Silakan buka halaman riwayat konsultasi untuk melihat ringkasan, catatan mentor, dan dokumen hasil.')
            ->action('Lihat Hasil', url(route('participant.consultation-results.show', $this->booking->consultationResult)));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message' => 'Hasil konsultasi untuk booking '.$this->booking->booking_code.' telah tersedia.',
        ];
    }
}