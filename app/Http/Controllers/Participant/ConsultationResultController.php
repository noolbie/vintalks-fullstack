<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\ConsultationResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConsultationResultController extends Controller
{
    // Tampilkan detail hasil konsultasi untuk peserta pemiliknya (boleh juga dilihat admin).
    public function show(ConsultationResult $result): View
    {
        $user = Auth::user();

        abort_unless($user->isAdmin() || $result->participant_id === $user->id, 403);

        $result->load(['booking.mentorProfile', 'documents']);

        return view('participant.consultation-results.show', compact('result'));
    }
}