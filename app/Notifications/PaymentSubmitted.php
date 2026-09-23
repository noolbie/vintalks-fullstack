<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSubmitted extends Notification implements ShouldQueue
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
            ->subject('Bukti Pembayaran Diterima — '.$this->booking->booking_code)
            ->greeting('Halo '.$this->booking->participant->name.',')
            ->line('Kami telah menerima bukti pembayaran Anda untuk booking '.$this->booking->booking_code.'.')
            ->line('Pembayaran Anda sedang menunggu verifikasi oleh admin. Anda akan mendapat notifikasi setelah diverifikasi.')
            ->line('Mentor: '.$this->booking->mentorProfile?->display_name)
            ->line('Jadwal: '.$this->booking->session_date->format('d M Y').' '.$this->booking->start_time_label.' - '.$this->booking->end_time_label.' WIB')
            ->action('Lihat Booking', url(route('participant.bookings.show', $this->booking)));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'message' => 'Bukti pembayaran Anda telah diterima dan sedang diverifikasi.',
        ];
    }
}