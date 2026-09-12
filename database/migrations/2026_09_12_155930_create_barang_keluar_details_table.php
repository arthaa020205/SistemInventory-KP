<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluar_details', function (Blueprint $table) {

            $table->id();

            $table->foreignId('barang_keluar_id')
                ->constrained('barang_keluars')
                ->cascadeOnDelete();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->restrictOnDelete();

            $table->foreignId('satuan_id')
                ->constrained('satuan')
                ->restrictOnDelete();

            $table->decimal('jumlah', 15, 2);

            $table->decimal('nilai_konversi', 15, 2)
                ->default(1);

            $table->decimal('jumlah_dasar', 15, 2);

            $table->decimal('harga_jual', 15, 2)
                ->default(0);

            $table->decimal('subtotal', 15, 2)
                ->default(0);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluar_details');
    }
};