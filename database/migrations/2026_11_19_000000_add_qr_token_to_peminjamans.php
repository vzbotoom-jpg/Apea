<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique();
        });

        DB::table('peminjamans')
            ->whereNull('qr_token')
            ->orderBy('id')
            ->chunkById(100, function ($peminjamans): void {
                foreach ($peminjamans as $peminjaman) {
                    DB::table('peminjamans')
                        ->where('id', $peminjaman->id)
                        ->update(['qr_token' => bin2hex(random_bytes(32))]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropColumn('qr_token');
        });
    }
};
