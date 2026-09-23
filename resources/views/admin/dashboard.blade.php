@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Peserta</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $totalParticipants }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Mentor</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $totalMentors }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Total Booking</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $totalBookings }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Verifikasi Menunggu</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingVerification }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Konfirmasi Aktif</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $confirmedBookings }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Sesi Selesai</p>
            <p class="text-2xl font-bold text-sky-600 mt-1">{{ $completedSessions }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 bg-white rounded-xl border border-slate-200 p-6">
            <p class="text-sm text-slate-500">Total Pendapatan (Verified)</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">Rp{{ number_format((float) $revenue, 0, ',', '.') }}</p>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-sm text-slate-500">Sesi mendatang: <strong class="text-[#001D3D]">{{ $upcomingSessions }}</strong></p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="inline-block mt-4 text-sm font-semibold text-[#001D3D] hover:underline">Kelola pembayaran &rarr;</a>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Booking Terbaru</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm text-[#001D3D] font-medium hover:underline">Semua</a>
            </div>
            @forelse ($recentBookings as $booking)
                <a href="{{ route('admin.bookings.show', $booking) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded-lg px-2">
                    <div>
                        <p class="font-medium text-slate-700">{{ $booking->booking_code }}</p>
                        <p class="text-xs text-slate-500">{{ $booking->participant->name }} • {{ $booking->mentorProfile->display_name ?? 'Mentor' }}</p>
                    </div>
                    <span class="badge badge-yellow">{{ $booking->booking_status_label }}</span>
                </a>
            @empty
                <p class="text-slate-500 text-sm">Belum ada booking.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-800">Pembayaran Terbaru</h3>
            <a href="{{ route('admin.payments.index') }}" class="text-sm text-[#001D3D] font-medium hover:underline">Semua</a>
        </div>
        <div class="space-y-1">
            @forelse ($recentPayments as $payment)
                <a href="{{ route('admin.payments.show', $payment) }}" class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50 rounded-lg px-2">
                    <div>
                        <p class="font-medium text-slate-700">{{ $payment->booking->booking_code ?? '-' }} — {{ $payment->booking->participant->name ?? '-' }}</p>
                        <p class="text-xs text-slate-500">{{ $payment->created_at->format('d M Y H:i') }} • {{ $payment->payment_method_label }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-semibold text-[#001D3D]">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</span>
                        <span class="badge {{ $payment->status === 'verified' ? 'badge-green' : ($payment->status === 'rejected' ? 'badge-red' : 'badge-yellow') }}">{{ \App\Enums\PaymentStatus::from($payment->status)->label() }}</span>
                    </div>
                </a>
            @empty
                <p class="text-slate-500 text-sm">Belum ada pembayaran.</p>
            @endforelse
        </div>
    </div>
@endsection