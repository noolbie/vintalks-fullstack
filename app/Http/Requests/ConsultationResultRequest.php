<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConsultationResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'summary' => ['required', 'string', 'max:10000'],
            'mentor_notes' => ['nullable', 'string', 'max:10000'],
            'documents' => ['nullable', 'array', 'min:0', 'max:5'],
            'documents.*.title' => ['nullable', 'string', 'max:255'],
            'documents.*.document_type' => ['nullable', 'string', 'in:review_cv,review_linkedin,assessment,consultation_result,notes,other'],
            'documents.*.file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'summary.required' => 'Ringkasan hasil wajib diisi.',
            'documents.*.file.mimes' => 'File hasil harus berupa PDF, DOC, JPG, atau PNG.',
            'documents.*.file.max' => 'Ukuran file hasil maksimal 10MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('documents')) {
                return;
            }

            foreach ($this->input('documents', []) as $index => $doc) {
                $hasFile = $this->hasFile('documents.'.$index.'.file');
                $hasTitle = filled(data_get($doc, 'title'));

                if ($hasFile && ! $hasTitle) {
                    $validator->errors()->add("documents.$index.title", 'Judul wajib diisi jika ada file.');
                }

                if (! $hasFile && ($hasTitle || filled(data_get($doc, 'document_type')))) {
                    $validator->errors()->add("documents.$index.file", 'Lampirkan file dokumen.');
                }
            }
        });
    }
}