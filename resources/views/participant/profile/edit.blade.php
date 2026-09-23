@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="max-w-2xl bg-white rounded-xl border border-slate-200 p-6">
        <div class="flex items-center gap-4 pb-5 border-b border-slate-100">
            @if ($profile && $profile->profile_photo && Storage::disk('public')->exists($profile->profile_photo))
                <img src="{{ asset('storage/'.$profile->profile_photo) }}" class="h-16 w-16 rounded-full object-cover">
            @else
                <div class="h-16 w-16 rounded-full bg-[#001D3D] text-white flex items-center justify-center text-xl font-bold">{{ substr(auth()->user()->name, 0, 1) }}</div>
            @endif
            <div>
                <h1 class="text-lg font-bold text-slate-800">{{ auth()->user()->name }}</h1>
                <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('participant.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">No. WhatsApp *</label>
                <input type="text" name="phone" id="phone" required value="{{ old('phone', $profile->phone ?? '') }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="gender" class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                        <option value="">— Pilih —</option>
                        <option value="male" @selected(($profile->gender ?? '') === 'male')>Laki-laki</option>
                        <option value="female" @selected(($profile->gender ?? '') === 'female')>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $profile->birth_date?->format('Y-m-d') ?? '') }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="occupation" class="block text-sm font-medium text-slate-700 mb-1">Pekerjaan</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $profile->occupation ?? '') }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label for="institution" class="block text-sm font-medium text-slate-700 mb-1">Institusi / Asal Instansi</label>
                    <input type="text" name="institution" id="institution" value="{{ old('institution', $profile->institution ?? '') }}"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                </div>
            </div>

            <div>
                <label for="linkedin_url" class="block text-sm font-medium text-slate-700 mb-1">URL LinkedIn</label>
                <input type="url" name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url ?? '') }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                <textarea name="address" id="address" rows="2" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">{{ old('address', $profile->address ?? '') }}</textarea>
            </div>

            <div>
                <label for="profile_photo" class="block text-sm font-medium text-slate-700 mb-1">Foto Profil</label>
                <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="w-full text-sm">
            </div>

            <button type="submit" class="bg-[#001D3D] hover:opacity-90 text-white font-semibold px-6 py-3 rounded-lg text-sm">Simpan Profil</button>
        </form>
    </div>
@endsection