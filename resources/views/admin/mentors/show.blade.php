@extends('layouts.app')

@section('title', 'Detail Mentor')

@section('content')
    <a href="{{ route('admin.mentors.index') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke daftar mentor</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                @if ($mentor->photo_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($mentor->photo))
                    <img src="{{ $mentor->photo_url }}" class="h-20 w-20 rounded-xl object-cover object-top">
                @else
                    <div class="h-20 w-20 rounded-xl bg-[#001D3D] text-white flex items-center justify-center text-2xl font-bold">{{ substr($mentor->display_name, 0, 1) }}</div>
                @endif
                <div>
                    <h1 class="text-xl font-bold text-slate-800">{{ $mentor->display_name }}</h1>
                    <p class="text-sm text-slate-500">{{ $mentor->expertise }}</p>
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        @foreach ($mentor->topics as $topic)
                            <span class="badge badge-blue">{{ $topic->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="badge {{ $mentor->is_active ? 'badge-green' : 'badge-gray' }}">{{ $mentor->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                <form method="POST" action="{{ route('admin.mentors.toggle-active', $mentor) }}">
                    @csrf
                    <button class="text-sm font-semibold border border-slate-300 rounded-lg px-4 py-2 hover:border-[#001D3D]">
                        {{ $mentor->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="md:col-span-2 space-y-4">
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-2">Bio</h3>
                    <p class="text-sm text-slate-700">{{ $mentor->bio }}</p>
                </div>
                @if ($mentor->experience)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-2">Pengalaman</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $mentor->experience }}</p>
                    </div>
                @endif
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-3">Jadwal Ketersediaan</h3>
                    @forelse ($mentor->availabilities as $availability)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0 text-sm">
                            <span>{{ $availability->date->format('D, d M Y') }} — {{ substr($availability->start_time, 0, 5) }}–{{ substr($availability->end_time, 0, 5) }} WIB</span>
                            <span class="badge {{ $availability->status === 'available' ? 'badge-blue' : 'badge-gray' }}">{{ $availability->status === 'available' ? 'Tersedia' : 'Nonaktif' }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada jadwal.</p>
                    @endforelse
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-3">Ringkasan</h3>
                    <dl class="text-sm space-y-2">
                        <div class="flex justify-between"><dt class="text-slate-500">Harga</dt><dd class="font-bold text-[#001D3D]">{{ $mentor->price_formatted }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Total Booking</dt><dd class="font-medium">{{ $mentor->bookings->count() }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Akun</dt><dd class="font-medium">{{ $mentor->user?->email ?? '-' }}</dd></div>
                    </dl>
                </div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-3">Booking Terbaru</h3>
                    @forelse ($mentor->bookings->take(5) as $booking)
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0 text-sm">
                            <span class="text-slate-700">{{ $booking->booking_code }}</span>
                            <span class="badge badge-yellow">{{ $booking->booking_status_label }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada booking.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection