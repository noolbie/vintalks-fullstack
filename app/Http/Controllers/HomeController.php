<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MentorProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    // Halaman utama: arahkan user yang sudah login ke dashboard sesuai rolenya, jika belum login tampilkan landing page.
    public function index(): \Illuminate\Http\RedirectResponse|View
    {
        if (Auth::check()) {
            $user = Auth::user();

            return match (true) {
                $user->isAdmin() => redirect()->route('admin.dashboard'),
                $user->isMentor() => redirect()->route('mentor.dashboard'),
                default => redirect()->route('participant.dashboard'),
            };
        }

        return view('landing.landing', [
            'mentors' => MentorProfile::query()->with('topics', 'user')->where('is_active', true)->get(),
            'recentBookings' => Booking::query()->count(),
        ]);
    }
}