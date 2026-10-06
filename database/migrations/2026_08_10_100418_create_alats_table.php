// database/migrations/2024_01_01_000002_create_alats_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alats', function (Blueprint $table) {
            $table->id();
            $table->string('kode_alat')->unique();
            $table->string('nama_alat');
            $table->string('slug')->unique();
            $table->foreignId('kategori_id')->constrained()->onDelete('restrict');
            $table->text('deskripsi')->nullable();
            $table->integer('stok')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'perbaikan'])->default('baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'perbaikan', 'tidak_tersedia'])->default('tersedia');
            $table->string('gambar')->nullable();
            $table->integer('harga_sewa_per_hari')->default(0);
            $table->integer('denda_per_hari')->default(5000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alats');
    }
};