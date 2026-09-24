<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot_id' => ['required', 'integer'],
            'topic_id' => ['nullable', 'integer', 'exists:topics,id'],
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'career_goal' => ['nullable', 'string', 'max:5000'],
            'consultation_topic' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'additional_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}