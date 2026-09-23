{{-- Halaman pendapatan mentor: ringkasan total diterima, sesi, potongan, dan rincian per sesi selesai --}}
@extends('layouts.app')

@section('title', 'Pendapatan')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5">
            <p class="text-sm text-emerald-100 font-medium">Total Diterima</p>
            <p class="text-2xl font-bold text-white mt-1">Rp{{ number_format($totalReceived, 0, ',', '.') }}</p>
            <p class="text-xs text-emerald-100 mt-1">Pendapatan bersih yang sudah dibayarkan admin</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Menunggu Pembayaran</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">Rp{{ number_format($totalPending, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">Belum dibayarkan oleh admin</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Jumlah Sesi Selesai</p>
            <p class="text-2xl font-bold text-[#001D3D] mt-1">{{ $totalSessions }}</p>
            <p class="text-xs text-slate-400 mt-1">Sesi mentoring selesai</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Total Fee Terpotong</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">Rp{{ number_format($totalFee, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1">Potongan platform {{ $commissionRate }}% per sesi</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Rincian Pendapatan</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Kode Booking</th>
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Peserta</th>
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Tanggal Sesi</th>
                        <th class="text-right px-4 py-2.5 border border-slate-200 font-semibold">Harga</th>
                        <th class="text-right px-4 py-2.5 border border-slate-200 font-semibold">Komisi ({{ $commissionRate }}%)</th>
                        <th class="text-right px-4 py-2.5 border border-slate-200 font-semibold">Diterima Mentor</th>
                        <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $booking)
                        <tr class="{{ $booking->isPaidToMentor() ? 'bg-emerald-50/60 text-slate-600' : 'text-slate-600' }}">
                            <td class="px-4 py-3 border border-slate-200 font-medium text-slate-800">{{ $booking->booking_code }}</td>
                            <td class="px-4 py-3 border border-slate-200">{{ $booking->participant->name ?? '-' }}</td>
                            <td class="px-4 py-3 border border-slate-200">{{ $booking->session_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 border border-slate-200 text-right">{{ $booking->price_formatted }}</td>
                            <td class="px-4 py-3 border border-slate-200 text-right text-rose-600">-Rp{{ number_format($booking->commission_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border border-slate-200 text-right font-semibold text-[#001D3D]">Rp{{ number_format($booking->mentor_net, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 border border-slate-200">
                                @if ($booking->isPaidToMentor())
                                    <span class="badge badge-green">Dibayar • {{ $booking->mentor_paid_at->format('d M Y') }}</span>
                                @else
                                    <span class="badge badge-yellow">Menunggu Pembayaran</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 border border-slate-200">
                                Belum ada sesi selesai. Pendapatan muncul setelah sesi selesai dan dikonfirmasi admin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection