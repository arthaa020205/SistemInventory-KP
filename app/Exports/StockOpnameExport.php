<?php

namespace App\Exports;

use App\Models\StockOpname;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockOpnameExport implements FromCollection, WithHeadings
{
    public function __construct(
        public $barang_id = null,
        public $status = null,
        public $tanggal_awal = null,
        public $tanggal_akhir = null
    ) {}

    public function collection()
    {
        $query = StockOpname::with([
            'barang',
            'user'
        ]);

        if ($this->barang_id) {

            $query->where('barang_id', $this->barang_id);

        }

        if ($this->status) {

            $query->where('status', $this->status);

        }

        if ($this->tanggal_awal && $this->tanggal_akhir) {

            $query->whereBetween('tanggal_opname', [
                $this->tanggal_awal,
                $this->tanggal_akhir
            ]);

        }

        return $query->get()->map(function ($item) {

            return [

                $item->kode_transaksi,

                $item->tanggal_opname,

                $item->barang->nama_barang,

                $item->stok_sistem,

                $item->stok_fisik,

                $item->selisih,

                $item->status,

                $item->user->name,

            ];

        });

    }

    public function headings(): array
    {
        return [

            'Kode',

            'Tanggal',

            'Barang',

            'Stok Sistem',

            'Stok Fisik',

            'Selisih',

            'Status',

            'Petugas',

        ];
    }
}