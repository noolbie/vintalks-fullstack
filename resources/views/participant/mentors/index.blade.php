@extends('layouts.app')

@section('title', 'Cari Mentor')

@section('content')
    <form method="GET" action="{{ route('participant.mentors.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau bidang keahlian…"
               class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
        <select name="topic" class="rounded-lg border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
            <option value="">Semua Topik</option>
            @foreach ($topics as $topic)
                <option value="{{ $topic->id }}" @selected(request('topic') == $topic->id)>{{ $topic->name }}</option>
            @endforeach
        </select>
        <button class="bg-[#001D3D] text-white px-6 py-2 rounded-lg text-sm font-semibold hover:opacity-90">Cari</button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($mentors as $mentor)
            <a href="{{ route('participant.mentors.show', $mentor) }}" class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition group">
                <div class="h-48 bg-slate-100 flex items-center justify-center overflow-hidden">
                    @if ($mentor->photo_url)
                        <img src="{{ $mentor->photo_url }}" alt="{{ $mentor->display_name }}" class="w-full h-full object-cover">
                    @else
                        <i class="ri-user-star-line text-5xl text-slate-300"></i>
                    @endif
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 group-hover:text-[#001D3D]">{{ $mentor->display_name }}</h3>
                    <p class="text-sm text-slate-600 mt-1 line-clamp-2">{{ $mentor->expertise }}</p>
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        @foreach ($mentor->topics as $topic)
                            <span class="badge badge-blue">{{ $topic->name }}</span>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <span class="font-bold text-[#001D3D]">{{ $mentor->price_formatted }}</span>
                        <span class="text-sm font-semibold text-[#FFC300]">Booking <i class="ri-arrow-right-line"></i></span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-10 text-slate-500">Mentor tidak ditemukan.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $mentors->links() }}</div>
@endsection