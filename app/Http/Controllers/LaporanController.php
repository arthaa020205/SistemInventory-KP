<?php

namespace App\Http\Controllers;

use App\Exports\BarangMasukExport;
use App\Exports\BarangKeluarExport;
use App\Exports\StockOpnameExport;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\StockOpname;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Pelanggan;
use App\Exports\StokExport;


class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    /*
|--------------------------------------------------------------------------
| LAPORAN STOK
|--------------------------------------------------------------------------
*/

    public function stok(Request $request)
    {
        $search = $request->search;
        $kategori_id = $request->kategori_id;
        $status = $request->status;

        $query = Barang::with([
            'kategori',
            'satuan'
        ]);

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where(
                    'kode_barang',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'nama_barang',
                    'like',
                    "%{$search}%"
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER KATEGORI
        |--------------------------------------------------------------------------
        */

        if ($kategori_id) {
            $query->where(
                'kategori_id',
                $kategori_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS STOK
        |--------------------------------------------------------------------------
        */

        if ($status === 'Habis') {

            $query->where(
                'stok',
                0
            );

        } elseif ($status === 'Menipis') {

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

        } elseif ($status === 'Aman') {

            $query->whereColumn(
                'stok',
                '>',
                'stok_minimum'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        $data = $query
            ->orderBy('nama_barang')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | DATA FILTER
        |--------------------------------------------------------------------------
        */

        $kategori = \App\Models\Kategori::orderBy(
            'nama_kategori'
        )->get();

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        $totalBarang = (clone $query)->count();

        $totalStok = (clone $query)->sum('stok');

        $totalHabis = (clone $query)
            ->where('stok', 0)
            ->count();

        $totalMenipis = (clone $query)
            ->whereColumn(
                'stok',
                '<=',
                'stok_minimum'
            )
            ->where(
                'stok',
                '>',
                0
            )
            ->count();

        return view(
            'laporan.stok',
            compact(
                'data',
                'kategori',
                'search',
                'kategori_id',
                'status',
                'totalBarang',
                'totalStok',
                'totalHabis',
                'totalMenipis'
            )
        );
    }

        /*
    |--------------------------------------------------------------------------
    | EXPORT LAPORAN STOK
    |--------------------------------------------------------------------------
    */

    public function stokExcel(Request $request)
    {
        return Excel::download(
            new StokExport(
                $request->search,
                $request->kategori_id,
                $request->status
            ),
            'laporan-stok.xlsx'
        );
    }


    public function stokPdf(Request $request)
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

        if ($request->search) {

            $query->where(function ($q) use ($request) {

                $q->where(
                    'kode_barang',
                    'like',
                    "%{$request->search}%"
                )
                ->orWhere(
                    'nama_barang',
                    'like',
                    "%{$request->search}%"
                );

            });

        }


        /*
        |--------------------------------------------------------------------------
        | FILTER KATEGORI
        |--------------------------------------------------------------------------
        */

        if ($request->kategori_id) {

            $query->where(
                'kategori_id',
                $request->kategori_id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FILTER STATUS
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'Habis') {

            $query->where(
                'stok',
                0
            );

        } elseif ($request->status === 'Menipis') {

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

        } elseif ($request->status === 'Aman') {

            $query->whereColumn(
                'stok',
                '>',
                'stok_minimum'
            );

        }


        $data = $query
            ->orderBy('nama_barang')
            ->get();


        $totalBarang = $data->count();

        $totalStok = $data->sum('stok');

        $totalHabis = $data
            ->where('stok', 0)
            ->count();

        $totalMenipis = $data
            ->filter(function ($item) {
                return $item->stok > 0 &&
                    $item->stok <= $item->stok_minimum;
            })
            ->count();


        $pdf = Pdf::loadView(
            'laporan.pdf.stok',
            compact(
                'data',
                'totalBarang',
                'totalStok',
                'totalHabis',
                'totalMenipis'
            )
        );


        $pdf->setPaper(
            'A4',
            'landscape'
        );


        return $pdf->download(
            'laporan-stok.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LAPORAN BARANG MASUK
    |--------------------------------------------------------------------------
    */

    public function barangMasuk(Request $request)
    {
        $search = $request->search;
        $barang_id = $request->barang_id;
        $supplier_id = $request->supplier_id;
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        $query = BarangMasuk::with([
            'barang',
            'supplier',
            'user'
        ]);

        if ($search) {
            $query->where('kode_transaksi', 'like', "%{$search}%");
        }

        if ($barang_id) {
            $query->where('barang_id', $barang_id);
        }

        if ($supplier_id) {
            $query->where('supplier_id', $supplier_id);
        }

        if ($tanggal_awal && $tanggal_akhir) {
            $query->whereBetween('tanggal_masuk', [
                $tanggal_awal,
                $tanggal_akhir
            ]);
        }

        $data = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $barang = Barang::orderBy('nama_barang')->get();

        $supplier = Supplier::orderBy('nama_supplier')->get();

        $totalQty = (clone $query)->sum('jumlah');

        $totalPembelian = (clone $query)
            ->sum(DB::raw('jumlah * harga_beli'));

        return view('laporan.barang-masuk', compact(
            'data',
            'barang',
            'supplier',
            'search',
            'barang_id',
            'supplier_id',
            'tanggal_awal',
            'tanggal_akhir',
            'totalQty',
            'totalPembelian'
        ));
    }

    public function barangKeluar(Request $request)
    {
        $search         = $request->search;
        $barang_id      = $request->barang_id;
        $pelanggan_id   = $request->pelanggan_id;
        $jenis_keluar   = $request->jenis_keluar;
        $tanggal_awal   = $request->tanggal_awal;
        $tanggal_akhir  = $request->tanggal_akhir;

        $query = BarangKeluar::with([
            'barang',
            'pelanggan',
            'user'
        ]);

        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('kode_transaksi', 'like', "%{$search}%")
                ->orWhereHas('barang', function ($qq) use ($search) {

                        $qq->where('nama_barang', 'like', "%{$search}%");

                });

            });

        }

        if ($barang_id) {

            $query->where('barang_id', $barang_id);

        }

        if ($pelanggan_id) {

            $query->where('pelanggan_id', $pelanggan_id);

        }

        if ($jenis_keluar) {

            $query->where('jenis_keluar', $jenis_keluar);

        }

        if ($tanggal_awal && $tanggal_akhir) {

            $query->whereBetween(
                'tanggal_keluar',
                [
                    $tanggal_awal,
                    $tanggal_akhir
                ]
            );

        }

        $barang = Barang::orderBy('nama_barang')->get();

        $pelanggan = Pelanggan::orderBy('nama_pelanggan')->get();

        $totalQty = (clone $query)->sum('jumlah');

        $totalPenjualan = (clone $query)
            ->where('jenis_keluar', 'Penjualan')
            ->sum('total_harga');

        $data = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'laporan.barang-keluar',
            compact(
                'data',
                'barang',
                'pelanggan',
                'search',
                'barang_id',
                'pelanggan_id',
                'jenis_keluar',
                'tanggal_awal',
                'tanggal_akhir',
                'totalQty',
                'totalPenjualan'
            )
        );
    }

    public function stockOpname(Request $request)
    {
        $barang_id = $request->barang_id;
        $status = $request->status;
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;

        $query = StockOpname::with([
            'barang',
            'user'
        ]);

        if ($barang_id) {

            $query->where('barang_id', $barang_id);

        }

        if ($status) {

            $query->where('status', $status);

        }

        if ($tanggal_awal && $tanggal_akhir) {

            $query->whereBetween('tanggal_opname', [
                $tanggal_awal,
                $tanggal_akhir
            ]);

        }

        $data = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $barang = Barang::orderBy('nama_barang')->get();

        $totalOpname = (clone $query)->count();

        $totalSelisih = (clone $query)
            ->where('status','Selisih')
            ->count();

        return view(
            'laporan.stock-opname',
            compact(
                'data',
                'barang',
                'barang_id',
                'status',
                'tanggal_awal',
                'tanggal_akhir',
                'totalOpname',
                'totalSelisih'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */

    public function barangMasukExcel(Request $request)
    {
        return Excel::download(
            new BarangMasukExport(
                $request->search,
                $request->barang_id,
                $request->supplier_id,
                $request->tanggal_awal,
                $request->tanggal_akhir
            ),
            'laporan-barang-masuk.xlsx'
        );
    }

    public function barangKeluarExcel(Request $request)
    {
        return Excel::download(

            new BarangKeluarExport(

                $request->search,

                $request->barang_id,

                $request->jenis_keluar,

                $request->tanggal_awal,

                $request->tanggal_akhir

            ),

            'laporan-barang-keluar.xlsx'

        );
    }

    public function stockOpnameExcel(Request $request)
    {
        return Excel::download(

            new StockOpnameExport(

                $request->barang_id,

                $request->status,

                $request->tanggal_awal,

                $request->tanggal_akhir

            ),

            'laporan-stock-opname.xlsx'

        );
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT PDF
    |--------------------------------------------------------------------------
    */

    public function barangMasukPdf(Request $request)
    {
        $query = BarangMasuk::with([
            'barang',
            'supplier',
            'user'
        ]);

        if ($request->search) {
            $query->where('kode_transaksi', 'like', "%{$request->search}%");
        }

        if ($request->barang_id) {
            $query->where('barang_id', $request->barang_id);
        }

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->tanggal_awal && $request->tanggal_akhir) {
            $query->whereBetween('tanggal_masuk', [
                $request->tanggal_awal,
                $request->tanggal_akhir
            ]);
        }

        $data = $query
            ->latest()
            ->get();

        $totalQty = $data->sum('jumlah');

        $totalPembelian = $data->sum(function ($item) {
            return $item->jumlah * $item->harga_beli;
        });

        $pdf = Pdf::loadView(
            'laporan.pdf.barang-masuk',
            compact(
                'data',
                'totalQty',
                'totalPembelian'
            )
        );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-barang-masuk.pdf');
    }

    public function barangKeluarPdf(Request $request)
    {
        $query = BarangKeluar::with([
            'barang',
            'user'
        ]);

        if($request->search){

            $query->where(
                'kode_transaksi',
                'like',
                "%{$request->search}%"
            );

        }

        if($request->barang_id){

            $query->where(
                'barang_id',
                $request->barang_id
            );

        }

        if($request->jenis_keluar){

            $query->where(
                'jenis_keluar',
                $request->jenis_keluar
            );

        }

        if($request->tanggal_awal && $request->tanggal_akhir){

            $query->whereBetween(
                'tanggal_keluar',
                [
                    $request->tanggal_awal,
                    $request->tanggal_akhir
                ]
            );

        }

        $data = $query
            ->latest()
            ->get();

        $totalQty = $data->sum('jumlah');

        $totalPenjualan = $data
            ->where('jenis_keluar', 'Penjualan')
            ->sum('total_harga');

        $pdf = Pdf::loadView(

            'laporan.pdf.barang-keluar',

            compact(
                'data',
                'totalQty',
                'totalPenjualan'
            )

        );

        $pdf->setPaper('A4','landscape');

        return $pdf->download(
            'laporan-barang-keluar.pdf'
        );
    }

    public function stockOpnamePdf(Request $request)
    {
        $query = StockOpname::with([
            'barang',
            'user'
        ]);

        if($request->barang_id){

            $query->where('barang_id',$request->barang_id);

        }

        if($request->status){

            $query->where('status',$request->status);

        }

        if($request->tanggal_awal && $request->tanggal_akhir){

            $query->whereBetween(
                'tanggal_opname',
                [
                    $request->tanggal_awal,
                    $request->tanggal_akhir
                ]
            );

        }

        $data = $query->latest()->get();

        $pdf = Pdf::loadView(

            'laporan.pdf.stock-opname',

            [

                'data'=>$data,

                'totalOpname'=>$data->count(),

                'totalSelisih'=>$data
                    ->where('status','Selisih')
                    ->count()

            ]

        );

        return $pdf->stream('laporan-stock-opname.pdf');
    }
}