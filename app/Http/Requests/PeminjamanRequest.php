<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alat_ids' => 'required|array|min:1|max:2',
            'alat_ids.*' => 'exists:alats,id',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_jatuh_tempo' => 'required|date|after:tanggal_pinjam',
            'catatan' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'alat_ids.required' => 'Pilih minimal 1 alat.',
            'alat_ids.min' => 'Pilih minimal 1 alat.',
            'alat_ids.max' => 'Maksimal 2 alat per peminjaman.',
            'alat_ids.*.exists' => 'Alat yang dipilih tidak valid.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh kurang dari hari ini.',
            'tanggal_jatuh_tempo.required' => 'Tanggal jatuh tempo wajib diisi.',
            'tanggal_jatuh_tempo.after' => 'Tanggal jatuh tempo harus setelah tanggal pinjam.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}