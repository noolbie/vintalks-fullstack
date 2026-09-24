<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MentorProfile;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    // Halaman utama: arahkan user yang sudah login ke dashboard sesuai rolenya, jika belum login tampilkan landing page.
    public function index(): RedirectResponse|View
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
            'packages' => Package::query()->where('is_active', true)->orderBy('price')->get(),
        ]);
    }

    // Peserta memilih paket dari landing page: simpan pilihan di session agar otomatis terpilih di form booking.
    public function selectPackage(Package $package): RedirectResponse
    {
        if (! $package->is_active) {
            abort(404);
        }

        session(['selected_package' => $package->slug]);

        if (Auth::check()) {
            if (Auth::user()->isParticipant()) {
                return redirect()->route('participant.mentors.index');
            }

            return redirect()->route('home');
        }

        return redirect()->route('register');
    }
}