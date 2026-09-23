@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
    <form method="GET" action="{{ route('admin.users.index') }}" class="bg-white rounded-xl border border-slate-200 p-4 mb-6 flex flex-col sm:flex-row gap-3">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email…"
               class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FFC300]">
        <select name="role" class="rounded-lg border border-slate-300 px-4 py-2 text-sm">
            <option value="">Semua Role</option>
            @foreach(['participant' => 'Peserta', 'mentor' => 'Mentor', 'admin' => 'Admin'] as $value => $label)
                <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="bg-[#001D3D] text-white px-6 py-2 rounded-lg text-sm font-semibold">Filter</button>
    </form>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="text-left px-5 py-3">Nama</th>
                    <th class="text-left px-5 py-3">Email</th>
                    <th class="text-left px-5 py-3">Role</th>
                    <th class="text-left px-5 py-3">Bergabung</th>
                    <th class="text-left px-5 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            @foreach ($user->roles as $role)
                                <span class="badge {{ $role->name === 'admin' ? 'badge-red' : ($role->name === 'mentor' ? 'badge-blue' : 'badge-green') }}">{{ ucfirst($role->name) }}</span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            @unless ($user->isAdmin())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini beserta datanya?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-rose-600 hover:underline text-xs font-semibold">Hapus</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">Tidak ada pengguna.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection