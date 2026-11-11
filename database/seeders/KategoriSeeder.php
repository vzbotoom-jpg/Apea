<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama_kategori' => 'Kamera & Lensa',
                'deskripsi' => 'Perangkat kamera profesional, mirrorless, dan lensa penunjang.',
                'status' => 'active',
            ],
            [
                'nama_kategori' => 'Audio & Recording',
                'deskripsi' => 'Mikrofon, recorder suara, dan perlengkapan audio visual.',
                'status' => 'active',
            ],
            [
                'nama_kategori' => 'Lighting & Studio',
                'deskripsi' => 'Lampu sorot, softbox, stand, dan aksesoris pencahayaan studio.',
                'status' => 'active',
            ],
            [
                'nama_kategori' => 'Elektronik & Proyektor',
                'deskripsi' => 'Proyektor LCD, layar presentasi, dan aksesoris multimedia.',
                'status' => 'active',
            ],
        ];

        foreach ($kategoris as $item) {
            Kategori::firstOrCreate(
                ['slug' => Str::slug($item['nama_kategori'])],
                [
                    'nama_kategori' => $item['nama_kategori'],
                    'deskripsi' => $item['deskripsi'],
                    'status' => $item['status'],
                ]
            );
        }
    }
}
