<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_masuks', function (Blueprint $table) {

            $table->id();

            /*
             * BARANG
             */
            $table->foreignId('barang_id')
                ->references('id')
                ->on('barang')
                ->cascadeOnDelete();


            /*
             * SUPPLIER
             */
            $table->foreignId('supplier_id')
                ->references('id')
                ->on('supplier')
                ->cascadeOnDelete();


            /*
             * KODE TRANSAKSI
             *
             * Contoh:
             * BM0001
             */
            $table->string('kode_transaksi')
                ->unique();


            /*
             * TANGGAL BARANG MASUK
             */
            $table->date('tanggal_masuk');


            /*
             * JUMLAH BARANG
             *
             * Jumlah berdasarkan satuan yang dipilih
             *
             * Contoh:
             * 3 BOX
             */
            $table->integer('jumlah');


            /*
             * SATUAN TRANSAKSI
             *
             * Contoh:
             * BOX
             * PACK
             * PCS
             * DUS
             */
            $table->foreignId('satuan_id')
                ->references('id')
                ->on('satuan')
                ->restrictOnDelete();


            /*
             * NILAI KONVERSI
             *
             * Menyimpan nilai konversi
             * yang berlaku ketika transaksi dibuat.
             *
             * Contoh:
             * 1 BOX = 50 PCS
             *
             * nilai_konversi = 50
             */
            $table->integer('nilai_konversi')
                ->default(1);


            /*
             * JUMLAH DALAM SATUAN DASAR
             *
             * Contoh:
             *
             * 3 BOX
             * × 50 PCS
             * = 150 PCS
             */
            $table->integer('jumlah_dasar');


            /*
             * HARGA BELI
             *
             * Harga berdasarkan satuan transaksi.
             *
             * Contoh:
             * 1 BOX = Rp50.000
             */
            $table->decimal('harga_beli', 12, 2);


            /*
             * TANGGAL KADALUARSA
             *
             * Digunakan terutama untuk
             * bahan kue / barang yang memiliki
             * masa berlaku.
             */
            $table->date('expired_date')
                ->nullable();


            /*
             * NOMOR FAKTUR
             */
            $table->string('nomor_faktur')
                ->nullable();


            /*
             * KETERANGAN
             */
            $table->text('keterangan')
                ->nullable();


            /*
             * USER YANG MENCATAT TRANSAKSI
             */
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();


            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('barang_masuks');
    }
};