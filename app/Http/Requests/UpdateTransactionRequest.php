<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'merchant_name'    => ['nullable', 'string', 'max:255'],
            'total_amount'     => ['required', 'numeric', 'min:1', 'max:9999999999999'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'category_id'      => ['required', 'exists:categories,id'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'total_amount.required'     => 'Total harga wajib diisi.',
            'total_amount.min'          => 'Total harga minimal Rp 1.',
            'total_amount.max'          => 'Nominal transaksi maksimal Rp 9.999.999.999.999.',
            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.before_or_equal' => 'Tanggal tidak boleh di masa depan.',
            'category_id.required'      => 'Kategori wajib dipilih.',
        ];
    }
}
