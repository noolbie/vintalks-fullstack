{{-- Halaman pembayaran peserta: instruksi transfer / Google Form / form unggah bukti bayar untuk 1 booking --}}
@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <a href="{{ route('participant.bookings.show', $booking) }}" class="text-sm text-slate-600 hover:text-[#001D3D]">&larr; Kembali ke detail booking</a>

    <div class="max-w-2xl mx-auto bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <div class="text-center pb-5 border-b border-slate-100">
            <h1 class="text-xl font-bold text-slate-800">Pembayaran Sesi</h1>
            <p class="text-sm text-slate-500 mt-1">Booking {{ $booking->booking_code }} • {{ $booking->mentorProfile->display_name ?? 'Mentor' }}</p>
            <p class="text-3xl font-bold text-[#001D3D] mt-3">{{ $booking->price_formatted }}</p>
        </div>

        @if ($paymentMode === 'google_form' && $googleFormUrl)
            <div class="mt-6 text-center">
                <p class="text-sm text-slate-600 mb-4">
                    Silakan lakukan pembayaran melalui instruksi berikut, lalu kirim bukti melalui Google Form di bawah ini.
                </p>
                <a href="{{ $googleFormUrl }}" target="_blank" rel="noopener"
                   class="inline-block bg-[#001D3D] text-white px-8 py-3 rounded-lg font-semibold">
                    Isi Google Form <i class="ri-external-link-line"></i>
                </a>
                <p class="text-xs text-slate-500 mt-4">
                    Setelah mengisi form, status pembayaran dan konfirmasi akan dikelola oleh admin. Halaman ini akan menampilkan perkembangan status Anda.
                </p>
            </div>
        @else
            <form method="POST" action="{{ route('participant.payments.store', $booking) }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf

                @if (!empty($account['account_number']) && in_array('transfer_bank', array_column($paymentMethods, 'value')))
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-slate-700">Transfer ke rekening VinTalks</p>
                        <dl class="mt-2 text-sm text-slate-600">
                            <dt class="text-slate-400">Bank</dt>
                            <dd class="font-medium text-slate-800">{{ $account['bank_name'] }}</dd>
                            <dt class="text-slate-400 mt-1">Atas Nama</dt>
                            <dd class="font-medium text-slate-800">{{ $account['account_name'] }}</dd>
                            <dt class="text-slate-400 mt-1">Nomor Rekening</dt>
                            <dd class="font-semibold text-[#001D3D] text-lg">{{ $account['account_number'] }}</dd>
                        </dl>
                    </div>
                @endif

                @if (!empty($paymentMethods))
                    <div>
                        <span class="block text-sm font-semibold text-slate-700 mb-2">Metode Pembayaran</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach ($paymentMethods as $method)
                                <label class="cursor-pointer">
                                    <input type="radio" name="payment_method" value="{{ $method['value'] }}"
                                           class="peer sr-only" required @checked($loop->first)>
                                    <span class="block border border-slate-200 rounded-lg px-4 py-3 text-center peer-checked:border-[#FFC300] peer-checked:bg-amber-50 peer-checked:ring-2 peer-checked:ring-[#FFC300]">
                                        <span class="block text-sm font-medium text-slate-700">{{ $method['label'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label for="payment_reference" class="block text-sm font-semibold text-slate-700 mb-2">Referensi Pembayaran (opsional)</label>
                    <input type="text" name="payment_reference" id="payment_reference" placeholder="cth: No. virtual account / ID transaksi"
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                </div>

                <div>
                    <label for="proof" class="block text-sm font-semibold text-slate-700 mb-2">Unggah Bukti Pembayaran</label>
                    <input type="file" name="proof" id="proof" accept=".jpg,.jpeg,.png,.pdf" required
                           class="w-full text-sm border border-slate-300 rounded-lg py-2 px-3">
                    <p class="text-xs text-slate-500 mt-2">Format JPG, PNG, atau PDF, maksimal 5MB.</p>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-500">
                    Setelah bukti dikirim, admin akan memverifikasi maksimal 1×24 jam. Anda akan mendapat notifikasi setelah pembayaran terverifikasi.
                </div>

                <button type="submit" class="w-full bg-[#FFC300] hover:bg-amber-400 text-[#001D3D] font-semibold py-3 rounded-lg text-sm">
                    Kirim Bukti Pembayaran
                </button>
            </form>
        @endif
    </div>
@endsection