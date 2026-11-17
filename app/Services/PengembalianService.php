<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\MutasiAlat;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PengembalianService
{
    public function process(
        Peminjaman $peminjaman,
        User $petugas,
        array $data,
        ?UploadedFile $buktiFoto = null
    ): ?Pengembalian {
        return DB::transaction(function () use ($peminjaman, $petugas, $data, $buktiFoto) {
            $loan = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();

            if (! in_array($loan->status, ['dipinjam', 'terlambat'], true)
                || $loan->pengembalian()->exists()) {
                return null;
            }

            $loan->load(['detailPeminjamans.alat']);
            $denda = (int) ($data['denda'] ?? $loan->calculateDenda());
            $hariTerlambat = $loan->getLateDays();

            if ($loan->status_pembayaran === 'lunas'
                && ! $loan->pembayarans()->where('status', 'disetujui')->exists()) {
                Pembayaran::create([
                    'peminjaman_id' => $loan->id,
                    'user_id' => $loan->user_id,
                    'metode' => $loan->metode_pembayaran ?: 'tunai',
                    'nominal' => $loan->total_bayar,
                    'status' => 'disetujui',
                    'verified_by' => $loan->dibayar_oleh,
                    'verified_at' => $loan->tanggal_bayar ?: now(),
                ]);
            }

            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $loan->id,
                'petugas_id' => $petugas->id,
                'tanggal_kembali' => now(),
                'denda' => $denda,
                'hari_terlambat' => $hariTerlambat,
                'kondisi_alat' => $data['kondisi_alat'],
                'catatan' => $data['catatan'] ?? null,
                'bukti_foto' => $buktiFoto?->store('pengembalian', 'public'),
            ]);

            foreach ($loan->detailPeminjamans as $detail) {
                $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();

                if ($data['kondisi_alat'] === 'hilang') {
                    if ($alat->stok < $detail->jumlah) {
                        throw new \LogicException("Stok alat {$alat->nama_alat} tidak konsisten.");
                    }

                    $alat->stok -= $detail->jumlah;
                    if ($alat->stok_tersedia === 0) {
                        $alat->status = 'tidak_tersedia';
                    }
                    $alat->save();
                    MutasiAlat::catat(
                        $alat,
                        'keluar',
                        $detail->jumlah,
                        "Alat hilang saat pengembalian {$loan->kode_peminjaman}",
                        $loan
                    );

                    continue;
                }

                $alat->addStock($detail->jumlah);
                MutasiAlat::catat(
                    $alat,
                    'masuk',
                    $detail->jumlah,
                    "Pengembalian {$loan->kode_peminjaman}",
                    $loan
                );

                if ($data['kondisi_alat'] === 'rusak_berat') {
                    $alat->status = 'perbaikan';
                    $alat->kondisi = 'rusak_berat';
                } elseif ($data['kondisi_alat'] === 'rusak_ringan') {
                    $alat->kondisi = 'rusak_ringan';
                }

                if ($data['kondisi_alat'] !== 'baik') {
                    $alat->save();
                }
            }

            $loan->update(['status' => 'dikembalikan']);

            if ($denda > 0) {
                Denda::create([
                    'peminjaman_id' => $loan->id,
                    'jumlah_denda' => $denda,
                    'tanggal_denda' => now(),
                    'hari_terlambat' => $hariTerlambat,
                    'status' => 'belum_bayar',
                ]);
            }

            if ($loan->getSisaTagihan() > 0) {
                $loan->update(['status_pembayaran' => 'belum_bayar']);
            }

            $message = "Peminjaman {$loan->kode_peminjaman} telah dikembalikan.";
            if ($denda > 0) {
                $message .= ' Denda: Rp '.number_format($denda, 0, ',', '.').'.';
            }
            if ($data['kondisi_alat'] !== 'baik') {
                $message .= ' Kondisi alat: '.ucfirst(str_replace('_', ' ', $data['kondisi_alat'])).'.';
            }

            Helper::sendNotification(
                $loan->user_id,
                'Pengembalian Selesai',
                $message,
                $denda > 0 ? 'warning' : 'success',
                route('user.peminjaman.show', $loan)
            );

            return $pengembalian;
        });
    }
}
