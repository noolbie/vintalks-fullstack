@extends('layouts.app')

@section('title', 'Tambah Mentor')

@section('content')
    <a href="{{ route('admin.mentors.index') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6 max-w-2xl">
        <h1 class="text-xl font-bold text-slate-800 mb-6">Tambah Mentor Baru</h1>

        <form method="POST" action="{{ route('admin.mentors.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="display_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Tampilan *</label>
                    <input type="text" name="display_name" id="display_name" required value="{{ old('display_name') }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                </div>
                <div>
                    <label for="expertise" class="block text-sm font-medium text-slate-700 mb-1">Bidang Keahlian *</label>
                    <input type="text" name="expertise" id="expertise" required value="{{ old('expertise') }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email *</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password (opsional)</label>
                    <input type="text" name="password" id="password" placeholder="Kosongkan = random"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                </div>
            </div>

            <div>
                <label for="experience" class="block text-sm font-medium text-slate-700 mb-1">Pengalaman / Kualifikasi</label>
                <textarea name="experience" id="experience" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">{{ old('experience') }}</textarea>
            </div>

            <div>
                <label for="bio" class="block text-sm font-medium text-slate-700 mb-1">Bio *</label>
                <textarea name="bio" id="bio" rows="4" required class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">{{ old('bio') }}</textarea>
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Harga per Sesi (Rp) *</label>
                <input type="number" name="price" id="price" required min="0" step="1000" value="{{ old('price') }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Topik Keahlian</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ($topics as $topic)
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 border border-slate-200 rounded-lg px-3 py-2 cursor-pointer">
                            <input type="checkbox" name="topics[]" value="{{ $topic->id }}" class="rounded">
                            {{ $topic->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="photo" class="block text-sm font-medium text-slate-700 mb-1">Foto Profil</label>
                <input type="file" name="photo" id="photo" accept="image/*" class="w-full text-sm">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('admin.mentors.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium">Batal</a>
                <button type="submit" class="bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold px-8 py-2.5 rounded-lg text-sm">Simpan Mentor</button>
            </div>
        </form>
    </div>
@endsection