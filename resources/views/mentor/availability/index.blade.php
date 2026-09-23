{{-- Halaman jadwal ketersediaan mentor: tambah slot kosong per bulan dan lihat slot yang sudah ter-book --}}
@extends('layouts.app')

@section('title', 'Jadwal Ketersediaan')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Tambah Slot Ketersediaan</h3>
                <form method="POST" action="{{ route('mentor.availability.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="slots[0][date]" id="date" min="{{ now()->toDateString() }}" required
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                    </div>
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-slate-700 mb-1">Jam Mulai</label>
                        <input type="time" name="slots[0][start_time]" id="start_time" required
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-slate-700 mb-1">Jam Selesai</label>
                        <input type="time" name="slots[0][end_time]" id="end_time" required
                               class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="radio" name="slots[0][status]" value="available" checked> Tersedia
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 ml-4">
                            <input type="radio" name="slots[0][status]" value="blocked"> Tidak Tersedia
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-[#001D3D] hover:opacity-90 text-white font-semibold py-2.5 rounded-lg text-sm">Tambah Slot</button>
                </form>
                <p class="text-xs text-slate-500 mt-3">Format jam WIB. Slot yang sudah terbooking otomatis tidak bisa dipesan peserta.</p>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-800">Ketersediaan — {{ $from->format('F Y') }}</h3>
                    <form method="GET" action="{{ route('mentor.availability.index') }}" class="flex items-center gap-2">
                        <input type="month" name="month" value="{{ $from->format('Y-m') }}"
                               class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm">
                        <button class="bg-slate-800 text-white px-3 py-1.5 rounded-lg text-sm">Tampilkan</button>
                    </form>
                </div>

                @forelse ($availabilities->groupBy(fn ($a) => $a->date->toDateString()) as $dateKey => $daySlots)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                                    <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Tanggal</th>
                                    <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Jam</th>
                                    <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Status</th>
                                    <th class="text-left px-4 py-2.5 border border-slate-200 font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($daySlots as $availability)
                                    @php
                                        $booked = $bookedSlots->first(fn ($b) => $b->session_date?->isSameDay($availability->date)
                                            && $b->start_time === $availability->start_time
                                            && $b->end_time === $availability->end_time);
                                    @endphp
                                    <tr class="{{ $booked ? 'bg-rose-50' : ($loop->even ? 'bg-white' : 'bg-slate-50/50') }} {{ $booked ? 'text-rose-700' : 'text-slate-600' }}">
                                        <td class="px-4 py-2.5 border border-slate-200 font-medium">{{ $availability->date->format('D, d M Y') }}</td>
                                        <td class="px-4 py-2.5 border border-slate-200 whitespace-nowrap">
                                            {{ substr($availability->start_time, 0, 5) }} – {{ substr($availability->end_time, 0, 5) }} WIB
                                        </td>
                                        <td class="px-4 py-2.5 border border-slate-200">
                                            @if ($booked)
                                                <span class="badge badge-red">Terbooking{{ $booked->participant ? ' • '.$booked->participant->name : '' }}</span>
                                            @elseif ($availability->status === 'available')
                                                <span class="badge badge-blue">Tersedia</span>
                                            @else
                                                <span class="badge badge-gray">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 border border-slate-200">
                                            <form method="POST" action="{{ route('mentor.availability.destroy', $availability) }}"
                                                  onsubmit="return confirm('Hapus slot ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="{{ $booked ? 'text-rose-500' : 'text-slate-400' }} hover:text-rose-700 disabled:opacity-40" title="Hapus slot">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @unless ($loop->last)
                        <div class="h-3"></div>
                    @endunless
                @empty
                    <p class="text-slate-500 text-sm text-center py-8">Belum ada slot pada bulan ini. Tambahkan slot di sebelah kiri.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection