<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = [
        'barang_id',
        'kode_transaksi',
        'tanggal_opname',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
        'stok_sistem' => 'decimal:2',
        'stok_fisik' => 'decimal:2',
        'selisih' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adjustment()
    {
        return $this->hasOne(StockAdjustment::class);
    }
}