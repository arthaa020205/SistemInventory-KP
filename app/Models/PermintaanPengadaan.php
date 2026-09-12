<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class PermintaanPengadaan extends Model
{
    use HasFactory;

    protected $fillable = [

        'kode_permintaan',

        'barang_id',

        'jumlah',

        'tanggal_permintaan',

        'alasan',

        'status',

        'catatan_owner',

        'user_id',

        'approved_by',

        'approved_at',

    ];

    protected $casts = [

        'tanggal_permintaan' => 'date',

        'approved_at' => 'datetime',

    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}