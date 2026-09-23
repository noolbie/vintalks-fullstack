{{-- Halaman daftar pembayaran untuk admin: tabel lengkap dengan filter status & pencarian kode booking --}}
@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <form method="GET" action="{{ route('admin.payments.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode booking…"
               class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-[#FFC300]">
        <select name="status" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <option value="">Semua Status</option>
            @foreach (\App\Enums\PaymentStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button class="bg-[#001D3D] text-white px-6 py-2 rounded-lg text-sm font-semibold">Filter</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="text-left px-5 py-3">Booking</th>
                    <th class="text-left px-5 py-3">Peserta</th>
                    <th class="text-left px-5 py-3">Metode</th>
                    <th class="text-left px-5 py-3">Jumlah</th>
                    <th class="text-left px-5 py-3">Diajukan</th>
                    <th class="text-left px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-slate-50 cursor-pointer" onclick="window.location='{{ route('admin.payments.show', $payment) }}'">
                        <td class="px-5 py-3 font-semibold text-[#001D3D]">{{ $payment->booking->booking_code ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $payment->booking->participant->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $payment->payment_method_label }}</td>
                        <td class="px-5 py-3 font-semibold">Rp{{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $payment->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3">
                            <span class="badge {{ $payment->status === 'verified' ? 'badge-green' : ($payment->status === 'rejected' ? 'badge-red' : ($payment->status === 'waiting_verification' ? 'badge-yellow' : 'badge-gray')) }}">
                                {{ \App\Enums\PaymentStatus::from($payment->status)->label() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">Tidak ada pembayaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $payments->links() }}</div>
@endsection