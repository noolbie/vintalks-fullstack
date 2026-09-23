<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document' => ['required', 'file'],
            'document_type' => ['required', 'string', 'in:cv,supporting_document,linkedin,other'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $file = $this->file('document');
            if (! $file) {
                return;
            }

            $type = $this->input('document_type', 'other');
            $allowed = match ($type) {
                'cv' => ['pdf', 'doc', 'docx'],
                'supporting_document', 'linkedin', 'other' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'],
                default => ['pdf', 'doc', 'docx'],
            };

            $extension = strtolower($file->getClientOriginalExtension());
            if (! in_array($extension, $allowed, true)) {
                $validator->errors()->add('document', 'Tipe file tidak diizinkan untuk jenis dokumen ini.');
            }

            if ($file->getSize() > 5 * 1024 * 1024) {
                $validator->errors()->add('document', 'Ukuran file maksimal 5MB.');
            }
        });
    }
}