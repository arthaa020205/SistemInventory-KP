<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $fillable = [

        'kode_pelanggan',
        'nama_pelanggan',
        'no_hp',
        'alamat',
        'status'

    ];

    public function barangKeluars(): HasMany
    {
        return $this->hasMany(BarangKeluar::class);
    }
}