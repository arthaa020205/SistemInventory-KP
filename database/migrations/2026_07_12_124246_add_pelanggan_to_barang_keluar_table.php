<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {

            $table->foreignId('pelanggan_id')
                ->nullable()
                ->after('barang_id')
                ->constrained('pelanggan')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {

            $table->dropForeign(['pelanggan_id']);

            $table->dropColumn('pelanggan_id');

        });
    }
};