<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konversi_satuan', function (Blueprint $table) {

            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnDelete();

            $table->foreignId('satuan_id')
                ->constrained('satuan')
                ->restrictOnDelete();

            $table->integer('nilai_konversi');

            $table->timestamps();

            $table->unique([
                'barang_id',
                'satuan_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konversi_satuan');
    }
};