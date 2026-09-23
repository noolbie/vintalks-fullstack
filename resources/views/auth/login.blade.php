{{-- Halaman form login untuk masuk ke aplikasi (peserta/mentor/admin) --}}
@extends('layouts.auth')

@section('title', 'Masuk')
@section('heading', 'Selamat Datang Kembali')
@section('subheading', 'Masuk untuk melanjutkan perjalanan karier global Anda')

@section('content')
    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" id="email" required autofocus
                   value="{{ old('email') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
        </div>
        <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center gap-2 text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Ingat saya
            </label>
        </div>
        <button type="submit"
                class="w-full bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold py-2.5 rounded-lg transition">
            Masuk
        </button>
    </form>
    <p class="text-center text-sm text-slate-600 mt-6">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-[#001D3D] hover:underline">Daftar sekarang</a>
    </p>
@endsection