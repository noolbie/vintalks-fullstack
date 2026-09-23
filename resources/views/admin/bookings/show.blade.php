@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
    <a href="{{ route('admin.bookings.index') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke daftar booking</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Booking Code</p>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">{{ $booking->booking_code }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $booking->participant->name }} with {{ $booking->mentorProfile->display_name ?? 'Mentor' }} • {{ $booking->session_date->format('D, d M Y') }} • {{ $booking->start_time_label }}–{{ $booking->end_time_label }} WIB
                </p>
            </div>
            <div class="text-right">
                <span class="badge {{ $booking->booking_status === 'confirmed' ? 'badge-green' : ($booking->booking_status === 'completed' ? 'badge-blue' : ($booking->booking_status === 'cancelled' || $booking->booking_status === 'rejected' ? 'badge-red' : 'badge-yellow')) }}">{{ $booking->booking_status_label }}</span>
                <p class="text-sm text-slate-500 mt-2">Pembayaran: <span class="badge {{ $booking->payment_status === 'verified' ? 'badge-green' : ($booking->payment_status === 'rejected' ? 'badge-red' : 'badge-yellow') }}">{{ $booking->payment_status_label }}</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="space-y-4">
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-3">Detail Sesi</h3>
                    <dl class="text-sm space-y-2">
                        <div class="flex justify-between"><dt class="text-slate-500">Mentor</dt><dd class="font-medium">{{ $booking->mentorProfile->display_name ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Peserta</dt><dd class="font-medium">{{ $booking->participant->name }} ({{ $booking->participant->email }})</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Topik</dt><dd class="font-medium">{{ $booking->topic->name ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Harga</dt><dd class="font-bold text-[#001D3D]">{{ $booking->price_formatted }}</dd></div>
                        @if ($booking->requirement?->linkedin_url)
                            <div class="flex justify-between"><dt class="text-slate-500">LinkedIn Peserta</dt><dd class="font-medium"><a href="{{ $booking->requirement->linkedin_url }}" target="_blank" rel="noopener" class="text-[#001D3D] underline">Buka</a></dd></div>
                        @endif
                    </dl>
                </div>

                @if ($booking->documents->count())
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Dokumen Peserta</h3>
                        <div class="space-y-2">
                            @foreach ($booking->documents as $document)
                                <div class="flex items-center justify-between text-sm bg-white rounded-lg px-4 py-3 border border-slate-100">
                                    <span>{{ $document->original_name }}</span>
                                    <a href="{{ $document->signed_url }}" class="font-semibold text-[#001D3D] hover:underline">Unduh</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($booking->consultationResult)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-2">Hasil Konsultasi</h3>
                        <a href="{{ route('admin.consultation-results.show', $booking->consultationResult) }}" class="font-semibold text-[#001D3D] hover:underline">Lihat Hasil &rarr;</a>
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                @if ($booking->booking_status === 'confirmed')
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Link Meeting</h3>
                        @if ($booking->meeting_url)
                            <div class="mb-3">
                                <p class="text-sm text-slate-700">Provider: {{ $booking->meeting_provider_label }}</p>
                                <a href="{{ $booking->meeting_url }}" target="_blank" rel="noopener" class="inline-block mt-1 text-[#001D3D] underline text-sm">Buka link</a>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('admin.bookings.meeting', $booking) }}" class="space-y-3">
                            @csrf
                            <select name="meeting_provider" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" required>
                                <option value="google_meet" @selected(($booking->meeting_provider ?? '') === 'google_meet')>Google Meet</option>
                                <option value="zoom" @selected(($booking->meeting_provider ?? '') === 'zoom')>Zoom</option>
                                <option value="other" @selected(($booking->meeting_provider ?? '') === 'other')>Lainnya</option>
                            </select>
                            <input type="url" name="meeting_url" value="{{ $booking->meeting_url ?? '' }}" placeholder="https://meet.google.com/…"
                                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" required>
                            <button class="w-full bg-[#001D3D] hover:opacity-90 text-white font-semibold py-2.5 rounded-lg text-sm">
                                {{ $booking->meeting_url ? 'Perbarui Link Meeting' : 'Simpan Link Meeting' }}
                            </button>
                        </form>
                        <p class="text-xs text-slate-500 mt-2">Peserta akan mendapat notifikasi saat link disimpan.</p>
                    </div>
                @endif

                @if ($booking->booking_status === 'confirmed' || $booking->booking_status === 'completed' || $booking->booking_status === 'cancelled' || $booking->booking_status === 'rejected')
                    <div class="bg-rose-50 border border-rose-100 rounded-xl p-4">
                        <h3 class="font-semibold text-rose-700 mb-2">Pembatalan</h3>
                        @if ($booking->cancellation_reason)
                            <p class="text-sm text-rose-600 mb-3">Alasan: {{ $booking->cancellation_reason }}</p>
                        @endif
                        @if ($booking->booking_status === 'confirmed' || $booking->booking_status === 'completed')
                            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}">
                                @csrf
                                <input type="text" name="cancellation_reason" placeholder="Alasan pembatalan…" required
                                       class="w-full rounded-lg border border-rose-200 px-4 py-2 text-sm mb-2">
                                <button class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2 rounded-lg text-sm" onclick="return confirm('Batalkan booking ini?')">Batalkan Booking</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection