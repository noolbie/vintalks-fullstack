<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\MentorProfile;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MentorController extends Controller
{
    // ==== Jelajah mentor ====

    // Daftar mentor aktif dengan filter pencarian & topik.
    public function index(Request $request): View
    {
        $mentors = MentorProfile::query()
            ->with('topics', 'user')
            ->where('is_active', true)
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('display_name', 'like', '%'.$request->string('q').'%')
                    ->orWhere('expertise', 'like', '%'.$request->string('q').'%');
            }))
            ->when($request->filled('topic'), fn ($q) => $q->whereHas('topics', fn ($t) => $t->where('topics.id', $request->integer('topic'))))
            ->orderBy('display_name')
            ->paginate(9)
            ->withQueryString();

        return view('participant.mentors.index', [
            'mentors' => $mentors,
            'topics' => \App\Models\Topic::orderBy('name')->get(),
        ]);
    }

    // Detail profil mentor beserta tanggal yang masih tersedia untuk di-book.
    public function show(MentorProfile $mentor, AvailabilityService $availabilityService): View
    {
        abort_if(! $mentor->is_active, 404);

        $availableDates = $availabilityService->availableDates($mentor);

        return view('participant.mentors.show', [
            'mentor' => $mentor->load('topics', 'user'),
            'availableDates' => $availableDates,
        ]);
    }

    // Ambil slot yang masih kosong pada tanggal tertentu (endpoint AJAX untuk form booking).
    public function slots(MentorProfile $mentor, Request $request, AvailabilityService $availabilityService): \Illuminate\Http\JsonResponse
    {
        $date = $request->query('date');

        if (! $date) {
            return response()->json(['success' => false, 'message' => 'Tanggal wajib diisi.'], 422);
        }

        try {
            $date = \Carbon\Carbon::parse($date);
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Format tanggal tidak valid.'], 422);
        }

        if ($date->lessThan(today())) {
            return response()->json(['success' => false, 'message' => 'Tanggal sudah lewat.'], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $availabilityService->slotsForDate($mentor, $date),
        ]);
    }
}