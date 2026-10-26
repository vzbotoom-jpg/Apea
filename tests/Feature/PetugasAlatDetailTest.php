<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use Tests\TestCase;

class PetugasAlatDetailTest extends TestCase
{
    public function test_petugas_can_view_alat_detail_statistics_and_history(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $kategori = Kategori::create([
            'nama_kategori' => 'Elektronik',
            'slug' => 'elektronik-' . uniqid(),
        ]);
        $alat = Alat::create([
            'kode_alat' => 'ALT-' . uniqid(),
            'nama_alat' => 'Proyektor',
            'slug' => 'proyektor-' . uniqid(),
            'kategori_id' => $kategori->id,
            'stok' => 2,
            'stok_tersedia' => 2,
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'harga_sewa_per_hari' => 10000,
            'denda_per_hari' => 5000,
        ]);

        $this->actingAs($petugas)
            ->get(route('petugas.alat.show', $alat))
            ->assertOk()
            ->assertSee('Unit Dipinjamkan')
            ->assertSee('Riwayat Peminjaman Alat');
    }
}
