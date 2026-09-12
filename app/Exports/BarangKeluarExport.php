<?php

namespace App\Exports;

use App\Models\BarangKeluar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangKeluarExport implements FromCollection, WithHeadings
{
    protected $search;
    protected $barang;
    protected $jenis;
    protected $awal;
    protected $akhir;

    public function __construct(
        $search,
        $barang,
        $jenis,
        $awal,
        $akhir
    )
    {
        $this->search = $search;
        $this->barang = $barang;
        $this->jenis = $jenis;
        $this->awal = $awal;
        $this->akhir = $akhir;
    }

    public function collection()
    {
        $query = BarangKeluar::with([
            'barang',
            'user'
        ]);

        if ($this->search) {
            $query->where('kode_transaksi','like',"%{$this->search}%");
        }

        if ($this->barang) {
            $query->where('barang_id',$this->barang);
        }

        if ($this->jenis) {
            $query->where('jenis_keluar',$this->jenis);
        }

        if($this->awal && $this->akhir){
            $query->whereBetween(
                'tanggal_keluar',
                [$this->awal,$this->akhir]
            );
        }

        return $query
            ->latest()
            ->get()
            ->map(function($item){

                return [

                    $item->kode_transaksi,

                    $item->tanggal_keluar->format('d-m-Y'),

                    $item->barang->nama_barang,

                    $item->jumlah,

                    $item->jenis_keluar,

                    $item->tujuan,

                    $item->user->name,

                ];

            });
    }

    public function headings(): array
    {
        return [

            'Kode Transaksi',

            'Tanggal',

            'Barang',

            'Jumlah',

            'Jenis Keluar',

            'Tujuan',

            'Petugas'

        ];
    }
}