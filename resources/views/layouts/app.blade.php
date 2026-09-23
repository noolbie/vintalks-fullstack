{{-- Layout utama setelah login: sidebar kiri berisi menu sesuai role (admin/mentor/participant) + header berisi judul halaman dan tombol keluar --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VinTalks') — VinTalks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">
    <style>
        .badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold; }
        .badge-green { @apply bg-emerald-100 text-emerald-700; }
        .badge-yellow { @apply bg-amber-100 text-amber-700; }
        .badge-blue { @apply bg-sky-100 text-sky-700; }
        .badge-red { @apply bg-rose-100 text-rose-700; }
        .badge-gray { @apply bg-gray-100 text-gray-600; }
    </style>
</head>
<body class="bg-slate-100 font-sans">
    @php $user = auth()->user(); @endphp
    <div class="min-h-screen flex">
        <aside class="w-64 bg-[#001D3D] text-white flex flex-col fixed inset-y-0 left-0 z-40">
            <div class="px-6 py-5 border-b border-white/10">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('assets/logovintalksround.png') }}" alt="VinTalks" class="h-9 w-9 object-contain">
                    <span class="font-bold text-lg tracking-tight">VinTalks</span>
                </a>
                <p class="text-xs text-slate-300 mt-1 capitalize">{{ $user->getRoleNames()->first() ?? 'user' }} Account</p>
            </div>
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                @if ($user->isAdmin())
                    <x-sidenav-link :route="route('admin.dashboard')" label="Dashboard" icon="ri-dashboard-line"/>
                    <x-sidenav-link :route="route('admin.users.index')" label="Pengguna" icon="ri-user-settings-line"/>
                    <x-sidenav-link :route="route('admin.participants.index')" label="Peserta" icon="ri-team-line"/>
                    <x-sidenav-link :route="route('admin.mentors.index')" label="Mentor" icon="ri-user-star-line"/>
                    <x-sidenav-link :route="route('admin.topics.index')" label="Topik" icon="ri-price-tag-3-line"/>
                    <x-sidenav-link :route="route('admin.bookings.index')" label="Booking" icon="ri-calendar-check-line"/>
                    <x-sidenav-link :route="route('admin.payments.index')" label="Pembayaran" icon="ri-money-dollar-circle-line"/>
                    <x-sidenav-link :route="route('admin.transactions.index')" label="Transaksi Mentor" icon="ri-arrow-left-right-line"/>
                    <x-sidenav-link :route="route('admin.settings.edit')" label="Pengaturan" icon="ri-settings-3-line"/>
                    <x-sidenav-link :route="route('admin.consultation-results.index')" label="Hasil Konsultasi" icon="ri-file-list-3-line"/>
                @elseif ($user->isMentor())
                    <x-sidenav-link :route="route('mentor.dashboard')" label="Dashboard" icon="ri-dashboard-line"/>
                    <x-sidenav-link :route="route('mentor.profile.edit')" label="Profil" icon="ri-user-star-line"/>
                    <x-sidenav-link :route="route('mentor.availability.index')" label="Jadwal" icon="ri-calendar-line"/>
                    <x-sidenav-link :route="route('mentor.bookings.index')" label="Booking" icon="ri-calendar-check-line"/>
                    <x-sidenav-link :route="route('mentor.earnings.index')" label="Pendapatan" icon="ri-wallet-3-line"/>
                @else
                    <x-sidenav-link :route="route('participant.dashboard')" label="Dashboard" icon="ri-dashboard-line"/>
                    <x-sidenav-link :route="route('participant.mentors.index')" label="Cari Mentor" icon="ri-search-line"/>
                    <x-sidenav-link :route="route('participant.bookings.history')" label="Booking Saya" icon="ri-calendar-check-line"/>
                    <x-sidenav-link :route="route('participant.notifications')" label="Notifikasi" icon="ri-notification-3-line"/>
                    <x-sidenav-link :route="route('participant.profile.edit')" label="Profil" icon="ri-user-settings-line"/>
                @endif
            </nav>
            <div class="px-4 py-4 border-t border-white/10 text-sm">
                <p class="truncate font-medium">{{ $user->name }}</p>
                <p class="truncate text-slate-400">{{ $user->email }}</p>
            </div>
        </aside>
        <div class="flex-1 ml-64 flex flex-col">
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                <h1 class="font-semibold text-slate-800">@yield('title', 'Dashboard')</h1>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-rose-600">
                        <i class="ri-logout-box-r-line"></i> Keluar
                    </button>
                </form>
            </header>
            <main class="flex-1 px-6 py-6">
                @if (Session::has('success'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ Session::get('success') }}</div>
                @endif
                @if (Session::has('error'))
                    <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">{{ Session::get('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet">
    @stack('scripts')
</body>
</html>