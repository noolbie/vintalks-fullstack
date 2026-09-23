<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_bank_name' => ['required', 'string', 'max:50'],
            'payment_account_name' => ['required', 'string', 'max:50'],
            'payment_account_number' => ['required', 'string', 'max:50'],
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['required', 'string', Rule::in(['qris', 'transfer_bank', 'digital_wallet'])],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_bank_name.required' => 'Nama bank wajib diisi.',
            'payment_account_name.required' => 'Nama pemilik rekening wajib diisi.',
            'payment_account_number.required' => 'Nomor rekening wajib diisi.',
            'payment_methods.required' => 'Pilih minimal satu metode pembayaran.',
            'payment_methods.min' => 'Pilih minimal satu metode pembayaran.',
            'commission_rate.required' => 'Persentase komisi wajib diisi.',
            'commission_rate.numeric' => 'Persentase komisi harus angka.',
            'commission_rate.min' => 'Persentase komisi minimal 0.',
            'commission_rate.max' => 'Persentase komisi maksimal 100.',
        ];
    }
}