<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE permintaan_pengadaans
            MODIFY status ENUM(
                'Menunggu',
                'Disetujui',
                'Selesai',
                'Ditolak'
            ) NOT NULL DEFAULT 'Menunggu'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE permintaan_pengadaans
            MODIFY status ENUM(
                'Menunggu',
                'Disetujui',
                'Ditolak'
            ) NOT NULL DEFAULT 'Menunggu'
        ");
    }
};
