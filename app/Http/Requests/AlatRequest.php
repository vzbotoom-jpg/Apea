<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $alatId = $this->route('alat') ? $this->route('alat')->id : null;

        return [
            'nama_alat' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategoris,id',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'kondisi' => ['required', Rule::in(['baik', 'rusak_ringan', 'rusak_berat', 'perbaikan'])],
            'status' => ['required', Rule::in(['tersedia', 'dipinjam', 'perbaikan', 'tidak_tersedia'])],
            'gambar' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,webp',
            'harga_sewa_per_hari' => 'required|integer|min:0',
            'denda_per_hari' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_alat.required' => 'Nama alat wajib diisi.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka.',
            'stok.min' => 'Stok minimal 0.',
            'kondisi.required' => 'Kondisi alat wajib dipilih.',
            'status.required' => 'Status alat wajib dipilih.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'harga_sewa_per_hari.required' => 'Harga sewa per hari wajib diisi.',
            'harga_sewa_per_hari.integer' => 'Harga sewa harus berupa angka.',
            'harga_sewa_per_hari.min' => 'Harga sewa minimal 0.',
        ];
    }
}