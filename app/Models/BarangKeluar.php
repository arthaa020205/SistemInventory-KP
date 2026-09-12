<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'barang_id',
        'pelanggan_id',
        'tanggal_keluar',

        // Jumlah transaksi
        'jumlah',
        'satuan_id',
        'nilai_konversi',
        'jumlah_dasar',

        // Penjualan
        'harga_jual',
        'total_harga',

        // Informasi keluar
        'jenis_keluar',
        'tujuan',
        'keterangan',

        'user_id',
    ];

    protected $casts = [
        'tanggal_keluar' => 'date',
        'jumlah' => 'decimal:2',
        'nilai_konversi' => 'decimal:2',
        'jumlah_dasar' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(
            BarangKeluarDetail::class,
            'barang_keluar_id'
        );
    }
    
}