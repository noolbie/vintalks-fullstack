@extends('layouts.app')

@section('title', 'Detail Hasil Konsultasi')

@section('content')
    <a href="{{ route('admin.consultation-results.index') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <div class="pb-5 border-b border-slate-100">
            <h1 class="text-xl font-bold text-slate-800">Hasil Konsultasi — {{ $result->booking->booking_code ?? '-' }}</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ $result->participant->name ?? '-' }} • {{ $result->mentor->display_name ?? '-' }} • {{ $result->created_at->format('d M Y H:i') }} WIB
            </p>
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
                    <h3 class="font-semibold text-slate-700 mb-3">Dokumen Result ({!! $result->documents->count() !!})</h3>
                    <div class="space-y-2">
                        @foreach ($result->documents as $document)
                            <div class="flex items-center justify-between text-sm bg-white rounded-lg px-4 py-3 border border-slate-100">
                                <span>{{ $document->title ?? $document->original_name }}</span>
                                <a href="{{ $document->signed_url }}" class="font-semibold text-[#001D3D] hover:underline">Unduh</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection