<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($document->user_id === $user->id) {
            return true;
        }

        if ($document->booking_id && $user->isMentor()) {
            return $document->booking?->mentor_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Document $document): bool
    {
        return $document->user_id === $user->id;
    }
}