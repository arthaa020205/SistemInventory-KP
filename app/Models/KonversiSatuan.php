<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KonversiSatuan extends Model
{
    use HasFactory;

    protected $table = 'konversi_satuan';

    protected $fillable = [
        'barang_id',
        'satuan_id',
        'nilai_konversi',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
}