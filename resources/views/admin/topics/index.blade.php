@extends('layouts.app')

@section('title', 'Topik')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6 h-fit">
            <h3 class="font-semibold text-slate-800 mb-4">Tambah Topik</h3>
            <form method="POST" action="{{ route('admin.topics.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Topik *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">{{ old('description') }}</textarea>
                </div>
                <button type="submit" class="w-full bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold py-2.5 rounded-lg text-sm">Simpan Topik</button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                    <tr>
                        <th class="text-left px-5 py-3">Nama</th>
                        <th class="text-left px-5 py-3">Deskripsi</th>
                        <th class="text-left px-5 py-3">Jml. Mentor</th>
                        <th class="text-left px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($topics as $topic)
                        <tr>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $topic->name }}</td>
                            <td class="px-5 py-3 text-slate-600 max-w-sm">{{ Str::limit($topic->description ?? '-', 60) }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $topic->mentors_count }}</td>
                            <td class="px-5 py-3">
                                <form method="POST" action="{{ route('admin.topics.destroy', $topic) }}" class="inline" onsubmit="return confirm('Hapus topik ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rose-600 hover:underline text-xs font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">Belum ada topik.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection