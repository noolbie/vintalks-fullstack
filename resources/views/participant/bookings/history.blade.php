@extends('layouts.app')

@section('title', 'Booking Saya')

@section('content')
    <form method="GET" action="{{ route('participant.bookings.history') }}" class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode booking…"
               class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
        <select name="status" class="rounded-lg border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
            <option value="">Semua Status</option>
            @foreach (\App\Enums\BookingStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button class="bg-[#001D3D] text-white px-6 py-2 rounded-lg text-sm font-semibold">Filter</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse ($bookings as $booking)
                <a href="{{ route('participant.bookings.show', $booking) }}" class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-lg bg-[#001D3D] text-white flex items-center justify-center font-bold text-xs">{{ substr($booking->mentorProfile->display_name ?? 'M', 0, 1) }}</div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $booking->mentorProfile->display_name ?? 'Mentor' }}</p>
                            <p class="text-xs text-slate-500">{{ $booking->booking_code }} • {{ $booking->session_date->format('d M Y') }} • {{ $booking->start_time_label }} WIB</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="badge {{ $booking->booking_status === 'confirmed' ? 'badge-green' : ($booking->booking_status === 'completed' ? 'badge-blue' : ($booking->booking_status === 'cancelled' || $booking->booking_status === 'rejected' ? 'badge-red' : 'badge-yellow')) }}">{{ $booking->booking_status_label }}</span>
                        <span class="text-sm font-bold text-[#001D3D]">{{ $booking->price_formatted }}</span>
                    </div>
                </a>
            @empty
                <div class="text-center py-12 text-slate-500">
                    <p>Belum ada booking.</p>
                    <a href="{{ route('participant.mentors.index') }}" class="inline-block mt-3 bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold px-5 py-2 rounded-lg text-sm">Cari Mentor</a>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-6">{{ $bookings->links() }}</div>
@endsection