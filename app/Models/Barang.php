<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KonversiSatuan;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'supplier_id',
        'satuan_id',
        'lokasi_rak',
        'stok',
        'stok_minimum',
        'status',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class);
    }

    public function barangKeluars()
    {
        return $this->hasMany(BarangKeluar::class);
    }

    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function permintaanPengadaans()
    {
        return $this->hasMany(PermintaanPengadaan::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function konversiSatuan()
    {
        return $this->hasMany(KonversiSatuan::class);
    }
    
    public function barangKeluarDetails()
    {
        return $this->hasMany(
            BarangKeluarDetail::class,
            'barang_id'
        );
    }
}