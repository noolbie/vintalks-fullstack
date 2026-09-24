<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('packages', 'slug')->ignore($this->route('package'))],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'old_price' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0', 'lte:old_price'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_popular' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'old_price.required' => 'Harga normal wajib diisi.',
            'price.required' => 'Harga promo wajib diisi.',
            'price.lte' => 'Harga promo tidak boleh lebih besar dari harga normal.',
            'slug.unique' => 'Kode paket sudah dipakai.',
        ];
    }
}