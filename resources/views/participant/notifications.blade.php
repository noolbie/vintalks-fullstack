@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        @forelse ($notifications as $notification)
            <div class="px-5 py-4 border-b border-slate-100 flex items-start justify-between gap-4 {{ $notification->read_at ? 'opacity-60' : '' }}">
                <div>
                    <p class="font-medium text-slate-800">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                    <p class="text-sm text-slate-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
                @if (! $notification->read_at)
                    <form method="POST" action="{{ route('participant.notifications.read', $notification->id) }}">
                        @csrf
                        <button class="text-xs font-semibold text-[#001D3D] hover:underline">Tandai dibaca</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="text-center py-12 text-slate-500">Tidak ada notifikasi.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $notifications->links() }}</div>
@endsection