<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Peminjaman;
use Illuminate\Console\Command;

class SendDueReminders extends Command
{
    protected $signature = 'peminjaman:ingatkan';
    protected $description = 'Tandai peminjaman terlambat & kirim pengingat jatuh tempo (H-1 dan hari H)';

    public function handle(): int
    {
        $today = now()->startOfDay();

        // 1) Auto-status TERLAMBAT + notifikasi
        $terlambat = Peminjaman::where('status', 'dipinjam')
            ->whereDate('tanggal_jatuh_tempo', '<', $today)
            ->get();

        foreach ($terlambat as $p) {
            $p->update(['status' => 'terlambat']);
            $this->notify($p->user_id, 'Peminjaman Terlambat ⚠️',
                "Peminjaman {$p->kode_peminjaman} TERLAMBAT. Segera kembalikan untuk membatasi denda.");
        }

        // 2) Pengingat H-1 dan hari H
        $reminders = Peminjaman::where('status', 'dipinjam')
            ->where(function ($q) use ($today) {
                $q->whereDate('tanggal_jatuh_tempo', $today)
                  ->orWhereDate('tanggal_jatuh_tempo', $today->copy()->addDay());
            })
            ->get();

        foreach ($reminders as $p) {
            $isToday = $p->tanggal_jatuh_tempo->isSameDay($today);
            $this->notify(
                $p->user_id,
                $isToday ? 'Jatuh Tempo Hari Ini' : 'Pengingat H-1 Jatuh Tempo',
                "Peminjaman {$p->kode_peminjaman} jatuh tempo " . ($isToday ? 'HARI INI' : 'BESOK') . ". Jangan lupa dikembalikan tepat waktu."
            );
        }

        $this->info("Selesai → {$terlambat->count()} ditandai terlambat, {$reminders->count()} pengingat terkirim.");
        return self::SUCCESS;
    }

    private function notify(int $userId, string $title, string $message): void
    {
        Notification::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }
}