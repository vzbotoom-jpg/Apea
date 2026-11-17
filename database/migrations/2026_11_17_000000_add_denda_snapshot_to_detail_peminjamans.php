<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_peminjamans', function (Blueprint $table) {
            $table->unsignedBigInteger('denda_per_hari_saat_pinjam')->nullable()->after('harga_sewa_saat_pinjam');
        });
    }

    public function down(): void
    {
        Schema::table('detail_peminjamans', function (Blueprint $table) {
            $table->dropColumn('denda_per_hari_saat_pinjam');
        });
    }
};
