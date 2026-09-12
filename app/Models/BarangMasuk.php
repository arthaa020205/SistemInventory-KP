<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuks';

    protected $fillable = [
        'barang_id',
        'supplier_id',
        'kode_transaksi',
        'tanggal_masuk',
        'jumlah',
        'satuan_id',
        'nilai_konversi',
        'jumlah_dasar',
        'harga_beli',
        'expired_date',
        'nomor_faktur',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'expired_date' => 'date',
        'harga_beli' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    // Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    // Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Satuan transaksi
    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    // User yang mencatat transaksi
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}