{{-- Halaman detail booking peserta: ringkasan sesi, status pembayaran, syarat, dokumen, dan hasil konsultasi --}}
@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
    <a href="{{ route('participant.bookings.history') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke booking saya</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Booking Code</p>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">{{ $booking->booking_code }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $booking->mentorProfile->display_name ?? 'Mentor' }} •
                    {{ $booking->session_date->format('D, d M Y') }} • {{ $booking->start_time_label }}–{{ $booking->end_time_label }} WIB
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
                        <div class="flex justify-between"><dt class="text-slate-500">Tanggal</dt><dd class="font-medium">{{ $booking->session_date->format('d M Y') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Jam</dt><dd class="font-medium">{{ $booking->start_time_label }} – {{ $booking->end_time_label }} WIB</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Topik</dt><dd class="font-medium">{{ $booking->topic->name ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Harga</dt><dd class="font-bold text-[#001D3D]">{{ $booking->price_formatted }}</dd></div>
                    </dl>
                </div>

                @if ($booking->requirement)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Kebutuhan Konsultasi</h3>
                        <dl class="text-sm space-y-2">
                            @if ($booking->requirement->linkedin_url)
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">LinkedIn</dt><dd class="font-medium text-right"><a href="{{ $booking->requirement->linkedin_url }}" target="_blank" rel="noopener" class="text-[#001D3D] underline">Buka</a></dd></div>
                            @endif
                            @if ($booking->requirement->consultation_topic)
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Judul Topik</dt><dd class="font-medium text-right">{{ $booking->requirement->consultation_topic }}</dd></div>
                            @endif
                            @if ($booking->requirement->career_goal)
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Tujuan Karier</dt><dd class="font-medium text-right max-w-[60%]">{{ $booking->requirement->career_goal }}</dd></div>
                            @endif
                            @if ($booking->requirement->description)
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Deskripsi</dt><dd class="font-medium text-right max-w-[60%]">{{ $booking->requirement->description }}</dd></div>
                            @endif
                        </dl>
                    </div>
                @endif

                @if ($booking->documents->count())
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Dokumen Saya</h3>
                        <div class="space-y-2">
                            @foreach ($booking->documents as $document)
                                <div class="flex items-center justify-between text-sm">
                                    <span>{{ $document->original_name }}</span>
                                    <a href="{{ $document->signed_url }}" class="font-medium text-[#001D3D] hover:underline">Unduh</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                @if ($booking->booking_status === 'pending' || $booking->booking_status === 'payment_pending')
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <h3 class="font-semibold text-amber-800 mb-1">Tagihan Menunggu</h3>
                        <p class="text-sm text-amber-700">Segera lakukan pembayaran sebesar <strong>{{ $booking->price_formatted }}</strong> agar sesi Anda dikonfirmasi.</p>
                        <a href="{{ route('participant.payments.create', $booking) }}" class="inline-block mt-3 bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold px-5 py-2 rounded-lg text-sm">Bayar Sekarang</a>
                    </div>
                @elseif ($booking->booking_status === 'payment_verification')
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <h3 class="font-semibold text-yellow-800 mb-1">Pembayaran Sedang Diverifikasi</h3>
                        <p class="text-sm text-yellow-700">Bukti pembayaran Anda sedang diperiksa admin. Anda akan menerima notifikasi setelah diverifikasi.</p>
                    </div>
                @elseif ($booking->hasMeetingUrl())
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <h3 class="font-semibold text-emerald-800 mb-1">Sesi Dikonfirmasi</h3>
                        <p class="text-sm text-emerald-700">Meeting {{ $booking->meeting_provider_label }}: </p>
                        <a href="{{ $booking->meeting_url }}" target="_blank" rel="noopener" class="inline-block mt-2 bg-[#001D3D] text-white px-5 py-2 rounded-lg text-sm font-semibold">Bergabung Meeting</a>
                    </div>
                @elseif ($booking->booking_status === 'confirmed')
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <h3 class="font-semibold text-emerald-800 mb-1">Sesi Dikonfirmasi</h3>
                        <p class="text-sm text-emerald-700">Link meeting akan muncul di sini setelah admin mengunggahnya.</p>
                    </div>
                @endif

                @if ($booking->payment)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Pembayaran</h3>
                        <dl class="text-sm space-y-2">
                            <div class="flex justify-between"><dt class="text-slate-500">Metode</dt><dd class="font-medium">{{ $booking->payment->payment_method_label }}</dd></div>
                            @if ($booking->payment->payment_reference)
                                <div class="flex justify-between"><dt class="text-slate-500">Referensi</dt><dd class="font-medium">{{ $booking->payment->payment_reference }}</dd></div>
                            @endif
                            <div class="flex justify-between"><dt class="text-slate-500">Status</dt><dd class="font-medium">{{ $booking->payment_status_label }}</dd></div>
                            @if ($booking->payment->rejection_reason)
                                <div class="mt-3 text-rose-700 bg-rose-50 rounded-lg px-3 py-2"><dt class="text-xs font-semibold">Alasan ditolak:</dt><dd class="mt-1">{{ $booking->payment->rejection_reason }}</dd></div>
                            @endif
                        </dl>
                        @if ($booking->booking_status !== 'payment_verification' && $booking->booking_status !== 'confirmed' && $booking->booking_status !== 'completed')
                            <a href="{{ route('participant.payments.create', $booking) }}" class="inline-block mt-3 text-sm font-semibold text-[#001D3D] hover:underline">Unggah ulang bukti &rarr;</a>
                        @endif
                    </div>
                @endif

                @if ($booking->consultationResult)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-1">Hasil Konsultasi</h3>
                        <p class="text-sm text-slate-500 mb-3">Mentor telah menyimpan hasil & dokumen untuk sesi ini.</p>
                        <a href="{{ route('participant.consultation-results.show', $booking->consultationResult) }}" class="font-semibold text-[#001D3D] hover:underline">Lihat Hasil &rarr;</a>
                    </div>
                @endif

                @if ($booking->isActive())
                    <form method="POST" action="{{ route('participant.bookings.cancel', $booking) }}" onsubmit="return confirm('Yakin ingin membatalkan booking ini?')">
                        @csrf
                        <input type="hidden" name="cancellation_reason" value="">
                        <button type="submit" class="text-sm text-rose-600 hover:underline">Batalkan booking ini</button>
                    </form>
                @endif

                @if ($booking->cancellation_reason)
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-rose-700">Booking dibatalkan</p>
                        <p class="text-sm text-rose-600 mt-1">{{ $booking->cancellation_reason ?: 'Tanpa alasan.' }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection