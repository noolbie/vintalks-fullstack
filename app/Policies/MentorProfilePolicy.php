<?php

namespace App\Policies;

use App\Models\MentorProfile;
use App\Models\User;

class MentorProfilePolicy
{
    public function manage(User $user, MentorProfile $mentor): bool
    {
        return $user->isAdmin() || $mentor->user_id === $user->id;
    }
}