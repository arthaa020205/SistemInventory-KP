<?php

namespace App\Exports;

use App\Models\Barang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StokExport implements FromCollection, WithHeadings
{
    protected $search;
    protected $kategori_id;
    protected $status;

    public function __construct(
        $search = null,
        $kategori_id = null,
        $status = null
    ) {
        $this->search = $search;
        $this->kategori_id = $kategori_id;
        $this->status = $status;
    }


    public function collection()
    {
        $query = Barang::with([
            'kategori',
            'satuan'
        ]);


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($this->search) {

            $query->where(function ($q) {

                $q->where(
                    'kode_barang',
                    'like',
                    "%{$this->search}%"
                )
                ->orWhere(
                    'nama_barang',
                    'like',
                    "%{$this->search}%"
                );

            });

        }


        /*
        |--------------------------------------------------------------------------
        | KATEGORI
        |--------------------------------------------------------------------------
        */

        if ($this->kategori_id) {

            $query->where(
                'kategori_id',
                $this->kategori_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | STATUS STOK
        |--------------------------------------------------------------------------
        */

        if ($this->status === 'Habis') {

            $query->where(
                'stok',
                0
            );

        } elseif ($this->status === 'Menipis') {

            $query->whereColumn(
                'stok',
                '<=',
                'stok_minimum'
            )
            ->where(
                'stok',
                '>',
                0
            );

        } elseif ($this->status === 'Aman') {

            $query->whereColumn(
                'stok',
                '>',
                'stok_minimum'
            );

        }


        return $query
            ->orderBy('nama_barang')
            ->get()
            ->map(function ($item, $index) {

                if ($item->stok == 0) {

                    $status = 'Habis';

                } elseif (
                    $item->stok <= $item->stok_minimum
                ) {

                    $status = 'Menipis';

                } else {

                    $status = 'Aman';

                }


                return [

                    'No' => $index + 1,

                    'Kode Barang' =>
                        $item->kode_barang,

                    'Nama Barang' =>
                        $item->nama_barang,

                    'Kategori' =>
                        $item->kategori?->nama_kategori ?? '-',

                    'Satuan' =>
                        $item->satuan?->nama_satuan ?? '-',

                    'Stok' =>
                        $item->stok,

                    'Stok Minimum' =>
                        $item->stok_minimum,

                    'Lokasi Rak' =>
                        $item->lokasi_rak ?? '-',

                    'Status' =>
                        $status,

                ];

            });
    }


    public function headings(): array
    {
        return [

            'No',

            'Kode Barang',

            'Nama Barang',

            'Kategori',

            'Satuan',

            'Stok',

            'Stok Minimum',

            'Lokasi Rak',

            'Status',

        ];
    }
}