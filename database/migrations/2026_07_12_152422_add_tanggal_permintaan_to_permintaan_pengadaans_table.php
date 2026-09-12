<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('permintaan_pengadaans', function (Blueprint $table) {

            $table->date('tanggal_permintaan')
                ->after('jumlah');

        });
    }

    public function down()
    {
        Schema::table('permintaan_pengadaans', function (Blueprint $table) {

            $table->dropColumn('tanggal_permintaan');

        });
    }
    };
