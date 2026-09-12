<?php

namespace App\Helpers;

use App\Models\LogAktivitas;

class ActivityLogger
{
    public static function log(
        string $aktivitas,
        string $modul,
        ?string $deskripsi = null
    ): void {
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => strtoupper($aktivitas),
            'modul' => $modul,
            'deskripsi' => $deskripsi,
            'ip_address' => request()->ip(),
        ]);
    }
}