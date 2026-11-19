<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPeminjaman;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Tests\TestCase;

class PeminjamanQrScanTest extends TestCase
{
    public function test_new_loans_receive_distinct_random_qr_tokens(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat();
        $first = $this->createLoan($user, $alat);
        $second = $this->createLoan($user, $alat);

        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $first->qr_token);
        $this->assertNotSame($first->qr_token, $second->qr_token);
    }

    public function test_loan_qr_endpoint_returns_a_png_for_its_owner(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat();
        $loan = $this->createLoan($user, $alat);
        $otherLoan = $this->createLoan($otherUser, $alat);

        $response = $this->actingAs($user)
            ->get(route('qr.peminjaman', $loan))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png')
            ->assertHeader('Cache-Control', 'no-store, private');
        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $response->getContent());

        $this->get(route('qr.peminjaman', $otherLoan))->assertForbidden();
    }

    public function test_scanning_a_transaction_qr_drives_the_valid_next_actions(): void
    {
        $staff = User::factory()->create(['role' => 'petugas']);
        $borrower = User::factory()->create(['role' => 'user']);
        $alat = $this->createAlat([
            'stok' => 1,
            'stok_tersedia' => 0,
            'status' => 'dipinjam',
        ]);
        $loan = $this->createLoan($borrower, $alat);
        DetailPeminjaman::create([
            'peminjaman_id' => $loan->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
            'harga_sewa_saat_pinjam' => 10000,
            'subtotal' => 10000,
        ]);

        $this->actingAs($staff)
            ->get(route('petugas.scan.transaksi', $loan->qr_token))
            ->assertOk()
            ->assertSee(route('petugas.peminjaman.verifikasi', $loan));

        $this->post(route('petugas.peminjaman.verifikasi', $loan))->assertRedirect();
        $this->assertSame('diverifikasi', $loan->fresh()->status);

        $this->get(route('petugas.scan.transaksi', $loan->qr_token))
            ->assertOk()
            ->assertSee(route('petugas.peminjaman.ambil', $loan));

        $this->post(route('petugas.peminjaman.ambil', $loan))->assertRedirect();
        $this->assertSame('dipinjam', $loan->fresh()->status);

        $this->get(route('petugas.scan.transaksi', $loan->qr_token))
            ->assertOk()
            ->assertSee(route('petugas.pengembalian.proses', $loan));

        $this->post(route('petugas.pengembalian.save', $loan), [
            'kondisi_alat' => 'baik',
        ])->assertRedirect(route('petugas.pengembalian.index'));

        $this->assertSame('dikembalikan', $loan->fresh()->status);
        $this->assertSame(1, $alat->fresh()->stok_tersedia);
    }

    public function test_transaction_scan_requires_staff_and_rejects_unknown_tokens(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user)
            ->get('/petugas/scan/transaksi/'.str_repeat('a', 64))
            ->assertRedirect(route('login'));

        $staff = User::factory()->create(['role' => 'petugas']);
        $this->actingAs($staff)
            ->get('/petugas/scan/transaksi/'.str_repeat('a', 64))
            ->assertNotFound();
    }

    private function createAlat(array $attributes = []): Alat
    {
        $kategori = Kategori::create([
            'nama_kategori' => 'Kategori '.uniqid(),
            'slug' => 'kategori-'.uniqid(),
        ]);

        return Alat::create(array_merge([
            'kode_alat' => 'ALT-'.uniqid(),
            'nama_alat' => 'Proyektor '.uniqid(),
            'slug' => 'proyektor-'.uniqid(),
            'kategori_id' => $kategori->id,
            'stok' => 1,
            'stok_tersedia' => 1,
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'harga_sewa_per_hari' => 10000,
            'denda_per_hari' => 5000,
        ], $attributes));
    }

    private function createLoan(User $user, Alat $alat): Peminjaman
    {
        return Peminjaman::create([
            'kode_peminjaman' => 'PJM-'.uniqid(),
            'user_id' => $user->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(3)->toDateString(),
            'status' => 'menunggu',
        ]);
    }
}
