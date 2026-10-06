<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================
// ⏰ APIC — Scheduler Pengingat Jatuh Tempo
// ============================================
// Setiap hari 07:00 → tandai peminjaman terlambat otomatis
// + kirim notifikasi pengingat H-1 dan hari H ke user
Schedule::command('peminjaman:ingatkan')->dailyAt('07:00');