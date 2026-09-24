{{-- Halaman daftar paket layanan untuk admin: edit nama, deskripsi, harga, dan benefit paket --}}
@extends('layouts.app')

@section('title', 'Paket Layanan')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-slate-500">Paket yang tampil di landing page dan bisa dipakai peserta untuk potongan harga.</p>
        <a href="{{ route('admin.packages.create') }}" class="bg-[#001D3D] text-white px-5 py-2 rounded-lg text-sm font-semibold">+ Tambah Paket</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="text-left px-5 py-3">Paket</th>
                    <th class="text-left px-5 py-3">Harga Normal</th>
                    <th class="text-left px-5 py-3">Harga Promo</th>
                    <th class="text-left px-5 py-3">Potongan</th>
                    <th class="text-left px-5 py-3">Benefit</th>
                    <th class="text-left px-5 py-3">Label</th>
                    <th class="text-left px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($packages as $package)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <p class="font-semibold text-[#001D3D]">{{ $package->name }}</p>
                            <p class="text-xs text-slate-400">{{ $package->slug }}</p>
                        </td>
                        <td class="px-5 py-3 text-slate-600 line-through">{{ $package->old_price_formatted }}</td>
                        <td class="px-5 py-3 font-semibold">{{ $package->price_formatted }}</td>
                        <td class="px-5 py-3 text-emerald-700 font-semibold">-{{ $package->discount_formatted }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ count($package->benefits ?? []) }} item</td>
                        <td class="px-5 py-3">
                            @if ($package->is_popular)
                                <span class="badge badge-yellow">⭐ Most Popular</span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="badge {{ $package->is_active ? 'badge-green' : 'badge-gray' }}">{{ $package->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.packages.edit', $package) }}" class="text-[#001D3D] font-semibold hover:underline text-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" onsubmit="return confirm('Hapus paket ini? Booking yang memakainya akan kehilangan paket.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rose-600 font-semibold hover:underline text-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-slate-500">Belum ada paket. Tambahkan paket pertama.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection