<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_alats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')->constrained('alats')->cascadeOnDelete();
            $table->string('jenis');            // masuk | keluar | perbaikan
            $table->integer('jumlah')->default(0);
            $table->text('keterangan')->nullable();
            $table->string('ref_type')->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_alats');
    }
};