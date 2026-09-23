{{-- Halaman detail pembayaran untuk admin: info pembayaran, bukti bayar, dan tombol verifikasi/tolak --}}
@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
    <a href="{{ route('admin.payments.index') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke daftar pembayaran</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Payment</p>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</h1>
                <p class="text-sm text-slate-500 mt-1">Booking {{ $payment->booking->booking_code ?? '-' }} • {{ $payment->payment_method_label }} • {{ $payment->created_at->format('d M Y H:i') }} WIB</p>
            </div>
            <span class="badge {{ $payment->status === 'verified' ? 'badge-green' : ($payment->status === 'rejected' ? 'badge-red' : ($payment->status === 'waiting_verification' ? 'badge-yellow' : 'badge-gray')) }} text-sm px-4 py-1.5">
                {{ \App\Enums\PaymentStatus::from($payment->status)->label() }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="lg:col-span-2 space-y-4">
                @if ($payment->proofDocument)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Bukti Pembayaran</h3>
                        <img src="{{ $payment->proofDocument->mime_type !== 'application/pdf' ? $payment->proofDocument->signed_url : asset('assets/logovintalksround.png') }}"
                             alt="Bukti" class="max-h-80 rounded-lg border border-slate-200 object-contain bg-white mb-3" onerror="this.style.display='none'">
                        <p class="text-sm text-slate-600">{{ $payment->proofDocument->original_name }}</p>
                        <a href="{{ $payment->proofDocument->signed_url }}" class="inline-block mt-2 bg-[#001D3D] text-white px-5 py-2 rounded-lg text-sm font-semibold">Unduh Bukti</a>
                    </div>
                @else
                    <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-500">Belum ada bukti pembayaran.</div>
                @endif

                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-3">Detail Booking</h3>
                    <dl class="text-sm space-y-2">
                        <div class="flex justify-between"><dt class="text-slate-500">Kode</dt><dd class="font-medium"><a href="{{ route('admin.bookings.show', $payment->booking) }}" class="text-[#001D3D] underline">{{ $payment->booking->booking_code ?? '-' }}</a></dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Peserta</dt><dd class="font-medium">{{ $payment->booking->participant->name ?? '-' }} ({{ $payment->booking->participant->email ?? '-' }})</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Mentor</dt><dd class="font-medium">{{ $payment->booking->mentorProfile->display_name ?? '-' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Jadwal</dt><dd class="font-medium">{{ $payment->booking->session_date->format('d M Y') }} {{ $payment->booking->start_time_label }} WIB</dd></div>
                    </dl>
                </div>

                @if ($payment->rejection_reason)
                    <div class="bg-rose-50 border border-rose-100 rounded-xl p-4">
                        <p class="text-sm font-semibold text-rose-700">Alasan penolakan</p>
                        <p class="text-sm text-rose-600 mt-1">{{ $payment->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                @if ($payment->status === 'waiting_verification')
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Tindakan</h3>
                        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" onsubmit="return confirm('Verifikasi pembayaran & konfirmasi booking?')">
                            @csrf
                            <button class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-lg text-sm mb-3">Verifikasi Pembayaran</button>
                        </form>
                        <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                            @csrf
                            <textarea name="rejection_reason" rows="3" required placeholder="Alasan penolakan…"
                                      class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm mb-2"></textarea>
                            <button class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2.5 rounded-lg text-sm" onclick="return confirm('Tolak pembayaran ini?')">Tolak Pembayaran</button>
                        </form>
                    </div>
                @else
                    <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-500">
                        @if ($payment->status === 'verified')
                            <p class="text-emerald-700 font-medium">Pembayaran diverifikasi pada {{ $payment->verified_at?->format('d M Y H:i') }} WIB.</p>
                        @elseif ($payment->status === 'rejected')
                            <p class="text-rose-700 font-medium">Pembayaran ditolak. Peserta dapat mengunggah ulang bukti.</p>
                        @else
                            <p>Pembayaran belum diajukan atau belum dalam tahap verifikasi.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection