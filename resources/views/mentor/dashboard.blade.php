@extends('layouts.app')

@section('title', 'Dashboard Mentor')

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Konsultasi Hari Ini</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $todaySessions->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Sesi Mendatang</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $upcomingSessions->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Sesi Selesai</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $completedSessions }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Peserta</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $totalParticipants }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-5">
            <p class="text-sm text-amber-100 font-medium">Pendapatan</p>
            <p class="text-2xl font-bold text-white mt-1">Rp{{ number_format($totalEarnings, 0, ',', '.') }}</p>
        </div>
    </div>

    @if ($pendingResults > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 mb-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-amber-700"><strong>{{ $pendingResults }}</strong> sesi selesai belum memiliki hasil konsultasi.</p>
            <a href="{{ route('mentor.bookings.index') }}" class="text-sm font-semibold text-amber-800 underline">Lihat booking</a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Konsultasi Hari Ini</h3>
            @forelse ($todaySessions as $booking)
                <a href="{{ route('mentor.bookings.show', $booking) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded-lg px-2">
                    <div>
                        <p class="font-medium text-slate-700">{{ $booking->participant->name }}</p>
                        <p class="text-xs text-slate-500">{{ $booking->start_time_label }}–{{ $booking->end_time_label }} WIB • {{ $booking->booking_code }}</p>
                    </div>
                    <span class="text-[#001D3D] font-semibold text-sm">Detail <i class="ri-arrow-right-line"></i></span>
                </a>
            @empty
                <p class="text-slate-500 text-sm">Tidak ada sesi hari ini.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Sesi Mendatang</h3>
            @forelse ($upcomingSessions as $booking)
                <a href="{{ route('mentor.bookings.show', $booking) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded-lg px-2">
                    <div>
                        <p class="font-medium text-slate-700">{{ $booking->participant->name }}</p>
                        <p class="text-xs text-slate-500">{{ $booking->session_date->format('D, d M Y') }} • {{ $booking->start_time_label }} WIB</p>
                    </div>
                    <span class="badge badge-green">{{ $booking->booking_status_label }}</span>
                </a>
            @empty
                <p class="text-slate-500 text-sm">Tidak ada sesi mendatang.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-800">Booking Terbaru</h3>
            <a href="{{ route('mentor.bookings.index') }}" class="text-sm text-[#001D3D] font-medium hover:underline">Semua</a>
        </div>
        <div class="space-y-1">
            @forelse ($recentBookings as $booking)
                <a href="{{ route('mentor.bookings.show', $booking) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded-lg px-2">
                    <div>
                        <p class="font-medium text-slate-700">{{ $booking->participant->name }}</p>
                        <p class="text-xs text-slate-500">{{ $booking->session_date->format('d M Y') }} • {{ $booking->start_time_label }} WIB</p>
                    </div>
                    <span class="badge {{ $booking->booking_status === 'confirmed' ? 'badge-green' : 'badge-yellow' }}">{{ $booking->booking_status_label }}</span>
                </a>
            @empty
                <p class="text-slate-500 text-sm">Belum ada booking.</p>
            @endforelse
        </div>
    </div>
@endsection