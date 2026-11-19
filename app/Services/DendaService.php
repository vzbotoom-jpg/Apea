<?php

namespace App\Services;

use App\Models\Denda;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;

class DendaService
{
    public function synchronizeOverdue(): int
    {
        $synchronized = 0;

        Peminjaman::query()
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereDate('tanggal_jatuh_tempo', '<', today())
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($loans) use (&$synchronized): void {
                foreach ($loans as $loan) {
                    if ($this->synchronizeLoan((int) $loan->id)) {
                        $synchronized++;
                    }
                }
            });

        return $synchronized;
    }

    public function synchronizeLoan(int $loanId): bool
    {
        return DB::transaction(function () use ($loanId): bool {
            $loan = Peminjaman::query()->lockForUpdate()->findOrFail($loanId);
            if (! in_array($loan->status, ['dipinjam', 'terlambat'], true) || ! $loan->isLate()) {
                return false;
            }

            $amount = $loan->calculateDenda();
            if ($amount <= 0) {
                return false;
            }

            $fine = $loan->denda()->first() ?? new Denda([
                'peminjaman_id' => $loan->id,
                'tanggal_denda' => today(),
            ]);
            if ($amount > (int) $fine->jumlah_denda) {
                $this->preserveLegacyPaidAmount($loan);
            }
            $fine->jumlah_denda = max($amount, (int) $fine->jumlah_denda);
            $fine->hari_terlambat = max($loan->getLateDays(), (int) $fine->hari_terlambat);
            $fine->status = $loan->getSisaTagihan() === 0 ? 'lunas' : 'belum_bayar';
            $fine->save();

            return true;
        });
    }

    public function assessOnReturn(Peminjaman $loan, int $amount, int $daysLate): ?Denda
    {
        $fine = $loan->denda()->first();
        $previousAmount = (int) ($fine?->jumlah_denda ?? 0);
        $amount = max($amount, $previousAmount);
        if ($amount <= 0) {
            return null;
        }

        if ($amount > $previousAmount) {
            $this->preserveLegacyPaidAmount($loan);
        }

        $fine ??= new Denda([
            'peminjaman_id' => $loan->id,
            'tanggal_denda' => today(),
        ]);
        $fine->jumlah_denda = $amount;
        $fine->hari_terlambat = max($daysLate, (int) $fine->hari_terlambat);
        $fine->status = $loan->getJumlahTerbayar() >= $loan->detailPeminjamans->sum('subtotal') + $amount
            ? 'lunas'
            : 'belum_bayar';
        $fine->save();

        return $fine;
    }

    private function preserveLegacyPaidAmount(Peminjaman $loan): void
    {
        if ($loan->status_pembayaran !== 'lunas') {
            return;
        }

        $recordedPayments = (int) $loan->pembayarans()
            ->where('status', 'disetujui')
            ->sum('nominal');
        $legacyPaidAmount = $loan->getJumlahTerbayar();
        $missingPayment = $legacyPaidAmount - $recordedPayments;
        if ($missingPayment <= 0) {
            return;
        }

        Pembayaran::create([
            'peminjaman_id' => $loan->id,
            'user_id' => $loan->user_id,
            'metode' => $loan->metode_pembayaran ?: 'tunai',
            'nominal' => $missingPayment,
            'status' => 'disetujui',
            'verified_by' => $loan->dibayar_oleh,
            'verified_at' => $loan->tanggal_bayar ?: now(),
        ]);
    }
}
