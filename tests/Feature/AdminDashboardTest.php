<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPeminjaman;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_dashboard_filters_statistics_charts_and_recent_loans(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $borrower = User::factory()->create(['role' => 'user']);
        $cameraCategory = $this->createCategory('Kamera');
        $soundCategory = $this->createCategory('Audio');
        $camera = $this->createAlat($cameraCategory, 'Kamera', 4, 3);
        $speaker = $this->createAlat($soundCategory, 'Speaker', 2, 1);

        $selectedLoan = $this->createPeminjaman($borrower, $camera, 'dipinjam', now()->startOfYear()->addDays(4));
        $otherLoan = $this->createPeminjaman($borrower, $speaker, 'dikembalikan', now()->startOfYear()->addMonth()->addDays(4));
        Pengembalian::create([
            'peminjaman_id' => $otherLoan->id,
            'tanggal_kembali' => now()->startOfYear()->addMonth()->addDays(8)->toDateString(),
            'denda' => 0,
            'hari_terlambat' => 0,
            'kondisi_alat' => 'baik',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard', [
                'bulan' => 1,
                'tahun' => now()->year,
                'compare' => 'yoy',
                'kategori' => $cameraCategory->id,
            ]))
            ->assertOk()
            ->assertSee($cameraCategory->nama_kategori)
            ->assertSee($selectedLoan->kode_peminjaman);

        $this->actingAs($admin)
            ->get(route('admin.dashboard', [
                'bulan' => 1,
                'tahun' => now()->year,
                'compare' => 'yoy',
                'kategori' => $cameraCategory->id,
            ]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('kpis.totalAlat', 4)
            ->assertJsonPath('kpis.alatTersedia', 3)
            ->assertJsonPath('kpis.totalPeminjaman', 1)
            ->assertJsonPath('kpis.peminjamanAktif', 1)
            ->assertJsonPath('kpis.totalUser', 1)
            ->assertJsonPath('charts.tren.labels.1', 'Jan '.now()->year)
            ->assertJsonPath('charts.tren.datasets.0.data.1', 1)
            ->assertJsonPath('charts.sirkulasi.datasets.0.data.1', 1)
            ->assertJsonPath('charts.status.datasets.1.data.1', 1)
            ->assertJsonPath('charts.topAlat.labels.0', $camera->nama_alat)
            ->assertJsonPath('charts.kategori.labels.0', $cameraCategory->nama_kategori)
            ->assertJsonPath('charts.kategori.datasets.0.data.0', 4)
            ->assertJsonPath('recentPeminjaman.0.kode', $selectedLoan->kode_peminjaman);

        $this->actingAs($admin)
            ->get(route('admin.dashboard', [
                'bulan' => 'all',
                'tahun' => now()->year,
                'compare' => 'mom',
                'kategori' => 'all',
            ]), ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('charts.tren.labels.0', 'Jan')
            ->assertJsonPath('charts.tren.datasets.1.label', 'Bulan sebelumnya')
            ->assertJsonPath('charts.sirkulasi.datasets.1.data.1', 1);
    }

    private function createCategory(string $name): Kategori
    {
        return Kategori::create([
            'nama_kategori' => $name,
            'slug' => strtolower($name),
        ]);
    }

    private function createAlat(Kategori $kategori, string $name, int $stock, int $available): Alat
    {
        return Alat::create([
            'kode_alat' => strtoupper(substr($name, 0, 3)).'-'.uniqid(),
            'nama_alat' => $name,
            'slug' => strtolower($name),
            'kategori_id' => $kategori->id,
            'stok' => $stock,
            'stok_tersedia' => $available,
            'kondisi' => 'baik',
            'status' => 'tersedia',
            'harga_sewa_per_hari' => 10000,
            'denda_per_hari' => 5000,
        ]);
    }

    private function createPeminjaman(User $user, Alat $alat, string $status, Carbon $tanggalPinjam): Peminjaman
    {
        $peminjaman = Peminjaman::create([
            'kode_peminjaman' => 'PJM-'.uniqid(),
            'user_id' => $user->id,
            'tanggal_pinjam' => $tanggalPinjam->toDateString(),
            'tanggal_jatuh_tempo' => $tanggalPinjam->copy()->addDays(7)->toDateString(),
            'status' => $status,
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
            'harga_sewa_saat_pinjam' => 10000,
            'subtotal' => 10000,
        ]);

        return $peminjaman;
    }
}
