<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'merchant_name'    => ['nullable', 'string', 'max:255'],
            'total_amount'     => ['required', 'numeric', 'min:1'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'category_id'      => ['required', 'exists:categories,id'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'receipt_image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,heic', 'max:10240'],
            'source'           => ['nullable', 'string', 'in:ai,manual,recurring'],
            'ai_confidence'    => ['nullable', 'string', 'in:high,medium,low'],
            'ai_raw_response'  => ['nullable', 'array'],
            'currency'         => ['nullable', 'string', 'size:3'],
            'manual_rate'      => ['nullable', 'numeric', 'min:0.000001'],
        ];
    }

    public function messages(): array
    {
        return [
            'total_amount.required'     => 'Total harga wajib diisi.',
            'total_amount.min'          => 'Total harga minimal Rp 1.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'category_id.required'      => 'Kategori wajib dipilih.',
            'category_id.exists'        => 'Kategori tidak valid.',
            'receipt_image.max'         => 'Ukuran gambar maksimal 10MB.',
            'receipt_image.mimes'       => 'Format gambar harus JPG, PNG, WEBP, atau HEIC.',
        ];
    }
}
