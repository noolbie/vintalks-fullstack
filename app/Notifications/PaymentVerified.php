<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerified extends Notification implements ShouldQueue
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
            ->subject('Pembayaran Terverifikasi — '.$this->booking->booking_code)
            ->greeting('Halo '.$this->booking->participant->name.',')
            ->line('Pembayaran Anda untuk booking '.$this->booking->booking_code.' telah terverifikasi.')
            ->line('Booking Anda kini berstatus Confirmed.')
            ->line('Mentor: '.$this->booking->mentorProfile?->display_name)
            ->line('Jadwal: '.$this->booking->session_date->format('d M Y').' '.$this->booking->start_time_label.' - '.$this->booking->end_time_label.' WIB')
            ->action('Lihat Booking', url(route('participant.bookings.show', $this->booking)));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message' => 'Pembayaran Anda telah terverifikasi. Booking Anda kini Confirmed.',
        ];
    }
}