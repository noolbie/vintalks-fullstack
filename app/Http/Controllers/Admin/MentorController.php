<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MentorProfileRequest;
use App\Models\MentorProfile;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MentorController extends Controller
{
    // ==== Manajemen mentor ====

    // Daftar mentor dengan filter pencarian nama/keahlian & status aktif.
    public function index(Request $request): View
    {
        $mentors = MentorProfile::with('user')
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('display_name', 'like', '%'.$request->string('q').'%')
                    ->orWhere('expertise', 'like', '%'.$request->string('q').'%');
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->boolean('status')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.mentors.index', compact('mentors'));
    }

    // Form tambah mentor baru (ikut pilih topik keahlian).
    public function create(): View
    {
        return view('admin.mentors.create', [
            'topics' => Topic::orderBy('name')->get(),
        ]);
    }

    // Simpan mentor baru: buat user ber-role mentor + profil + link topik.
    public function store(MentorProfileRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['display_name'],
            'email' => $request->input('email'),
            'password' => $request->input('password') ?? Str::random(16),
        ]);
        $user->assignRole('mentor');

        $data = $validated;
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('mentor-photos', 'public');
        }

        $mentor = MentorProfile::create(array_merge($data, [
            'user_id' => $user->id,
            'is_active' => true,
        ]));

        $mentor->topics()->sync($request->input('topics', []));

        return redirect()->route('admin.mentors.index')->with('success', 'Mentor berhasil ditambahkan.');
    }

    // Detail mentor lengkap beserta jadwal dan booking-nya.
    public function show(MentorProfile $mentor): View
    {
        $mentor->load(['user', 'topics', 'availabilities' => fn ($q) => $q->orderByDesc('date'), 'bookings.participant']);

        return view('admin.mentors.show', compact('mentor'));
    }

    // Aktifkan / nonaktifkan mentor dari pencarian peserta (toggle is_active).
    public function toggleActive(MentorProfile $mentor): \Illuminate\Http\RedirectResponse
    {
        $mentor->update(['is_active' => ! $mentor->is_active]);

        cache()->forget('mentors_active');

        return back()->with('success', 'Status mentor berhasil diubah.');
    }
}