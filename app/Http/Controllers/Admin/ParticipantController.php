<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    // ==== Manajemen peserta ====

    // Daftar user ber-role participant dengan pencarian nama/email.
    public function index(Request $request): View
    {
        $participants = User::role('participant')
            ->with(['participantProfile'])
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->string('q').'%')
                    ->orWhere('email', 'like', '%'.$request->string('q').'%');
            }))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.participants.index', compact('participants'));
    }

    // Detail profil peserta beserta riwayat booking & pembayarannya.
    public function show(User $user): View
    {
        abort_unless($user->hasRole('participant'), 404);

        $user->load(['participantProfile', 'participantBookings.mentorProfile', 'participantBookings.payment']);

        return view('admin.participants.show', compact('user'));
    }
}