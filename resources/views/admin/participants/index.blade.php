@extends('layouts.app')

@section('title', 'Peserta')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <form method="GET" action="{{ route('admin.participants.index') }}" class="flex gap-3 flex-1 max-w-xl">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email…"
                   class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
            <button class="bg-[#001D3D] text-white px-6 py-2 rounded-lg text-sm font-semibold">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse ($participants as $user)
                <a href="{{ route('admin.participants.show', $user) }}" class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-lg bg-[#001D3D] text-white flex items-center justify-center font-bold text-xs">{{ substr($user->name, 0, 1) }}</div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $user->email }}{{ $user->participantProfile?->phone ? ' • '.$user->participantProfile->phone : '' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">{{ $user->participantBookings()->count() }} booking</p>
                        <p class="text-xs text-slate-400">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </a>
            @empty
                <div class="text-center py-12 text-slate-500">Tidak ada peserta.</div>
            @endforelse
        </div>
    </div>

    <div class="mt-6">{{ $participants->links() }}</div>
@endsection