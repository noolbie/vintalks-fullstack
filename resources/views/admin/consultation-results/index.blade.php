@extends('layouts.app')

@section('title', 'Hasil Konsultasi')

@section('content')
    <form method="GET" action="{{ route('admin.consultation-results.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode booking…"
               class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:ring-2 focus:ring-[#FFC300]">
        <button class="bg-[#001D3D] text-white px-6 py-2 rounded-lg text-sm font-semibold">Cari</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="text-left px-5 py-3">Booking</th>
                    <th class="text-left px-5 py-3">Peserta</th>
                    <th class="text-left px-5 py-3">Mentor</th>
                    <th class="text-left px-5 py-3">Dibuat</th>
                    <th class="text-left px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($results as $result)
                    <tr>
                        <td class="px-5 py-3 font-semibold text-[#001D3D]">{{ $result->booking->booking_code ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $result->participant->name ?? '-' }}</td>
                        <td class="px-5 py-3">{{ $result->mentor->display_name ?? '-' }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ $result->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.consultation-results.show', $result) }}" class="text-[#001D3D] font-semibold hover:underline">Lihat</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada hasil konsultasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $results->links() }}</div>
@endsection