<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permintaan_pengadaans', function (Blueprint $table) {

            $table->foreignId('barang_masuk_id')
                ->nullable()
                ->after('approved_at')
                ->constrained('barang_masuks')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('permintaan_pengadaans', function (Blueprint $table) {

            $table->dropForeign(['barang_masuk_id']);

            $table->dropColumn('barang_masuk_id');

        });
    }
};
