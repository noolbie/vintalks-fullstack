<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Notifications\SessionReminder;
use Illuminate\Console\Command;

class SendSessionReminderCommand extends Command
{
    protected $signature = 'vintalks:remind-sessions';

    protected $description = 'Kirim pengingat sesi konsultasi H-1 untuk booking confirmed';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();

        $bookings = Booking::query()
            ->with(['participant', 'mentorProfile'])
            ->where('booking_status', BookingStatus::Confirmed->value)
            ->whereDate('session_date', $tomorrow)
            ->get();

        foreach ($bookings as $booking) {
            $booking->participant->notify(new SessionReminder($booking));
            $this->info("Pengingat dikirim untuk {$booking->booking_code}");
        }

        if ($bookings->isEmpty()) {
            $this->info('Tidak ada sesi besok.');
        }

        return self::SUCCESS;
    }
}