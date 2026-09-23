<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isMentor();
    }

    public function view(User $user, Payment $payment): bool
    {
        $booking = $payment->booking;

        return $user->isAdmin()
            || $booking->isOwnedByParticipant($user->id)
            || $booking->isOwnedByMentor($user->id);
    }

    public function verify(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }
}