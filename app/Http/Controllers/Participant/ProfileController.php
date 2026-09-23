<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\ParticipantProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // ==== Profil peserta ====

    // Tampilkan form perbarui profil peserta.
    public function edit(): View
    {
        return view('participant.profile.edit', [
            'profile' => Auth::user()->participantProfile,
        ]);
    }

    // Simpan perubahan profil peserta (termasuk foto profil).
    public function update(ParticipantProfileRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->participantProfile;

        $data = $request->validated();

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('participant-photos', 'public');

            if ($profile->profile_photo) {
                Storage::disk('public')->delete($profile->profile_photo);
            }

            $data['profile_photo'] = $path;
        }

        $profile->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}