<?php

namespace App\Exports;

use App\Models\BarangMasuk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarangMasukExport implements FromCollection, WithHeadings
{
    protected $search;
    protected $barang_id;
    protected $supplier_id;
    protected $tanggal_awal;
    protected $tanggal_akhir;

    public function __construct(
        $search,
        $barang_id,
        $supplier_id,
        $tanggal_awal,
        $tanggal_akhir
    ) {
        $this->search = $search;
        $this->barang_id = $barang_id;
        $this->supplier_id = $supplier_id;
        $this->tanggal_awal = $tanggal_awal;
        $this->tanggal_akhir = $tanggal_akhir;
    }

    public function collection()
    {
        $query = BarangMasuk::with([
            'barang',
            'supplier',
            'user'
        ]);

        if ($this->search) {

            $query->where('kode_transaksi', 'like', "%{$this->search}%");

        }

        if ($this->barang_id) {

            $query->where('barang_id', $this->barang_id);

        }

        if ($this->supplier_id) {

            $query->where('supplier_id', $this->supplier_id);

        }

        if ($this->tanggal_awal && $this->tanggal_akhir) {

            $query->whereBetween('tanggal_masuk', [
                $this->tanggal_awal,
                $this->tanggal_akhir
            ]);

        }

        return $query
            ->latest()
            ->get()
            ->map(function ($item) {

                return [

                    'Kode Transaksi' => $item->kode_transaksi,

                    'Tanggal' => $item->tanggal_masuk->format('d-m-Y'),

                    'Barang' => $item->barang->nama_barang,

                    'Supplier' => $item->supplier->nama_supplier,

                    'Jumlah' => $item->jumlah,

                    'Harga Beli' => $item->harga_beli,

                    'Subtotal' => $item->jumlah * $item->harga_beli,

                    'Expired' => $item->expired_date
                        ? $item->expired_date->format('d-m-Y')
                        : '-',

                    'Petugas' => $item->user->name,

                ];

            });
    }

    public function headings(): array
    {
        return [

            'Kode Transaksi',

            'Tanggal',

            'Barang',

            'Supplier',

            'Jumlah',

            'Harga Beli',

            'Subtotal',

            'Expired',

            'Petugas',

        ];
    }
}