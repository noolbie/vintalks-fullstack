<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\ParticipantProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    // Tampilkan halaman form login.
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    // Proses login: cek rate-limit lalu autentikasi, lalu redirect ke dashboard sesuai role.
    public function login(LoginRequest $request): RedirectResponse
    {
        $this->ensureIsNotRateLimited($request);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey($request), 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        $request->session()->regenerate();

        return redirect()->intended($this->homeRoute());
    }

    // Tampilkan halaman form registrasi.
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    // Proses registrasi: buat user (role participant) + profil peserta, lalu login otomatis.
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = \DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ]);

            $user->assignRole('participant');

            ParticipantProfile::create([
                'user_id' => $user->id,
                'phone' => $request->input('phone'),
            ]);

            return $user;
        });

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('participant.dashboard'));
    }

    // Keluar dari aplikasi dan bersihkan sesi.
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // ==== Helper internal ====

    // Tentukan halaman dashboard tujuan setelah login berdasarkan role user.
    private function homeRoute(): string
    {
        $user = Auth::user();

        return match (true) {
            $user->isAdmin() => route('admin.dashboard'),
            $user->isMentor() => route('mentor.dashboard'),
            default => route('participant.dashboard'),
        };
    }

    // Batasi percobaan login (maks 5x) agar tidak terjadi brute-force.
    private function ensureIsNotRateLimited(Request $request): void
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }
    }

    // Kunci unik rate-limiter berdasarkan IP pengunjung.
    private function throttleKey(Request $request): string
    {
        return 'login:'.$request->ip();
    }
}