<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kategoriId = $this->route('kategori') ? $this->route('kategori')->id : null;

        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:50',
                Rule::unique('kategoris', 'nama_kategori')->ignore($kategoriId),
            ],
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
            'status.required' => 'Status wajib dipilih.',
        ];
    }
}