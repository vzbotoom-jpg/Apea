<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kondisi_alat' => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])],
            'catatan' => 'nullable|string|max:500',
            'denda' => 'nullable|numeric|min:0',
            'bukti_foto' => 'nullable|image|max:2048|mimes:jpeg,png,jpg',
        ];
    }

    public function messages(): array
    {
        return [
            'kondisi_alat.required' => 'Kondisi alat wajib dipilih.',
            'denda.numeric' => 'Denda harus berupa angka.',
            'denda.min' => 'Denda minimal 0.',
            'bukti_foto.image' => 'File harus berupa gambar.',
            'bukti_foto.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}