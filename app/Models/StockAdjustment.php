<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'kode_transaksi',
        'barang_id',
        'stock_opname_id',
        'tanggal',
        'stok_sebelum',
        'stok_sesudah',
        'selisih',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'stok_sebelum' => 'decimal:2',
        'stok_sesudah' => 'decimal:2',
        'selisih' => 'decimal:2',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}