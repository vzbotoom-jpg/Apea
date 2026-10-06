<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->boolean('perpanjangan_requested')->default(false)->after('status');
            $table->date('tanggal_perpanjangan')->nullable()->after('perpanjangan_requested');
            $table->string('status_perpanjangan')->nullable()->after('tanggal_perpanjangan'); // menunggu|disetujui|ditolak
            $table->text('alasan_perpanjangan')->nullable()->after('status_perpanjangan');
            $table->foreignId('approved_by')->nullable()->after('alasan_perpanjangan')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['perpanjangan_requested', 'tanggal_perpanjangan', 'status_perpanjangan', 'alasan_perpanjangan']);
        });
    }
};