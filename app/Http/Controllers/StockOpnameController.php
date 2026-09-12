<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StockOpname;
use App\Models\StockAdjustment;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $barang = $request->barang;

        $tanggalAwal = $request->tanggal_awal;

        $tanggalAkhir = $request->tanggal_akhir;

        $data = StockOpname::with([
            'barang',
            'user'
        ])
        ->when($search, function ($query) use ($search) {
            $query->where(
                'kode_transaksi',
                'like',
                "%{$search}%"
            );
        })
        ->when($barang, function ($query) use ($barang) {
            $query->where('barang_id', $barang);
        })
        ->when(
            $tanggalAwal && $tanggalAkhir,
            function ($query) use (
                $tanggalAwal,
                $tanggalAkhir
            ) {
                $query->whereBetween(
                    'tanggal_opname',
                    [
                        $tanggalAwal,
                        $tanggalAkhir
                    ]
                );
            }
        )
        ->latest()
        ->paginate(10)
        ->withQueryString();

        $barangList = Barang::where(
            'status',
            'Aktif'
        )
        ->orderBy('nama_barang')
        ->get();

        return view(
            'stock-opname.index',
            compact(
                'data',
                'search',
                'barang',
                'barangList',
                'tanggalAwal',
                'tanggalAkhir'
            )
        );
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barang = Barang::where(
            'status',
            'Aktif'
        )
        ->orderBy('nama_barang')
        ->get();

        $kode = $this->generateKode();

        return view(
            'stock-opname.create',
            compact(
                'barang',
                'kode'
            )
        );
    }


    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            'barang_id'      => 'required|exists:barang,id',
            'tanggal_opname' => 'required|date',
            'stok_fisik'     => 'required|numeric|min:0',
            'keterangan'     => 'nullable|string',
        ]);

        $kodeTransaksi = null;
        $namaBarang = null;
        $selisih = 0;

        DB::transaction(function () use (
            $request,
            &$kodeTransaksi,
            &$namaBarang,
            &$selisih
        ) {

            $barang = Barang::lockForUpdate()
                ->findOrFail(
                    $request->barang_id
                );

            $stokSistem = $barang->stok;

            $stokFisik = $request->stok_fisik;

            $selisih = $stokFisik - $stokSistem;

            $status = $selisih == 0
                ? 'Sesuai'
                : 'Selisih';

            $kodeTransaksi = $this->generateKode();

            $namaBarang = $barang->nama_barang;

            $stockOpname = StockOpname::create([

                'barang_id'       => $barang->id,

                'kode_transaksi'  => $kodeTransaksi,

                'tanggal_opname'  => $request->tanggal_opname,

                'stok_sistem'     => $stokSistem,

                'stok_fisik'      => $stokFisik,

                'selisih'         => $selisih,

                'status'          => $status,

                'keterangan'      => $request->keterangan,

                'user_id'         => Auth::id(),

            ]);

            /*
             * Update stok barang
             */
            $barang->update([
                'stok' => $stokFisik
            ]);

            /*
             * Jika terdapat selisih,
             * buat Stock Adjustment
             */
            if ($selisih != 0) {

                StockAdjustment::create([

                    'kode_transaksi'  =>
                        $this->generateAdjustmentCode(),

                    'barang_id'       =>
                        $barang->id,

                    'stock_opname_id' =>
                        $stockOpname->id,

                    'tanggal' =>
                        $request->tanggal_opname,

                    'stok_sebelum' =>
                        $stokSistem,

                    'stok_sesudah' =>
                        $stokFisik,

                    'selisih' =>
                        $selisih,

                    'keterangan' =>
                        $request->keterangan
                        ?: 'Adjustment otomatis dari Stock Opname',

                    'user_id' =>
                        Auth::id(),

                ]);
            }
        });


        /*
         * LOG AKTIVITAS
         */
        LogAktivitas::create([

            'user_id' =>
                Auth::id(),

            'modul' =>
                'Stock Opname',

            'aktivitas' =>
                'CREATE',

            'deskripsi' =>
                'Menambahkan stock opname '
                . $kodeTransaksi
                . ' untuk barang '
                . $namaBarang
                . ' dengan selisih '
                . $selisih,

            'ip_address' =>
                request()->ip(),

        ]);


        return redirect()
            ->route('stock-opname.index')
            ->with(
                'success',
                'Stock Opname berhasil disimpan.'
            );
    }


    /**
     * Display the specified resource.
     */
    public function show(
        StockOpname $stockOpname
    ) {
        $stockOpname->load([
            'barang',
            'user',
            'adjustment'
        ]);

        return view(
            'stock-opname.show',
            compact('stockOpname')
        );
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(
        StockOpname $stockOpname
    ) {
        return back()->with(
            'warning',
            'Stock Opname tidak dapat diedit.'
        );
    }


    /**
     * Update the specified resource.
     */
    public function update(
        Request $request,
        StockOpname $stockOpname
    ) {
        return back()->with(
            'warning',
            'Stock Opname tidak dapat diubah.'
        );
    }


    /**
     * Remove the specified resource.
     */
    public function destroy(
        StockOpname $stockOpname
    ) {
        return back()->with(
            'warning',
            'Stock Opname tidak dapat dihapus.'
        );
    }


    private function generateKode()
    {
        $last = StockOpname::latest('id')->first();

        if (!$last) {
            return 'SO0001';
        }

        $number = (int) substr(
            $last->kode_transaksi,
            2
        );

        return 'SO'
            . str_pad(
                $number + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
    }


    private function generateAdjustmentCode()
    {
        $last = StockAdjustment::latest('id')->first();

        if (!$last) {
            return 'ADJ0001';
        }

        $number = (int) substr(
            $last->kode_transaksi,
            3
        );

        return 'ADJ'
            . str_pad(
                $number + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
    }
}