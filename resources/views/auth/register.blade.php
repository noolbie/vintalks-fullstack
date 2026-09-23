@extends('layouts.auth')

@section('title', 'Daftar')
@section('heading', 'Buat Akun Peserta')
@section('subheading', 'Gratis — mulai jelajahi mentor dan booking sesi konsultasi')

@section('content')
    <form method="POST" action="{{ route('register.attempt') }}" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
        </div>
        <div>
            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">No. WhatsApp</label>
            <input type="text" name="phone" id="phone" required value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Ulangi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
            </div>
        </div>
        <button type="submit"
                class="w-full bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold py-2.5 rounded-lg transition">
            Daftar
        </button>
    </form>
    <p class="text-center text-sm text-slate-600 mt-6">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-[#001D3D] hover:underline">Masuk</a>
    </p>
@endsection