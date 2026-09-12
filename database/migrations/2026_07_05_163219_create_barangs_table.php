<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {

            $table->id();

            $table->string('kode_barang')->unique();

            $table->string('nama_barang');

            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->cascadeOnDelete();

            $table->foreignId('supplier_id')
                ->constrained('supplier')
                ->cascadeOnDelete();

            $table->foreignId('satuan_id')
                ->constrained('satuan')
                ->cascadeOnDelete();

            $table->decimal('stok', 15, 2)->default(0);
            $table->decimal('stok_minimum', 15, 2)->default(10);

            $table->string('lokasi_rak')->nullable();

            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};