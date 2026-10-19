<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use Tests\TestCase;

class AlatCatalogViewTest extends TestCase
{
    public function test_public_and_user_catalogs_render_the_shared_alat_card(): void
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Kamera',
            'slug' => 'kamera',
        ]);

        $alat = Alat::create([
            'kode_alat' => 'ALT-TEST',
            'nama_alat' => 'Kamera Uji',
            'slug' => 'kamera-uji',
            'kategori_id' => $kategori->id,
            'stok' => 3,
            'stok_tersedia' => 2,
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'harga_sewa_per_hari' => 12000,
            'denda_per_hari' => 5000,
        ]);

        $publicResponse = $this->get(route('alat'));
        $publicResponse->assertOk()
            ->assertSee($alat->nama_alat)
            ->assertSee($alat->kode_alat)
            ->assertSee('Stok:')
            ->assertSee('Rp 12.000');

        $this->get('/')
            ->assertOk()
            ->assertSee($alat->nama_alat)
            ->assertSee($alat->kode_alat)
            ->assertSee('Stok:')
            ->assertSee('Rp 12.000')
            ->assertSee(route('user.alat.show', $alat));

        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('user.alat.index'))
            ->assertOk()
            ->assertSee($alat->nama_alat)
            ->assertSee($alat->kode_alat)
            ->assertSee('Stok:')
            ->assertSee('Rp 12.000')
            ->assertSee(route('user.alat.show', $alat));
    }
}
