<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluarDetail extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar_details';

    protected $fillable = [
        'barang_keluar_id',
        'barang_id',
        'satuan_id',
        'jumlah',
        'nilai_konversi',
        'jumlah_dasar',
        'harga_jual',
        'subtotal',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'nilai_konversi' => 'decimal:2',
        'jumlah_dasar' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI KE BARANG KELUAR
    |--------------------------------------------------------------------------
    */

    public function barangKeluar()
    {
        return $this->belongsTo(
            BarangKeluar::class,
            'barang_keluar_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI KE BARANG
    |--------------------------------------------------------------------------
    */

    public function barang()
    {
        return $this->belongsTo(
            Barang::class,
            'barang_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI KE SATUAN
    |--------------------------------------------------------------------------
    */

    public function satuan()
    {
        return $this->belongsTo(
            Satuan::class,
            'satuan_id'
        );
    }
}