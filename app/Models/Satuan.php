<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Satuan extends Model
{
    use HasFactory;

    protected $table = 'satuan';

    protected $fillable = [
        'kode_satuan',
        'nama_satuan',
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class);
    }

    public function konversiSatuan()
    {
        return $this->hasMany(KonversiSatuan::class, 'satuan_id');
    }
}