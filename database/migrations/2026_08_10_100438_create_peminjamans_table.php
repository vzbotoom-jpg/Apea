// database/migrations/2024_01_01_000003_create_peminjamans_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjamans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_verifikasi')->nullable();
            $table->date('tanggal_ambil')->nullable();
            $table->enum('status', ['menunggu', 'diverifikasi', 'dipinjam', 'dikembalikan', 'dibatalkan', 'terlambat'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->integer('total_denda')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjamans');
    }
};