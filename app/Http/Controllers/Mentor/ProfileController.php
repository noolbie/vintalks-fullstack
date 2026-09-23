<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Http\Requests\MentorProfileRequest;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // ==== Profil mentor ====

    // Tampilkan form perbarui profil mentor (termasuk pilihan topik).
    public function edit(): View
    {
        return view('mentor.profile.edit', [
            'mentor' => Auth::user()->mentorProfile->load('topics'),
            'topics' => Topic::orderBy('name')->get(),
        ]);
    }

    // Simpan perubahan profil mentor (foto, bio, tarif, dan topik).
    public function update(MentorProfileRequest $request): \Illuminate\Http\RedirectResponse
    {
        $mentor = Auth::user()->mentorProfile;

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('mentor-photos', 'public');

            if ($mentor->photo) {
                Storage::disk('public')->delete($mentor->photo);
            }

            $data['photo'] = $path;
        }

        $mentor->update($data);

        if ($request->filled('topics')) {
            $mentor->topics()->sync($request->input('topics'));
        }

        if ($mentor->wasChanged() || $request->has('topics')) {
            cache()->forget('mentors_active');
        }

        return back()->with('success', 'Profil mentor berhasil diperbarui.');
    }
}