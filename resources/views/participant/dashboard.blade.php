@extends('layouts.app')

@section('title', 'Dashboard Peserta')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Booking Aktif</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $activeBookings->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Sesi Selesai</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $history->where('booking_status', 'completed')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Hasil Konsultasi</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $consultationResults->count() }}</p>
        </div>
    </div>

    @if ($upcomingSession)
        <div class="bg-[#FFC300] rounded-xl p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#001D3D]/70">Sesi Berikutnya — {{ $upcomingSession->session_date->format('d M Y') }} • {{ $upcomingSession->start_time_label }}–{{ $upcomingSession->end_time_label }} WIB</p>
                    <h2 class="text-lg font-bold text-[#001D3D] mt-1">Konsultasi dengan {{ $upcomingSession->mentorProfile->display_name ?? 'Mentor' }}</h2>
                    <p class="text-sm text-[#001D3D]/70 mt-1">Kode: {{ $upcomingSession->booking_code }}</p>
                </div>
                <a href="{{ route('participant.bookings.show', $upcomingSession) }}" class="bg-[#001D3D] text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:opacity-90 transition">Lihat Detail</a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl border border-dashed border-slate-300 p-8 text-center mb-6">
            <p class="text-slate-500">Belum ada sesi mendatang.</p>
            <a href="{{ route('participant.mentors.index') }}" class="inline-block mt-3 bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold px-5 py-2.5 rounded-lg text-sm">Cari Mentor</a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Booking Aktif</h3>
                <a href="{{ route('participant.bookings.history') }}" class="text-sm text-[#001D3D] font-medium hover:underline">Semua</a>
            </div>
            @forelse ($activeBookings as $booking)
                <a href="{{ route('participant.bookings.show', $booking) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded-lg px-2">
                    <div>
                        <p class="font-medium text-slate-700">{{ $booking->mentorProfile->display_name ?? 'Mentor' }}</p>
                        <p class="text-xs text-slate-500">{{ $booking->session_date->format('d M Y') }} • {{ $booking->start_time_label }} WIB</p>
                    </div>
                    <span class="badge {{ $booking->booking_status === 'confirmed' ? 'badge-green' : 'badge-yellow' }}">{{ $booking->booking_status_label }}</span>
                </a>
            @empty
                <p class="text-slate-500 text-sm">Tidak ada booking aktif.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Hasil Konsultasi Terbaru</h3>
            @forelse ($consultationResults as $result)
                <a href="{{ route('participant.consultation-results.show', $result) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded-lg px-2">
                    <div>
                        <p class="font-medium text-slate-700">Hasil {{ $result->booking->booking_code ?? '' }}</p>
                        <p class="text-xs text-slate-500">{{ $result->created_at->format('d M Y') }}</p>
                    </div>
                    <span class="text-[#001D3D] font-medium text-sm">Lihat <i class="ri-arrow-right-line"></i></span>
                </a>
            @empty
                <p class="text-slate-500 text-sm">Belum ada hasil konsultasi.</p>
            @endforelse
        </div>
    </div>
@endsection