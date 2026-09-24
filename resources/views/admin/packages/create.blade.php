{{-- Form tambah paket layanan --}}
@extends('layouts.app')

@section('title', 'Tambah Paket')

@section('content')
    <a href="{{ route('admin.packages.index') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke paket</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6 max-w-3xl">
        <h1 class="text-lg font-bold text-slate-800 mb-5">Tambah Paket</h1>

        <form method="POST" action="{{ route('admin.packages.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Paket</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Mis. Starter Package"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode (slug)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="starter"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Deskripsi / Keterangan</label>
                <textarea name="description" rows="2" placeholder="Keterangan singkat paket…"
                          class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Normal (Rp)</label>
                    <input type="number" name="old_price" value="{{ old('old_price') }}" required min="0" placeholder="150000"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Promo (Rp)</label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" placeholder="110000"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Gambar (asset path)</label>
                    <input type="text" name="image" value="{{ old('image') }}" placeholder="assets/xxx.png"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm">
                </div>
            </div>
            <p class="text-xs text-slate-500">Potongan otomatis = Harga Normal - Harga Promo.</p>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Benefit yang Diberikan</label>
                <div id="benefits-container" class="space-y-2">
                    <div class="flex gap-2">
                        <input type="text" name="benefits[]" placeholder="Benefit 1…"
                               class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm">
                        <button type="button" onclick="this.parentElement.remove()" class="remove-benefit text-rose-500 hover:text-rose-700 text-sm px-2">✕</button>
                    </div>
                </div>
                <button type="button" id="add-benefit" class="mt-3 text-sm font-semibold text-[#001D3D] hover:underline">+ Tambah Benefit</button>
            </div>

            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_popular" value="1" {{ old('is_popular') ? 'checked' : '' }} class="h-4 w-4">
                    ⭐ Most Popular
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4">
                    Aktif
                </label>
            </div>

            <button type="submit" class="bg-[#001D3D] text-white font-semibold px-8 py-2.5 rounded-lg text-sm">Simpan</button>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('add-benefit').addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'flex gap-2';
            row.innerHTML = '<input type="text" name="benefits[]" placeholder="Benefit…" class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm">' +
                            '<button type="button" class="remove-benefit text-rose-500 hover:text-rose-700 text-sm px-2">✕</button>';
            row.querySelector('.remove-benefit').addEventListener('click', () => row.remove());
            document.getElementById('benefits-container').appendChild(row);
        });
        document.querySelectorAll('.remove-benefit').forEach(btn => btn.addEventListener('click', () => btn.parentElement.remove()));
    </script>
    @endpush
@endsection