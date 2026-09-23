<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    // ==== Pengaturan aplikasi ====

    // Tampilkan form pengaturan pembayaran (bank, rekening, dan metode bayar).
    public function edit(): View
    {
        $settings = [
            'payment_bank_name' => Setting::get('payment_bank_name', 'BCA'),
            'payment_account_name' => Setting::get('payment_account_name', 'VinTalks'),
            'payment_account_number' => Setting::get('payment_account_number', ''),
            'payment_methods' => Setting::paymentMethodValues(),
            'commission_rate' => Setting::get('commission_rate', 15),
        ];

        return view('admin.settings.edit', $settings);
    }

    // Simpan pengaturan pembayaran ke tabel settings.
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        Setting::set('payment_bank_name', $request->input('payment_bank_name'));
        Setting::set('payment_account_name', $request->input('payment_account_name'));
        Setting::set('payment_account_number', $request->input('payment_account_number'));
        Setting::set('payment_methods', $request->input('payment_methods', []));
        Setting::set('commission_rate', $request->input('commission_rate'));

        return back()->with('success', 'Pengaturan pembayaran berhasil disimpan.');
    }
}