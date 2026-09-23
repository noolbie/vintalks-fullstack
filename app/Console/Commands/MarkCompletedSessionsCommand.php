<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Console\Command;

class MarkCompletedSessionsCommand extends Command
{
    protected $signature = 'vintalks:complete-sessions';

    protected $description = 'Menandai booking confirmed yang waktunya sudah lewat sebagai completed';

    public function handle(BookingService $bookingService): int
    {
        $bookings = Booking::query()
            ->where('booking_status', BookingStatus::Confirmed->value)
            ->where(function ($q) {
                $q->whereDate('session_date', '<', now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->whereDate('session_date', now()->toDateString())
                            ->whereTime('end_time', '<=', now()->format('H:i:s'));
                    });
            })
            ->get();

        foreach ($bookings as $booking) {
            try {
                $bookingService->complete($booking);
                $this->info("Booking {$booking->booking_code} ditandai completed.");
            } catch (\Throwable $e) {
                $this->error("Gagal menyelesaikan {$booking->booking_code}: {$e->getMessage()}");
            }
        }

        if ($bookings->isEmpty()) {
            $this->info('Tidak ada sesi yang perlu ditandai selesai.');
        }

        return self::SUCCESS;
    }
}