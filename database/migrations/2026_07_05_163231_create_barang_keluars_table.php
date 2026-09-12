<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluars', function (Blueprint $table) {

            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->cascadeOnDelete();

            $table->string('kode_transaksi')->unique();

            $table->date('tanggal_keluar');

            $table->integer('jumlah');

            $table->enum('jenis_keluar', [
                'Transfer Ke Toko',
                'Penjualan',
                'Rusak',
                'Retur',
                'Pemakaian Internal',
                'Kadaluarsa'
            ]);

            $table->string('tujuan')->nullable();

            $table->text('keterangan')->nullable();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluars');
    }
};