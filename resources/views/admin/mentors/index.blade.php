@extends('layouts.app')

@section('title', 'Mentor')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <form method="GET" action="{{ route('admin.mentors.index') }}" class="flex gap-3 flex-1 max-w-xl">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / keahlian…"
                   class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
            <select name="status" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="1" @selected(request('status') === '1')>Aktif</option>
                <option value="0" @selected(request('status') === '0')>Nonaktif</option>
            </select>
            <button class="bg-[#001D3D] text-white px-6 py-2 rounded-lg text-sm font-semibold">Filter</button>
        </form>
        <a href="{{ route('admin.mentors.create') }}" class="bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold px-5 py-2 rounded-lg text-sm">+ Tambah Mentor</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($mentors as $mentor)
            <a href="{{ route('admin.mentors.show', $mentor) }}" class="bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition">
                <div class="h-40 bg-slate-100 flex items-center justify-center overflow-hidden">
                    @if ($mentor->photo_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($mentor->photo))
                        <img src="{{ $mentor->photo_url }}" class="w-full h-full object-cover object-top" alt="{{ $mentor->display_name }}">
                    @else
                        <i class="ri-user-star-line text-5xl text-slate-300"></i>
                    @endif
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-slate-800">{{ $mentor->display_name }}</h3>
                        <span class="badge {{ $mentor->is_active ? 'badge-green' : 'badge-gray' }}">{{ $mentor->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <p class="text-sm text-slate-600 mt-1">{{ $mentor->expertise }}</p>
                    <div class="flex items-center justify-between mt-3">
                        <span class="font-bold text-[#001D3D]">{{ $mentor->price_formatted }}</span>
                        <span class="text-xs text-slate-500">{{ $mentor->bookings()->count() }} booking</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-10 text-slate-500">Tidak ada mentor.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $mentors->links() }}</div>
@endsection