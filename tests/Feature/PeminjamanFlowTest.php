<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\MutasiAlat;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\User;
use Tests\TestCase;

class PeminjamanFlowTest extends TestCase
{
    public function test_request_reserves_stock_once_and_verification_does_not_deduct_it_again(): void
    {
        $borrower = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $petugas = User::factory()->create(['role' => 'petugas']);
        $alat = $this->createAlat(2);

        $this->actingAs($borrower)->post(route('user.peminjaman.store'), [
            'alat_ids' => [$alat->id],
            'tanggal_pinjam' => now()->addDay()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(2)->toDateString(),
        ])->assertRedirect();

        $peminjaman = Peminjaman::firstOrFail();
        $this->assertSame('menunggu', $peminjaman->status);
        $this->assertSame(1, $alat->fresh()->stok_tersedia);
        $this->assertSame(1, MutasiAlat::where('alat_id', $alat->id)->count());
        $this->assertSame(1, Notifikasi::where('user_id', $admin->id)->count());
        $this->assertSame(1, Notifikasi::where('user_id', $petugas->id)->count());

        $this->actingAs($petugas)->post(route('petugas.peminjaman.verifikasi', $peminjaman))
            ->assertRedirect();

        $this->assertSame('diverifikasi', $peminjaman->fresh()->status);
        $this->assertSame(1, $alat->fresh()->stok_tersedia);
        $this->assertSame(1, MutasiAlat::where('alat_id', $alat->id)->count());

        $this->actingAs($admin)->post(route('admin.peminjaman.batalkan', $peminjaman))
            ->assertRedirect();

        $this->assertSame('dibatalkan', $peminjaman->fresh()->status);
        $this->assertSame(2, $alat->fresh()->stok_tersedia);
        $this->assertSame(2, MutasiAlat::where('alat_id', $alat->id)->count());

        $this->post(route('admin.peminjaman.batalkan', $peminjaman))
            ->assertRedirect();

        $this->assertSame(2, $alat->fresh()->stok_tersedia);
        $this->assertSame(2, MutasiAlat::where('alat_id', $alat->id)->count());
    }

    public function test_borrower_can_cancel_a_waiting_request_and_restore_its_reservation_once(): void
    {
        $borrower = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat(1);

        $this->actingAs($borrower)->post(route('user.peminjaman.store'), [
            'alat_ids' => [$alat->id],
            'tanggal_pinjam' => now()->addDay()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(2)->toDateString(),
        ])->assertRedirect();

        $peminjaman = Peminjaman::firstOrFail();

        $this->post(route('user.peminjaman.batalkan', $peminjaman))
            ->assertRedirect();

        $this->assertSame('dibatalkan', $peminjaman->fresh()->status);
        $this->assertSame(1, $alat->fresh()->stok_tersedia);
        $this->assertSame(2, MutasiAlat::where('alat_id', $alat->id)->count());

        $this->post(route('user.peminjaman.batalkan', $peminjaman))
            ->assertRedirect();

        $this->assertSame(1, $alat->fresh()->stok_tersedia);
        $this->assertSame(2, MutasiAlat::where('alat_id', $alat->id)->count());
    }

    private function createAlat(int $stock): Alat
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Elektronik',
            'slug' => 'elektronik-' . uniqid(),
        ]);

        return Alat::create([
            'kode_alat' => 'ALT-' . uniqid(),
            'nama_alat' => 'Proyektor',
            'slug' => 'proyektor-' . uniqid(),
            'kategori_id' => $kategori->id,
            'stok' => $stock,
            'stok_tersedia' => $stock,
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'harga_sewa_per_hari' => 10000,
            'denda_per_hari' => 5000,
        ]);
    }
}
