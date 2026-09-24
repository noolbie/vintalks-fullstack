@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
    <a href="{{ route('mentor.bookings.index') }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke booking</a>

    <div class="bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Booking Code</p>
                <h1 class="text-2xl font-bold text-slate-800 mt-1">{{ $booking->booking_code }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ $booking->participant->name }} • {{ $booking->session_date->format('D, d M Y') }} • {{ $booking->start_time_label }}–{{ $booking->end_time_label }} WIB
                </p>
            </div>
            <div class="text-right">
                <span class="badge {{ $booking->booking_status === 'confirmed' ? 'badge-green' : ($booking->booking_status === 'completed' ? 'badge-blue' : ($booking->booking_status === 'cancelled' || $booking->booking_status === 'rejected' ? 'badge-red' : 'badge-yellow')) }}">{{ $booking->booking_status_label }}</span>
                <p class="text-sm text-slate-500 mt-2">Pembayaran: <span class="badge {{ $booking->payment_status === 'verified' ? 'badge-green' : 'badge-yellow' }}">{{ $booking->payment_status_label }}</span></p>
            </div>
        </div>

        @if ($booking->hasPackage())
            <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="font-semibold text-slate-700">Paket Peserta: {{ $booking->package?->name }}</h3>
                    <span class="badge {{ $booking->isPackageApproved() ? 'badge-green' : ($booking->isPackageRejected() ? 'badge-red' : 'badge-yellow') }}">{{ $booking->package_status_label }}</span>
                </div>
                <p class="text-sm text-slate-600 mt-1">{{ $booking->package?->description }}</p>
                <p class="text-sm mt-2">
                    Potongan paket: <span class="font-bold text-emerald-700">{{ $booking->discount_amount_formatted }}</span>
                    <span class="text-slate-500">• Harga sesi: {{ $booking->price_formatted }}</span>
                </p>
                @if ($booking->package?->benefits)
                    <h4 class="text-sm font-semibold text-slate-700 mt-3">Yang harus diberikan pada hasil sesi:</h4>
                    <ul class="text-sm text-slate-600 mt-1 space-y-1 list-disc list-inside">
                        @foreach ($booking->package->benefits as $benefit)
                            <li>{{ $benefit }}</li>
                        @endforeach
                    </ul>
                @endif
                @if ($booking->isPackagePending())
                    <p class="mt-3 text-xs text-amber-700">Paket masih menunggu persetujuan admin; potongan belum final.</p>
                @endif
                @if ($booking->isPackageRejected() && $booking->package_rejection_reason)
                    <p class="mt-3 text-xs text-rose-700 bg-rose-50 rounded-lg px-3 py-2">Paket ditolak admin: {{ $booking->package_rejection_reason }}</p>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="space-y-4">
                <div class="bg-slate-50 rounded-xl p-4">
                    <h3 class="font-semibold text-slate-700 mb-3">Detail Peserta</h3>
                    <dl class="text-sm space-y-2">
                        <div class="flex justify-between"><dt class="text-slate-500">Nama</dt><dd class="font-medium">{{ $booking->participant->name }}</dd></div>
                        <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd class="font-medium">{{ $booking->participant->email }}</dd></div>
                        @if ($booking->participant->participantProfile)
                            <div class="flex justify-between"><dt class="text-slate-500">WhatsApp</dt><dd class="font-medium">{{ $booking->participant->participantProfile->phone }}</dd></div>
                            @if ($booking->participant->participantProfile->occupation)
                                <div class="flex justify-between"><dt class="text-slate-500">Pekerjaan</dt><dd class="font-medium">{{ $booking->participant->participantProfile->occupation }}</dd></div>
                            @endif
                        @endif
                    </dl>
                </div>

                @if ($booking->requirement)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Kebutuhan Peserta</h3>
                        <dl class="text-sm space-y-2">
                            @if ($booking->requirement->linkedin_url)
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">LinkedIn</dt><dd class="font-medium"><a href="{{ $booking->requirement->linkedin_url }}" target="_blank" rel="noopener" class="text-[#001D3D] underline">Buka</a></dd></div>
                            @endif
                            @if ($booking->requirement->consultation_topic)
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Judul Topik</dt><dd class="font-medium text-right">{{ $booking->requirement->consultation_topic }}</dd></div>
                            @endif
                            @if ($booking->requirement->career_goal)
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Tujuan Karier</dt><dd class="font-medium text-right max-w-[60%]">{{ $booking->requirement->career_goal }}</dd></div>
                            @endif
                            @if ($booking->requirement->description)
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Deskripsi</dt><dd class="font-medium text-right max-w-[60%]">{{ $booking->requirement->description }}</dd></div>
                            @endif
                            @if ($booking->requirement->additional_notes)
                                <div class="flex justify-between gap-4"><dt class="text-slate-500">Catatan</dt><dd class="font-medium text-right max-w-[60%]">{{ $booking->requirement->additional_notes }}</dd></div>
                            @endif
                        </dl>
                    </div>
                @endif
            </div>

            <div class="space-y-4">
                @if ($booking->documents->count())
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Dokumen Peserta</h3>
                        <div class="space-y-2">
                            @foreach ($booking->documents as $document)
                                <div class="flex items-center justify-between text-sm bg-white rounded-lg px-4 py-3 border border-slate-100">
                                    <span>{{ $document->original_name }}</span>
                                    <a href="{{ $document->signed_url }}" class="font-semibold text-[#001D3D] hover:underline">Unduh</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($booking->booking_status === 'confirmed' && $booking->meeting_url)
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <h3 class="font-semibold text-emerald-800 mb-1">Link Meeting</h3>
                        <p class="text-sm text-emerald-700">{{ $booking->meeting_provider_label }}: </p>
                        <a href="{{ $booking->meeting_url }}" target="_blank" rel="noopener" class="inline-block mt-2 bg-[#001D3D] text-white px-5 py-2 rounded-lg text-sm font-semibold">Buka Meeting</a>
                    </div>
                @endif

                @if ($booking->booking_status === 'confirmed' || $booking->booking_status === 'completed')
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-1">Aksi</h3>
                        <div class="space-y-3 mt-3">
                            @if ($booking->booking_status === 'confirmed')
                                <form method="POST" action="{{ route('mentor.bookings.complete', $booking) }}" onsubmit="return confirm('Tandai sesi ini selesai? Hasil konsultasi dapat diisi setelah ini.')">
                                    @csrf
                                    <button type="submit" class="w-full bg-[#001D3D] hover:opacity-90 text-white font-semibold py-2.5 rounded-lg text-sm">Tandai Sesi Selesai</button>
                                </form>
                            @endif
                            @if ($booking->booking_status === 'completed')
                                @if ($booking->consultationResult)
                                    <p class="text-sm text-emerald-700 font-medium">Hasil konsultasi sudah diisi.</p>
                                @else
                                    <a href="{{ route('mentor.consultation-results.create', $booking) }}" class="block text-center bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold py-2.5 rounded-lg text-sm">Isi Hasil Konsultasi</a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

                @if ($booking->consultationResult)
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h3 class="font-semibold text-slate-700 mb-2">Hasil Konsultasi</h3>
                        <p class="text-sm text-slate-600 whitespace-pre-line">{{ Str::limit($booking->consultationResult->summary ?? '—', 200) }}</p>
                        @if ($booking->consultationResult->documents->count())
                            <div class="mt-3 space-y-2">
                                @foreach ($booking->consultationResult->documents as $doc)
                                    <div class="flex items-center justify-between text-sm bg-white rounded-lg px-4 py-3 border border-slate-100">
                                        <span>{{ $doc->title ?? $doc->original_name }}</span>
                                        <a href="{{ $doc->signed_url }}" class="font-semibold text-[#001D3D] hover:underline">Unduh</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection