@extends('layouts.app')

@section('title', 'Profil Mentor')

@section('content')
    <div class="bg-white rounded-xl border border-slate-200 p-6 max-w-2xl">
        <div class="flex items-center gap-4 pb-5 border-b border-slate-100">
            @if ($mentor->photo_url && file_exists(public_path('storage/'.$mentor->photo)))
                <img src="{{ $mentor->photo_url }}" class="h-16 w-16 rounded-xl object-cover">
            @else
                <div class="h-16 w-16 rounded-xl bg-[#001D3D] text-white flex items-center justify-center text-xl font-bold">{{ substr($mentor->display_name, 0, 1) }}</div>
            @endif
            <div>
                <h1 class="text-lg font-bold text-slate-800">{{ $mentor->display_name }}</h1>
                <p class="text-sm text-slate-500">{{ $mentor->expertise }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('mentor.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="display_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Tampilan *</label>
                    <input type="text" name="display_name" id="display_name" required value="{{ old('display_name', $mentor->display_name) }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                </div>
                <div>
                    <label for="expertise" class="block text-sm font-medium text-slate-700 mb-1">Bidang Keahlian *</label>
                    <input type="text" name="expertise" id="expertise" required value="{{ old('expertise', $mentor->expertise) }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                </div>
            </div>

            <div>
                <label for="experience" class="block text-sm font-medium text-slate-700 mb-1">Pengalaman / Kualifikasi</label>
                <textarea name="experience" id="experience" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">{{ old('experience', $mentor->experience) }}</textarea>
            </div>

            <div>
                <label for="bio" class="block text-sm font-medium text-slate-700 mb-1">Bio *</label>
                <textarea name="bio" id="bio" rows="4" required class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">{{ old('bio', $mentor->bio) }}</textarea>
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-slate-700 mb-1">Harga per Sesi (Rp) *</label>
                <input type="number" name="price" id="price" required min="0" step="1000" value="{{ old('price', $mentor->price) }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Topik Keahlian</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ($topics as $topic)
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 border border-slate-200 rounded-lg px-3 py-2 cursor-pointer hover:border-[#001D3D]">
                            <input type="checkbox" name="topics[]" value="{{ $topic->id }}"
                                   @checked($mentor->topics->contains($topic->id)) class="rounded">
                            {{ $topic->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="photo" class="block text-sm font-medium text-slate-700 mb-1">Foto Profil</label>
                <input type="file" name="photo" id="photo" accept="image/*" class="w-full text-sm">
            </div>

            <button type="submit" class="bg-[#001D3D] hover:opacity-90 text-white font-semibold px-6 py-3 rounded-lg text-sm">Simpan Profil</button>
        </form>
    </div>
@endsection