<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kamera = Kategori::where('nama_kategori', 'Kamera & Lensa')->first();
        $audio = Kategori::where('nama_kategori', 'Audio & Recording')->first();
        $proyektor = Kategori::where('nama_kategori', 'Elektronik & Proyektor')->first();

        $alats = [
            [
                'kode_alat' => 'ALT-001',
                'nama_alat' => 'Sony Alpha A7 III',
                'kategori_id' => $kamera?->id,
                'stok' => 4,
                'stok_tersedia' => 4,
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'harga_sewa_per_hari' => 150000,
                'denda_per_hari' => 30000,
                'deskripsi' => 'Kamera mirrorless full-frame dengan kapabilitas foto dan video 4K.',
            ],
            [
                'kode_alat' => 'ALT-002',
                'nama_alat' => 'Rode Wireless GO II',
                'kategori_id' => $audio?->id,
                'stok' => 5,
                'stok_tersedia' => 5,
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'harga_sewa_per_hari' => 50000,
                'denda_per_hari' => 15000,
                'deskripsi' => 'Sistem mikrofon wireless ringkas kanal ganda untuk konten dan liputan.',
            ],
            [
                'kode_alat' => 'ALT-003',
                'nama_alat' => 'Epson EB-E500 Projector',
                'kategori_id' => $proyektor?->id,
                'stok' => 3,
                'stok_tersedia' => 3,
                'kondisi' => 'baik',
                'status' => 'tersedia',
                'harga_sewa_per_hari' => 75000,
                'denda_per_hari' => 20000,
                'deskripsi' => 'Proyektor 3300 lumens resolusi XGA untuk kebutuhan presentasi ruangan.',
            ],
        ];

        foreach ($alats as $item) {
            if ($item['kategori_id']) {
                Alat::firstOrCreate(
                    ['kode_alat' => $item['kode_alat']],
                    array_merge($item, [
                        'slug' => Str::slug($item['nama_alat']) . '-' . Str::lower($item['kode_alat']),
                    ])
                );
            }
        }
    }
}
