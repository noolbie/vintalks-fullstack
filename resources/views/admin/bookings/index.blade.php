@extends('layouts.app')

@section('title', 'Booking')

@section('content')
    <form method="GET" action="{{ route('admin.bookings.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode booking…"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-[#FFC300]">
        <select name="mentor" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <option value="">Semua Mentor</option>
            @foreach ($mentors as $mentorUser)
                <option value="{{ $mentorUser->id }}" @selected(request('mentor') == $mentorUser->id)>{{ $mentorUser->mentorProfile->display_name ?? $mentorUser->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <option value="">Semua Status</option>
            @foreach (\App\Enums\BookingStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <select name="payment_status" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <option value="">Semua Pembayaran</option>
            @foreach (\App\Enums\PaymentStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('payment_status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button class="bg-[#001D3D] text-white px-6 py-2 rounded-lg text-sm font-semibold">Filter</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="text-left px-5 py-3">Kode</th>
                    <th class="text-left px-5 py-3">Peserta</th>
                    <th class="text-left px-5 py-3">Mentor</th>
                    <th class="text-left px-5 py-3">Jadwal</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-left px-5 py-3">Pembayaran</th>
                    <th class="text-left px-5 py-3">Harga</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-slate-50 cursor-pointer" onclick="window.location='{{ route('admin.bookings.show', $booking) }}'">
                        <td class="px-5 py-3 font-semibold text-[#001D3D]">{{ $booking->booking_code }}</td>
                        <td class="px-5 py-3">{{ $booking->participant->name }}</td>
                        <td class="px-5 py-3">{{ $booking->mentorProfile->display_name ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $booking->session_date->format('d M Y') }}<br><span class="text-xs">{{ $booking->start_time_label }} WIB</span></td>
                        <td class="px-5 py-3"><span class="badge {{ $booking->booking_status === 'confirmed' ? 'badge-green' : ($booking->booking_status === 'completed' ? 'badge-blue' : ($booking->booking_status === 'cancelled' || $booking->booking_status === 'rejected' ? 'badge-red' : 'badge-yellow')) }}">{{ $booking->booking_status_label }}</span></td>
                        <td class="px-5 py-3"><span class="badge {{ $booking->payment_status === 'verified' ? 'badge-green' : ($booking->payment_status === 'rejected' ? 'badge-red' : 'badge-yellow') }}">{{ $booking->payment_status_label }}</span></td>
                        <td class="px-5 py-3 font-semibold">{{ $booking->price_formatted }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-slate-500">Tidak ada booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $bookings->links() }}</div>
@endsection