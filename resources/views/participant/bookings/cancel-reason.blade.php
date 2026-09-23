@extends('layouts.app')

@section('title', 'Batalkan Booking')

@section('content')
    <div class="max-w-lg mx-auto bg-white rounded-xl border border-slate-200 p-6 mt-8">
        <h1 class="text-lg font-bold text-slate-800">Batalkan Booking {{ $booking->booking_code }}</h1>
        <p class="text-sm text-slate-500 mt-2">Anda akan membatalkan sesi dengan {{ $booking->mentorProfile->display_name ?? 'Mentor' }} pada {{ $booking->session_date->format('d M Y') }} pukul {{ $booking->start_time_label }} WIB.</p>

        <form method="POST" action="{{ route('participant.bookings.cancel', $booking) }}" class="mt-5 space-y-4" onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
            @csrf
            <div>
                <label for="cancellation_reason" class="block text-sm font-medium text-slate-700 mb-1">Alasan Pembatalan (opsional)</label>
                <textarea name="cancellation_reason" id="cancellation_reason" rows="3"
                          class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]"></textarea>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('participant.bookings.show', $booking) }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium">Kembali</a>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold">Ya, Batalkan</button>
            </div>
        </form>
    </div>
@endsection