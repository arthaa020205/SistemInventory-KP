<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {

            $table->foreignId('satuan_id')
                ->nullable()
                ->after('jumlah')
                ->constrained('satuan')
                ->nullOnDelete();

            $table->decimal('nilai_konversi', 15, 2)
                ->default(1)
                ->after('satuan_id');

            $table->decimal('jumlah_dasar', 15, 2)
                ->default(0)
                ->after('nilai_konversi');
        });
    }

    public function down(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {

            $table->dropForeign(['satuan_id']);

            $table->dropColumn([
                'satuan_id',
                'nilai_konversi',
                'jumlah_dasar',
            ]);
        });
    }
};