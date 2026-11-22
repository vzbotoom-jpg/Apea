<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('prefix')->unique();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
        });

        foreach ([
            'ALT-' => DB::table('alats')->pluck('kode_alat'),
            'PMJ-' => DB::table('peminjamans')->pluck('kode_peminjaman'),
        ] as $prefix => $codes) {
            $lastNumber = $codes->reduce(function (int $current, string $code) use ($prefix): int {
                if (preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $code, $matches)) {
                    return max($current, (int) $matches[1]);
                }

                return $current;
            }, 0);

            DB::table('code_sequences')->insert([
                'prefix' => $prefix,
                'last_number' => $lastNumber,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('code_sequences');
    }
};
