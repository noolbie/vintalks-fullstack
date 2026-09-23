<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'meeting_provider' => ['required', Rule::in(['google_meet', 'zoom', 'other'])],
            'meeting_url' => ['required', 'url', 'max:500'],
        ];
    }
}