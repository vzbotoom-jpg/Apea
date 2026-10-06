// database/migrations/2026_08_10_100459_create_detail_peminjamans_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')  // <-- PERBAIKAN: tambahkan 'peminjamans'
                ->onDelete('cascade');
            $table->foreignId('alat_id')->constrained('alats')
                ->onDelete('restrict');
            $table->integer('jumlah');
            $table->integer('harga_sewa_saat_pinjam');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjamans');
    }
};