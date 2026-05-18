<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScanReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receipt_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,heic', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'receipt_image.required' => 'Gambar struk wajib diunggah.',
            'receipt_image.image'    => 'File harus berupa gambar.',
            'receipt_image.mimes'    => 'Format gambar harus JPG, PNG, WEBP, atau HEIC.',
            'receipt_image.max'      => 'Ukuran gambar maksimal 10MB.',
        ];
    }
}
