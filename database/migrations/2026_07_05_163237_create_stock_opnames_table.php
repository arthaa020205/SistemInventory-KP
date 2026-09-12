<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {

            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnDelete();

            $table->string('kode_transaksi')->unique();

            $table->date('tanggal_opname');

            $table->decimal('stok_sistem', 15, 2);

            $table->decimal('stok_fisik', 15, 2);
            
            $table->decimal('selisih', 15, 2);

            $table->enum('status', [

                'Sesuai',

                'Selisih'

            ])->default('Sesuai');

            $table->text('keterangan')->nullable();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};