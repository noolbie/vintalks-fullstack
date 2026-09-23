<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationResult;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationResultController extends Controller
{
    // ==== Hasil konsultasi ====

    // Daftar hasil konsultasi dengan pencarian kode booking.
    public function index(Request $request): View
    {
        $results = ConsultationResult::with(['booking', 'participant', 'mentor'])
            ->when($request->filled('q'), fn ($q) => $q->whereHas('booking', fn ($b) => $b->where('booking_code', 'like', '%'.$request->string('q').'%')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.consultation-results.index', compact('results'));
    }

    // Detail hasil konsultasi beserta dokumen lampirannya.
    public function show(ConsultationResult $result): View
    {
        $result->load(['booking', 'participant.participantProfile', 'mentor.mentorProfile', 'documents']);

        return view('admin.consultation-results.show', compact('result'));
    }
}