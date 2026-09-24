{{-- Halaman persetujuan paket untuk admin: setujui/tolak paket potongan yang dipilih peserta --}}
@extends('layouts.app')

@section('title', 'Persetujuan Paket')

@section('content')
    <form method="GET" action="{{ route('admin.package-approvals.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <select name="status" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <option value="">Menunggu Persetujuan</option>
            <option value="approved" @selected(request('status') === 'approved')>Disetujui</option>
            <option value="rejected" @selected(request('status') === 'rejected')>Ditolak</option>
        </select>
        <button class="bg-[#001D3D] text-white px-6 py-2 rounded-lg text-sm font-semibold">Filter</button>
    </form>

    <div class="space-y-4">
        @forelse ($bookings as $booking)
            <div class="bg-white rounded-xl border border-slate-200 p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-[#001D3D]">{{ $booking->booking_code }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ $booking->participant->name ?? '-' }} &rarr; {{ $booking->mentorProfile->display_name ?? '-' }}</p>
                        <p class="text-sm text-slate-500">{{ $booking->session_date->format('d M Y') }} • {{ $booking->start_time_label }} WIB</p>
                    </div>
                    <div class="text-right">
                        <span class="badge {{ $booking->isPackageApproved() ? 'badge-green' : ($booking->isPackageRejected() ? 'badge-red' : 'badge-yellow') }}">
                            {{ $booking->package_status_label }}
                        </span>
                        @if ($booking->hasPackage())
                            <span class="badge badge-gray">Harga normal: {{ $booking->package_base_price_formatted }}</span>
                            <span class="badge badge-gray">Harga sesi: {{ $booking->price_formatted }}</span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-2">{{ $booking->package?->name ?? '-' }}</h3>
                        <p class="text-sm text-slate-600">{{ $booking->package?->description }}</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-2">Yang Diberikan</h3>
                        <ul class="text-sm text-slate-600 space-y-1 list-disc list-inside">
                            @forelse ($booking->package?->benefits ?? [] as $benefit)
                                <li>{{ $benefit }}</li>
                            @empty
                                <li class="text-slate-400">Tidak ada benefit.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-2">Potongan</h3>
                        <p class="text-2xl font-bold text-emerald-700">{{ $booking->discount_amount_formatted }}</p>
                        <p class="text-xs text-slate-500 mt-1">Harga sesi setelah potongan: {{ $booking->price_formatted }}</p>
                        @if ($booking->isPackageRejected() && $booking->package_rejection_reason)
                            <p class="mt-3 text-xs text-rose-700 bg-rose-50 rounded-lg px-3 py-2">Alasan ditolak: {{ $booking->package_rejection_reason }}</p>
                        @endif
                    </div>
                </div>

                @if ($booking->isPackagePending())
                    <div class="mt-4 flex flex-col sm:flex-row gap-3 sm:items-start">
                        <form method="POST" action="{{ route('admin.package-approvals.approve', $booking) }}" class="sm:flex-none">
                            @csrf
                            <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2.5 rounded-lg text-sm"
                                    onclick="return confirm('Setujui paket ini? Diskon final & mentor bisa melihatnya.')">Setujui Paket</button>
                        </form>
                        <form method="POST" action="{{ route('admin.package-approvals.reject', $booking) }}" class="flex-1 flex gap-2">
                            @csrf
                            <input type="text" name="reason" required placeholder="Alasan penolakan…"
                                   class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                            <button class="bg-rose-600 hover:bg-rose-700 text-white font-semibold px-6 py-2.5 rounded-lg text-sm"
                                    onclick="return confirm('Tolak paket ini? Harga sesi dikembalikan ke harga normal.')">Tolak</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-500">Tidak ada pengajuan paket.</div>
        @endforelse

        <div class="mt-6">{{ $bookings->links() }}</div>
    </div>
@endsection