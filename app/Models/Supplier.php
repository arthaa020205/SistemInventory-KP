<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;

class Supplier extends Model
{
    protected $table = 'supplier';

    protected $fillable = [
        'nama_supplier',
        'pic',
        'telepon',
        'email',
        'alamat',
        'status',
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class);
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }
}