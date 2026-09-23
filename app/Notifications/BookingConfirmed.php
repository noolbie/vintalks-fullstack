<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Booking Dikonfirmasi — '.$this->booking->booking_code)
            ->greeting('Halo '.$this->booking->participant->name.',')
            ->line('Booking Anda telah dikonfirmasi dan sesi konsultasi dijadwalkan sebagai berikut:')
            ->line('Mentor: '.$this->booking->mentorProfile?->display_name)
            ->line('Tanggal: '.$this->booking->session_date->format('d M Y'))
            ->line('Waktu: '.$this->booking->start_time_label.' - '.$this->booking->end_time_label.' WIB');

        if ($this->booking->meeting_url) {
            $mail->line('Link Meeting: '.$this->booking->meeting_url);
        }

        return $mail->action('Lihat Booking', url(route('participant.bookings.show', $this->booking)));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message' => 'Booking Anda telah dikonfirmasi. Persiapkan diri Anda untuk sesi konsultasi.',
        ];
    }
}