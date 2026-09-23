@extends('layouts.app')

@section('title', 'Detail Peserta')

@section('content')
    <a href="{{ route('admin.participants.index') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke daftar peserta</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <div class="flex items-center gap-4">
            @if ($user->participantProfile?->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->participantProfile->profile_photo))
                <img src="{{ asset('storage/'.$user->participantProfile->profile_photo) }}" class="h-16 w-16 rounded-full object-cover">
            @else
                <div class="h-16 w-16 rounded-full bg-[#001D3D] text-white flex items-center justify-center text-xl font-bold">{{ substr($user->name, 0, 1) }}</div>
            @endif
            <div>
                <h1 class="text-xl font-bold text-slate-800">{{ $user->name }}</h1>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="bg-slate-50 rounded-xl p-4">
                <h3 class="font-semibold text-slate-700 mb-3">Data Profil</h3>
                <dl class="text-sm space-y-2">
                    <div class="flex justify-between"><dt class="text-slate-500">WhatsApp</dt><dd class="font-medium">{{ $user->participantProfile?->phone ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Jenis Kelamin</dt><dd class="font-medium">{{ ($user->participantProfile?->gender ?? null) === 'male' ? 'Laki-laki' : (($user->participantProfile?->gender ?? null) === 'female' ? 'Perempuan' : '-') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Pekerjaan</dt><dd class="font-medium">{{ $user->participantProfile?->occupation ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Institusi</dt><dd class="font-medium">{{ $user->participantProfile?->institution ?? '-' }}</dd></div>
                    @if ($user->participantProfile?->linkedin_url)
                        <div class="flex justify-between"><dt class="text-slate-500">LinkedIn</dt><dd class="font-medium"><a href="{{ $user->participantProfile->linkedin_url }}" target="_blank" rel="noopener" class="text-[#001D3D] underline">Buka</a></dd></div>
                    @endif
                </dl>
            </div>

            <div class="bg-slate-50 rounded-xl p-4">
                <h3 class="font-semibold text-slate-700 mb-3">Riwayat Booking</h3>
                @forelse ($user->participantBookings as $booking)
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0 text-sm">
                        <div>
                            <p class="font-medium text-slate-700">{{ $booking->booking_code }}</p>
                            <p class="text-xs text-slate-500">{{ $booking->mentorProfile->display_name ?? 'Mentor' }} • {{ $booking->session_date->format('d M Y') }}</p>
                        </div>
                        <span class="badge {{ $booking->booking_status === 'confirmed' ? 'badge-green' : 'badge-yellow' }}">{{ $booking->booking_status_label }}</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">Belum ada booking.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection