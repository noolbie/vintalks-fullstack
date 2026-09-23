@extends('layouts.app')

@section('title', 'Hasil Konsultasi')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Hasil Konsultasi</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Booking {{ $result->booking->booking_code ?? '-' }} •
                    {{ ($result->booking->mentorProfile->display_name ?? 'Mentor') }} •
                    {{ $result->created_at->format('d M Y') }}
                </p>
            </div>
            @if ($result->booking)
                <a href="{{ route('participant.bookings.show', $result->booking) }}" class="text-sm font-semibold text-[#001D3D] hover:underline">Kembali ke Booking</a>
            @endif
        </div>

        <div class="mt-6 space-y-4">
            @if ($result->summary)
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-2">Ringkasan Hasil</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $result->summary }}</p>
                </div>
            @endif

            @if ($result->mentor_notes)
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-2">Catatan Mentoring</h3>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $result->mentor_notes }}</p>
                </div>
            @endif

            @if ($result->documents->count())
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-3">Dokumen Hasil ({!! $result->documents->count() !!})</h3>
                    <div class="space-y-2">
                        @foreach ($result->documents as $document)
                            <div class="flex items-center justify-between text-sm bg-white rounded-lg px-4 py-3 border border-slate-100">
                                <span>{{ $document->title ?? $document->original_name }}</span>
                                <a href="{{ $document->signed_url }}" class="font-semibold text-[#001D3D] hover:underline">Unduh</a>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500 mt-3">Tautan unduhan berlaku 30 menit.</p>
                </div>
            @endif
        </div>
    </div>
@endsection