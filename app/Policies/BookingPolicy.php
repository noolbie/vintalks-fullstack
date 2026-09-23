<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isParticipant();
    }

    public function view(User $user, Booking $booking): bool
    {
        return $this->belongsToBooking($user, $booking);
    }

    public function viewParticipantBooking(User $user, Booking $booking): bool
    {
        return $booking->isOwnedByParticipant($user->id);
    }

    public function viewMentorBooking(User $user, Booking $booking): bool
    {
        return $booking->isOwnedByMentor($user->id);
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($booking->isOwnedByMentor($user->id) || $booking->isOwnedByParticipant($user->id)) {
            return $booking->isActive() && ! $booking->paymentIsVerified();
        }

        return $user->isAdmin();
    }

    public function complete(User $user, Booking $booking): bool
    {
        return $booking->isOwnedByMentor($user->id) || $user->isAdmin();
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $this->belongsToBooking($user, $booking);
    }

    private function belongsToBooking(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->isOwnedByParticipant($user->id) || $booking->isOwnedByMentor($user->id);
    }
}