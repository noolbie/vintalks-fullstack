<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') — VinTalks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{ asset('assets/favicon.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-[#001D3D] min-h-screen flex items-center justify-center px-4 font-sans">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <img src="{{ asset('assets/logovintalksround.png') }}" alt="VinTalks" class="h-12 w-12 object-contain">
            </a>
            <h1 class="text-white text-2xl font-bold mt-3">@yield('heading', 'VinTalks')</h1>
            <p class="text-slate-400 text-sm mt-1">@yield('subheading', 'Konsultasi karier global Anda dimulai di sini.')</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if (Session::has('status'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ Session::get('status') }}</div>
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
        </div>
        <p class="text-center text-slate-500 text-xs mt-6">© {{ date('Y') }} Logika Tech Repair | Cyber Security Analyst. All Rights Reserved.</p>
    </div>
</body>
</html>
