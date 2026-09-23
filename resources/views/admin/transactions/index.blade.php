{{-- Halaman transaksi mentor (admin): daftar sesi selesai, perhitungan komisi, dan aksi bayar ke mentor --}}
@extends('layouts.app')

@section('title', 'Transaksi Mentor')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <p class="text-sm text-slate-600">Sesi selesai dikalikan persentase komisi {{ $commissionRate }}% — nominal bersih yang dibayarkan ke mentor.</p>
        <form method="GET" action="{{ route('admin.transactions.index') }}" class="flex items-center gap-2">
            <select name="status" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                <option value="">Semua Status</option>
                <option value="pending" @selected(request('status') === 'pending')>Menunggu Pembayaran</option>
                <option value="paid" @selected(request('status') === 'paid')>Sudah Dibayar</option>
            </select>
            <button class="bg-slate-800 text-white px-3 py-1.5 rounded-lg text-sm">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Kode Booking</th>
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Mentor</th>
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Peserta</th>
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Tanggal</th>
                        <th class="text-right px-4 py-2.5 border border-slate-200 font-semibold">Harga</th>
                        <th class="text-right px-4 py-2.5 border border-slate-200 font-semibold">Komisi ({{ $commissionRate }}%)</th>
                        <th class="text-right px-4 py-2.5 border border-slate-200 font-semibold">Untuk Mentor</th>
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Status</th>
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $booking)
                        <tr class="{{ $booking->isPaidToMentor() ? 'bg-emerald-50/60 text-slate-600' : 'text-slate-600' }}">
                            <td class="px-4 py-3 border border-slate-200 font-medium text-slate-800">{{ $booking->booking_code }}</td>
                            <td class="px-4 py-3 border border-slate-200">{{ $booking->mentorProfile->display_name ?? '-' }}</td>
                            <td class="px-4 py-3 border border-slate-200">{{ $booking->participant->name ?? '-' }}</td>
                            <td class="px-4 py-3 border border-slate-200 whitespace-nowrap">{{ $booking->session_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 border border-slate-200 text-right">{{ $booking->price_formatted }}</td>
                            <td class="px-4 py-3 border border-slate-200 text-right text-rose-600">-Rp{{ number_format($booking->commission_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border border-slate-200 text-right font-semibold text-[#001D3D]">Rp{{ number_format($booking->mentor_net, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border border-slate-200">
                                @if ($booking->isPaidToMentor())
                                    <span class="badge badge-green">Dibayar</span>
                                @else
                                    <span class="badge badge-yellow">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 border border-slate-200">
                                @unless ($booking->isPaidToMentor())
                                    <form method="POST" action="{{ route('admin.transactions.mark-paid', $booking) }}"
                                          onsubmit="return confirm('Konfirmasi pembayaran Rp{{ number_format($booking->mentor_net, 0, ',', '.') }} ke mentor?')">
                                        @csrf
                                        <button class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                                            Tandai Dibayar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-emerald-600">{{ $booking->mentor_paid_at->format('d M Y H:i') }}</span>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-500 border border-slate-200">
                                Belum ada sesi selesai untuk dibuatkan transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection