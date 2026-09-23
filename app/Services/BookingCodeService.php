<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Str;

class BookingCodeService
{
    // Buat kode booking unik (mis. "VT-202609-AB12CD") dengan mengulang sampai tidak bentrok di database.
    public function generateUnique(): string
    {
        do {
            $code = 'VT-'.now()->format('Ym').'-'.strtoupper(Str::random(6));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }
}