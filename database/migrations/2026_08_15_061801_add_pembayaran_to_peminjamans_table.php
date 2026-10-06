<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->string('status_pembayaran')->default('belum_lunas')->after('total_denda');
            $table->string('metode_pembayaran')->nullable()->after('status_pembayaran'); // tunai|transfer
            $table->timestamp('tanggal_bayar')->nullable()->after('metode_pembayaran');
            $table->foreignId('dibayar_oleh')->nullable()->after('tanggal_bayar')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dibayar_oleh');
            $table->dropColumn(['status_pembayaran', 'metode_pembayaran', 'tanggal_bayar']);
        });
    }
};