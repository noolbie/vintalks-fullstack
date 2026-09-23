@extends('layouts.app')

@section('title', $mentor->display_name)

@section('content')
    <a href="{{ route('participant.mentors.index') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke daftar mentor</a>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mt-4">
        <div class="h-56 bg-slate-100 flex items-center justify-center">
            @if ($mentor->photo_url)
                <img src="{{ $mentor->photo_url }}" alt="{{ $mentor->display_name }}" class="w-full h-full object-cover object-top">
            @else
                <i class="ri-user-star-line text-7xl text-slate-300"></i>
            @endif
        </div>
        <div class="p-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $mentor->display_name }}</h1>
                    <p class="text-slate-600 mt-1">{{ $mentor->expertise }}</p>
                    <p class="text-sm text-slate-500 mt-1">{{ $mentor->experience }}</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-[#001D3D]">{{ $mentor->price_formatted }}</p>
                    <p class="text-xs text-slate-500">per sesi</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-1.5 mt-4">
                @foreach ($mentor->topics as $topic)
                    <span class="badge badge-blue">{{ $topic->name }}</span>
                @endforeach
            </div>
            @if ($mentor->bio)
                <p class="text-slate-700 mt-4 leading-relaxed">{{ $mentor->bio }}</p>
            @endif

            <div class="mt-6">
                <p class="text-sm font-semibold text-slate-600">Jadwal Tersedia ({{ count($availableDates) }} hari):</p>
                @if (count($availableDates))
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($availableDates as $date)
                            <button type="button" class="date-pill px-3 py-1.5 rounded-lg text-sm border border-slate-300 hover:border-[#001D3D]"
                                    data-date="{{ \Illuminate\Support\Carbon::parse($date)->format('Y-m-d') }}">
                                {{ \Illuminate\Support\Carbon::parse($date)->format('d M') }}
                            </button>
                        @endforeach
                    </div>
                    <a href="{{ route('participant.bookings.create', $mentor) }}" class="inline-block mt-5 bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold px-6 py-3 rounded-lg">
                        Lanjut Booking Sesi <i class="ri-arrow-right-line"></i>
                    </a>
                @else
                    <p class="text-slate-500 text-sm mt-2">Belum ada jadwal tersedia. Silakan kembali lagi nanti.</p>
                @endif
            </div>
        </div>
    </div>

    <style>
        .date-pill { cursor: pointer; }
        .date-pill.active { background: #001D3D; color: #fff; border-color: #001D3D; }
    </style>
@endsection