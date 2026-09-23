{{-- Halaman pengaturan pembayaran admin: atur rekening tujuan dan metode pembayaran --}}
@extends('layouts.app')

@section('title', 'Pengaturan Pembayaran')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-xl border border-slate-200 mt-4 p-6">
        <h1 class="text-xl font-bold text-slate-800">Pengaturan Pembayaran</h1>
        <p class="text-sm text-slate-500 mt-1">Atur rekening tujuan dan metode pembayaran yang ditampilkan ke peserta.</p>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="payment_bank_name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Bank</label>
                <input type="text" name="payment_bank_name" id="payment_bank_name" value="{{ old('payment_bank_name', $payment_bank_name) }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
            </div>

            <div>
                <label for="payment_account_name" class="block text-sm font-semibold text-slate-700 mb-1">Nama Pemilik Rekening</label>
                <input type="text" name="payment_account_name" id="payment_account_name" value="{{ old('payment_account_name', $payment_account_name) }}"
                       placeholder="cth: VinTalks"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
            </div>

            <div>
                <label for="payment_account_number" class="block text-sm font-semibold text-slate-700 mb-1">Nomor Rekening</label>
                <input type="text" name="payment_account_number" id="payment_account_number" value="{{ old('payment_account_number', $payment_account_number) }}"
                       placeholder="cth: 081329393939"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                <p class="text-xs text-slate-500 mt-1">Nomor ini ditampilkan kepada peserta saat memilih metode Transfer Bank.</p>
            </div>

            <div>
                <span class="block text-sm font-semibold text-slate-700 mb-2">Metode Pembayaran</span>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 cursor-pointer">
                        <input type="checkbox" name="payment_methods[]" value="qris"
                               @checked(in_array('qris', $payment_methods)) class="h-4 w-4">
                        <span>
                            <span class="block text-sm font-medium text-slate-700">QRIS</span>
                            <span class="block text-xs text-slate-500">Scan kode QR dari aplikasi pembayaran</span>
                        </span>
                    </label>
                    <label class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 cursor-pointer">
                        <input type="checkbox" name="payment_methods[]" value="transfer_bank"
                               @checked(in_array('transfer_bank', $payment_methods)) class="h-4 w-4">
                        <span>
                            <span class="block text-sm font-medium text-slate-700">Transfer Bank</span>
                            <span class="block text-xs text-slate-500">Transfer ke rekening VinTalks di atas</span>
                        </span>
                    </label>
                    <label class="flex items-center gap-3 bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 cursor-pointer">
                        <input type="checkbox" name="payment_methods[]" value="digital_wallet"
                               @checked(in_array('digital_wallet', $payment_methods)) class="h-4 w-4">
                        <span>
                            <span class="block text-sm font-medium text-slate-700">Dompet Digital</span>
                            <span class="block text-xs text-slate-500">cth: GoPay, OVO, DANA</span>
                        </span>
                    </label>
                </div>
            </div>

            <div>
                <label for="commission_rate" class="block text-sm font-semibold text-slate-700 mb-1">Persentase Komisi Platform (%)</label>
                <input type="number" name="commission_rate" id="commission_rate" min="0" max="100" step="0.01"
                       value="{{ old('commission_rate', $commission_rate) }}"
                       class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#FFC300]">
                <p class="text-xs text-slate-500 mt-1">Potongan platform dari tiap sesi selesai. Contoh: harga 150.000 x 15% = potongan 22.500, mentor menerima 127.500.</p>
            </div>

            <button type="submit" class="w-full bg-[#001D3D] hover:opacity-90 text-white font-semibold py-3 rounded-lg text-sm">
                Simpan Pengaturan
            </button>
        </form>
    </div>
@endsection