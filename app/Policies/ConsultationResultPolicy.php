<?php

namespace App\Policies;

use App\Models\ConsultationResult;
use App\Models\User;

class ConsultationResultPolicy
{
    public function view(User $user, ConsultationResult $result): bool
    {
        return $user->isAdmin()
            || $result->participant_id === $user->id
            || $result->mentor_id === $user->id;
    }

    public function create(User $user, ConsultationResult $result): bool
    {
        return $result->mentor_id === $user->id;
    }
}